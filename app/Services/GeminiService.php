<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        
        $this->apiKey = config('services.gemini.api_key');

        $model = 'gemini-2.5-flash'; 
        $this->baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
    }

    /**
     * Mengubah teks mentah menjadi laporan formal
     * @param string $rawText
     * @return string
     * @throws Exception
     */
    public function refineReport($rawText)
    {
        if (empty($this->apiKey)) {
            
            throw new Exception("API Key Gemini belum dipasang di konfigurasi.");
        }

        $prompt = "Tugas: Ubah teks berikut menjadi kalimat laporan aktivitas teknis yang lebih formal dan profesional, sesuai dengan gaya Logbook Laboratorium. \n" .
            "Aturan: \n" .
            "1. Hindari format surat, judul, atau penggunaan poin-poin. \n" .
            "2. Jangan menambahkan tanggal atau placeholder (seperti [kurung siku]). \n" .
            "3. Berikan hanya kalimat hasil perbaikan, tanpa tambahan lainnya. \n\n" .
            "Input Kasual: '" . $rawText . "'";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.4, 
                    'maxOutputTokens' => 500, 
                ]
                
                
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $resultText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if (!$resultText) {
                    throw new Exception("Gemini tidak mengembalikan teks (Response kosong).");
                }

                return trim($resultText);
            } 

            
            $errorMessage = $response->json()['error']['message'] ?? $response->body();
            Log::error('Gemini API Error: ' . $errorMessage);
            throw new Exception("Gagal memproses laporan: " . $errorMessage);

        } catch (Exception $e) {
            
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            
            
            throw $e; 
        }
    }
}