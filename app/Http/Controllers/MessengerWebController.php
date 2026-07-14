<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesSupportThreads;
use App\Models\SupportThread;
use App\Models\User;
use App\Services\SupportChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/** API JSON messagerie support (session web admin / commercial, sans rechargement). */
class MessengerWebController extends Controller
{
    use ManagesSupportThreads;

    private function staff(): User
    {
        $user = auth()->user();
        if (! $user || ! in_array($user->role, ['admin', 'commercial'], true)) {
            abort(403);
        }

        return $user;
    }

    public function show(int $id)
    {
        $staff = $this->staff();
        $thread = SupportThread::with(['user', 'messages'])->find($id);

        if (! $thread || ! SupportChatService::staffCanAccessThread($staff, $thread)) {
            return response()->json(['success' => false, 'message' => 'Conversation introuvable'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => SupportChatService::threadPayloadForStaff($thread),
        ]);
    }

    public function sendMessage(Request $request, int $id)
    {
        $staff = $this->staff();
        $thread = SupportThread::with('user')->find($id);

        if (! $thread || ! SupportChatService::staffCanAccessThread($staff, $thread)) {
            return response()->json(['success' => false, 'message' => 'Conversation introuvable'], 404);
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

        try {
            SupportChatService::addAdminMessage($thread, $staff, $request->input('body'));
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Réponse envoyée',
            'data' => SupportChatService::threadPayloadForStaff($thread->fresh(['user', 'messages'])),
        ]);
    }

    public function close(int $id)
    {
        $staff = $this->staff();
        $thread = $this->findAccessibleThread($staff, $id);
        $thread->update(['status' => 'closed']);
        SupportChatService::broadcastThreadUpdate($thread->fresh());

        return response()->json(['success' => true, 'data' => ['status' => 'closed']]);
    }

    public function reopen(int $id)
    {
        $staff = $this->staff();
        $thread = $this->findAccessibleThread($staff, $id);
        $thread->update(['status' => 'open']);
        SupportChatService::broadcastThreadUpdate($thread->fresh());

        return response()->json(['success' => true, 'data' => ['status' => 'open']]);
    }
}
