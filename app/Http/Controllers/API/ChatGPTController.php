<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\AIService;

class ChatGPTController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function chat(Request $request)
    {
        $content = $request->input('content');
        $message = [
            [
                "role" => 'system',
                'content' => 'You are my assistant'
            ],
            [
                "role" => 'user',
                'content' => $content
            ]
        ];
        $response = $this->aiService->chat($message);
        return response()->json($response['choices'][0]['message']['content']);
    }

    public function saveResponse(Request $request)
    {
        $ask_me_anything = $request->post('ask_me_anything');
        $ai_response = $request->post('ai_response');
        $user_id = $request->post('user_id');

        $data = [
            "ask_me_anything" => $ask_me_anything,
            "ai_response" => $ai_response,
        ];
        Post::createPost($user_id, $data);
        return 'Success';
    }

    public function getPost(Request $request)
    {
        $user_id = $request->post('user_id');

        $result = Post::where('user_id', $user_id)->get();
        return response()->json($result);
    }

    public function deletePost(Request $request)
    {
        $id = $request->post('id');

        $result = Post::find($id);
        $result->delete();
        return response()->json($result);
    }
}
