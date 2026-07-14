<?php

namespace App\Http\Controllers\Concerns;

use App\Models\SupportThread;
use App\Models\User;
use App\Services\SupportChatService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

trait ManagesSupportThreads
{
    protected function ensureSupportStaff(array $roles): User
    {
        $user = auth()->user();
        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403);
        }

        return $user;
    }

    protected function findAccessibleThread(User $staff, int $id): SupportThread
    {
        $thread = SupportThread::with(['user', 'messages.senderUser'])->findOrFail($id);

        if (! SupportChatService::staffCanAccessThread($staff, $thread)) {
            abort(403);
        }

        return $thread;
    }

    protected function resolveActiveThread(Request $request, User $staff, LengthAwarePaginator $threads): ?SupportThread
    {
        $conversationId = $request->integer('conversation');
        if (! $conversationId && $threads->isNotEmpty()) {
            $conversationId = $threads->first()->id;
        }

        if (! $conversationId) {
            return null;
        }

        $thread = SupportThread::with(['user', 'messages.senderUser'])->find($conversationId);
        if (! $thread || ! SupportChatService::staffCanAccessThread($staff, $thread)) {
            return null;
        }

        SupportChatService::markReadByAdmin($thread);

        return $thread->fresh(['user', 'messages.senderUser']);
    }
}
