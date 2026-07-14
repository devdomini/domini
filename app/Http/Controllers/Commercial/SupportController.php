<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Concerns\ManagesSupportThreads;
use App\Http\Controllers\Controller;
use App\Models\SupportThread;
use App\Services\SupportChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportController extends Controller
{
    use ManagesSupportThreads;

    public function index(Request $request)
    {
        $staff = $this->ensureSupportStaff(['commercial']);

        $threads = SupportChatService::threadsQueryForStaff($staff)
            ->with(['user', 'latestMessage'])
            ->orderByDesc('unread_admin')
            ->orderByDesc('last_message_at')
            ->paginate(25)
            ->withQueryString();

        $unreadTotal = SupportChatService::unreadTotalForStaff($staff);
        $activeThread = $this->resolveActiveThread($request, $staff, $threads);

        return view('commercial.support.index', compact('threads', 'unreadTotal', 'activeThread'));
    }

    public function show(int $id)
    {
        $this->ensureSupportStaff(['commercial']);

        return redirect()->route('commercial.support.index', ['conversation' => $id]);
    }

    public function sendMessage(Request $request, int $id)
    {
        $staff = $this->ensureSupportStaff(['commercial']);
        $thread = SupportThread::with('user')->findOrFail($id);

        if (! SupportChatService::staffCanAccessThread($staff, $thread)) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'body' => 'required|string|min:1|max:4000',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('commercial.support.index', ['conversation' => $id])
                ->withErrors($validator)
                ->withInput();
        }

        try {
            SupportChatService::addAdminMessage($thread, $staff, $request->input('body'));
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('commercial.support.index', ['conversation' => $id])
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('commercial.support.index', ['conversation' => $id])
            ->with('success', 'Réponse envoyée.');
    }

    public function close(int $id)
    {
        $staff = $this->ensureSupportStaff(['commercial']);
        $thread = $this->findAccessibleThread($staff, $id);
        $thread->update(['status' => 'closed']);

        return redirect()
            ->route('commercial.support.index', ['conversation' => $id])
            ->with('success', 'Conversation fermée.');
    }

    public function reopen(int $id)
    {
        $staff = $this->ensureSupportStaff(['commercial']);
        $thread = $this->findAccessibleThread($staff, $id);
        $thread->update(['status' => 'open']);

        return redirect()
            ->route('commercial.support.index', ['conversation' => $id])
            ->with('success', 'Conversation rouverte.');
    }
}
