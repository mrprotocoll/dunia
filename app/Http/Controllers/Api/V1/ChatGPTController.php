<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ChatGPTController extends Controller
{
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . env('CHATGPT_API_KEY'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Interact with the GPT-3.5 Turbo model to generate responses.
     *
     * This endpoint allows users to ask questions or provide messages to the Dunia AI and receive responses.
     *
     * @urlParam message required The user's message or question. Example: "Who are you"
     *
     * @response {
     *     "data": "The generated response from the GPT-3.5 Turbo model.",
     *     "status": 200
     * }
     *
     * @response 400 {
     *     "error": "Bad Request",
     *     "message": "The request is missing a 'message' parameter."
     * }
     *
     * @group Dunia AI
     */
    public function askChatGpt(Request $request)
    {
        $response = $this->httpClient->post('chat/completions', [
            'json' => [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'Welcome, ask me about green education'],
                    ['role' => 'user', 'content' => $request->message ?? 'Who are you'],
                ],
            ],
        ]);

        return json_decode($response->getBody(), true)['choices'][0]['message']['content'];
    }
}
