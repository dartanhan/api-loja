<?php


namespace App\Service;
use Illuminate\Support\Facades\Http;


class FacilZapService
{
    protected string $token;

    public function __construct()
    {
        $this->token = config('services.facilzap.token');
    }

    /**
     * Envia um produto para a API do FacilZap
     * @param array $produto
     * @return array
     */
    public function enviarProduto(array $produto): array
    {
        $response = Http::withToken($this->token)
            ->post('https://api.facilzap.app.br/lojista/v1/produtos', $produto);

        return [
            'status' => $response->status(),
            'body' => $response->json(),
            'success' => $response->successful()
        ];
    }


}
