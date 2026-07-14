<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportThread;
use App\Models\User;
use App\Services\SupportChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommercialSupportApiController extends Controller
{
    /** @return array{0: User|null, 1: \Illuminate\Http\JsonResponse|null} */
    private function commercial(Request $request): array
    {
        $user = $request->user();
        if (! $user || $user->role !== 'commercial') {
            return [null, response()->json([
                'success' => false,
                'message' => 'Accès réservé aux commerciaux',
            ], 403)];
        }

        return [$user, null];
    }

    public function threads(Request $request)
    {
        [$staff, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $items = SupportChatService::threadsQueryForStaff($staff)
            ->orderByDesc('unread_admin')
            ->orderByDesc('last_message_at')
            ->paginate(30);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => collect($items->items())
                    ->map(fn (SupportThread $t) => SupportChatService::threadSummary($t))
                    ->values()
                    ->all(),
                'unread_total' => SupportChatService::unreadTotalForStaff($staff),
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                ],
            ],
        ]);
    }

    public function show(Request $request, int $id)
    {
        [$staff, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $thread = SupportThread::with('user')->find($id);
        if (! $thread || ! SupportChatService::staffCanAccessThread($staff, $thread)) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation introuvable',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => SupportChatService::threadPayloadForStaff($thread),
        ]);
    }

    public function sendMessage(Request $request, int $id)
    {
        [$staff, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $validator = Validator::make($request->all(), [
            'body' => 'required|string|min:1|max:4000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors(),
            ], 422);
        }

        $thread = SupportThread::with('user')->find($id);
        if (! $thread || ! SupportChatService::staffCanAccessThread($staff, $thread)) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation introuvable',
            ], 404);
        }

        try {
            SupportChatService::addAdminMessage($thread, $staff, $request->input('body'));
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Réponse envoyée',
            'data' => SupportChatService::threadPayloadForStaff($thread->fresh()),
        ]);
    }

    public function close(Request $request, int $id)
    {
        [$staff, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $thread = SupportThread::find($id);
        if (! $thread || ! SupportChatService::staffCanAccessThread($staff, $thread)) {
            return response()->json(['success' => false, 'message' => 'Conversation introuvable'], 404);
        }

        $thread->update(['status' => 'closed']);

        return response()->json([
            'success' => true,
            'message' => 'Conversation fermée',
            'data' => SupportChatService::threadPayloadForStaff($thread->fresh()),
        ]);
    }

    public function reopen(Request $request, int $id)
    {
        [$staff, $err] = $this->commercial($request);
        if ($err) {
            return $err;
        }

        $thread = SupportThread::find($id);
        if (! $thread || ! SupportChatService::staffCanAccessThread($staff, $thread)) {
            return response()->json(['success' => false, 'message' => 'Conversation introuvable'], 404);
        }

        $thread->update(['status' => 'open']);

        return response()->json([
            'success' => true,
            'message' => 'Conversation rouverte',
            'data' => SupportChatService::threadPayloadForStaff($thread->fresh()),
        ]);
    }
}
