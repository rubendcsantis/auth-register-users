<?php

namespace App\Http\Controllers;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isEmpty;

class HistoryController extends Controller
{
    //
    private static string $URL_TRM = 'https://www.datos.gov.co/resource/32sa-8pi3.json';
    private static string $URL_CHATGPT = 'https://api.openai.com/v1/chat/completions';
    private string $API_KEY_CHATGPT;

    public function __construct()
    {
        $this->API_KEY_CHATGPT = config('services.api.key'); // ✅ Esto sí funciona con `config:cache`
    }

    public function index(Request $request){
        $data = $request->all();
        $query = '';
        if (isset($data['fromDate']) && isset($data['toDate'])) {
            $query = '?$where=vigenciadesde between "'. $data['fromDate'] .'" and "' . $data['toDate'] . '"';
        } elseif (isset($data['fromDate'])) {
            $query = '?vigenciadesde=' . $data['fromDate'] . 'T00:00:00.000';
        } elseif (isset($data['toDate'])) {
            $query = '?vigenciahasta=' . $data['toDate'] . 'T00:00:00.000';
        }

        $response = Http::get(self::$URL_TRM . $query);
        $data = $response->json();

        return response()->json($data);
    }

    /**
     * @throws ConnectionException
     */
    public function analizarDatos(Request $request)
    {
        $data = $request->all();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->API_KEY_CHATGPT,
            'Content-Type'  => 'application/json',
        ])->post(self::$URL_CHATGPT, [
            'model' => 'gpt-4', // Usa 'gpt-3.5-turbo' si prefieres
            'messages' => [
                ['role' => 'system', 'content' => $data['message']],
                ['role' => 'user', 'content' => substr(json_encode($data['data']), 0, 8192)],
            ],
            'temperature' => 0.7,
        ]);

        return $response->json()['choices'][0]['message']['content'] ?? 'Error en la respuesta';
    }
}
