<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\GeminiService;
use Illuminate\Support\Facades\DB;

class IotController extends Controller
{
    public function scanRfid(Request $request)
    {
        
        $uid = $request->input('uid'); 
        
        $user = User::where('rfid_uid', $uid)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kartu Tidak Dikenal',
                'command' => 'REJECT' 
            ], 404);
        }

        $logAktif = DB::table('attendance_logs')
                    ->where('user_id', $user->id)
                    ->where('status', 'working')
                    ->first();

        if (!$logAktif) {
            
            DB::table('attendance_logs')->insert([
                'user_id' => $user->id,
                'check_in_time' => now(),
                'status' => 'working',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'user' => $user->name,
                'message' => "Selamat Datang",
                'command' => 'UNLOCK' 
            ]);

        } else {
            
            
            return response()->json([
                'status' => 'pending',
                'user' => $user->name,
                'message' => 'Silakan Lapor Kegiatan',
                'command' => 'REQ_VOICE' 
            ]);
        }
    }

    public function submitReport(Request $request, GeminiService $gemini)
    {
        $uid = $request->input('uid');
        
        
        $user = User::where('rfid_uid', $uid)->first();
        
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        }

        $logId = DB::table('attendance_logs')
                    ->where('user_id', $user->id)
                    ->where('status', 'working')
                    ->orderBy('id', 'desc')
                    ->value('id');

        if (!$logId) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada sesi aktif'], 400);
        }

        $rawText = $request->input('transcription', 'Laporan rutin harian');

        $laporanFormal = $gemini->refineReport($rawText);

        DB::table('attendance_logs')
            ->where('id', $logId)
            ->update([
                'check_out_time' => now(),
                'activity_summary' => $laporanFormal,
                'status' => 'done',
                'updated_at' => now()
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan Tersimpan!',
            'ai_result' => $laporanFormal,
            'command' => 'UNLOCK'
        ]);
    }
}   