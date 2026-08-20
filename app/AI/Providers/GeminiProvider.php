<?php

namespace App\AI\Providers;

use App\Models\ConfiguracaoIa;
use GuzzleHttp\Client;

class GeminiProvider
{
    protected $apiKey;
    protected $modelo;

    public function __construct()
    {
        $config = ConfiguracaoIa::where('ativo', true)->where('provedor', 'gemini')->first();
        
        $this->apiKey = $config ? $config->api_key : env('GEMINI_API_KEY');
        $this->modelo = $config ? $config->modelo : 'gemini-2.5-flash';
    }

    public function generateContent(string $prompt): string
    {
        $client = new Client();
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->modelo}:generateContent?key={$this->apiKey}";

        try {
            $response = $client->post($url, [
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ],
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'timeout' => 30
            ]);

            $data = json_decode((string)$response->getBody(), true);
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Não foi possível gerar a resposta.';
        } catch (\Exception $e) {
            return 'Erro ao comunicar com a IA Gemini: ' . $e->getMessage();
        }
    }
}
