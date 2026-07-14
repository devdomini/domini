<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SupportChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportApiController extends Controller
{
    private function ensureMobileUser(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return [null, response()->json(['success' => false, 'message' => 'Non authentifié'], 401)];
        }
        if (! in_array($user->role, ['employe', 'livreur'], true)) {
            return [null, response()->json(['success' => false, 'message' => 'Accès réservé aux clients et livreurs'], 403)];
        }

        return [$user, null];
    }

    public function show(Request $request)
    {
        [$user, $error] = $this->ensureMobileUser($request);
        if ($error) {
            return $error;
        }

        $thread = SupportChatService::threadForUser($user);
        $thread->load('messages');

        return response()->json([
            'success' => true,
            'data' => SupportChatService::threadPayloadForUser($thread, $user),
        ]);
    }

    public function send(Request $request)
    {
        [$user, $error] = $this->ensureMobileUser($request);
        if ($error) {
            return $error;
        }

        $validator = Validator::make($request->all(), [
            'body' => 'required|string|min:1|max:4000',
        ], [
            'body.required' => 'Le message est obligatoire.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $message = SupportChatService::addUserMessage($user, $request->input('body'));
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $thread = $message->thread()->with('messages')->first();

        return response()->json([
            'success' => true,
            'message' => 'Message envoyé',
            'data' => SupportChatService::threadPayloadForUser($thread, $user),
        ], 201);
    }
}
