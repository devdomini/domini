{{-- Interface messagerie — temps réel Pusher + AJAX sans rechargement --}}

@php

    $activeId = $activeThread?->id;

    $accent = $accent ?? '#FF0000';

    $staffBubble = $staffBubble ?? '#111111';

    $initialMessages = $activeThread

        ? $activeThread->messages->map(fn ($m) => \App\Services\SupportChatService::messageToArray($m))->values()->all()

        : [];

@endphp



<style>

    .msg-app { display:flex; height:calc(100vh - 140px); min-height:520px; background:#fff; border:1px solid #e8e8e8; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.06); }

    .msg-sidebar { width:340px; min-width:280px; border-right:1px solid #eee; display:flex; flex-direction:column; background:#fafafa; }

    .msg-sidebar-head { padding:1rem 1.1rem; border-bottom:1px solid #eee; background:#fff; }

    .msg-sidebar-head h2 { margin:0; font-size:1.15rem; font-weight:700; }

    .msg-sidebar-head p { margin:0.25rem 0 0; font-size:0.8rem; color:#777; }

    .msg-unread-pill { display:inline-block; margin-top:0.5rem; background:{{ $accent }}; color:#fff; font-size:0.75rem; font-weight:700; padding:0.2rem 0.65rem; border-radius:999px; }

    .msg-thread-list { flex:1; overflow-y:auto; }

    .msg-thread-item { display:flex; gap:0.75rem; padding:0.85rem 1rem; cursor:pointer; border-bottom:1px solid #f0f0f0; transition:background 0.15s; }

    .msg-thread-item:hover { background:#f0f0f0; }

    .msg-thread-item.active { background:#ebebeb; border-left:3px solid {{ $accent }}; }

    .msg-thread-item.unread { background:#fff8f8; }

    .msg-avatar { width:46px; height:46px; border-radius:50%; background:#222; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; flex-shrink:0; }

    .msg-thread-body { flex:1; min-width:0; }

    .msg-thread-top { display:flex; justify-content:space-between; gap:0.5rem; }

    .msg-thread-name { font-weight:600; font-size:0.95rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

    .msg-thread-time { font-size:0.72rem; color:#999; flex-shrink:0; }

    .msg-thread-preview { font-size:0.82rem; color:#666; margin-top:0.2rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

    .msg-thread-meta { display:flex; align-items:center; gap:0.4rem; margin-top:0.25rem; }

    .msg-role-tag { font-size:0.65rem; padding:0.1rem 0.4rem; border-radius:4px; background:#eee; color:#444; }

    .msg-badge { background:{{ $accent }}; color:#fff; font-size:0.7rem; font-weight:700; min-width:20px; height:20px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; padding:0 6px; }

    .msg-main { flex:1; display:flex; flex-direction:column; min-width:0; background:#e8ece9; }

    .msg-main-empty { flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#888; text-align:center; padding:2rem; }

    .msg-main-empty.hidden, .msg-chat-active.hidden { display:none !important; }

    .msg-chat-active { flex:1; display:flex; flex-direction:column; min-height:0; }

    .msg-header { padding:0.85rem 1.25rem; background:#fff; border-bottom:1px solid #eee; display:flex; align-items:center; justify-content:space-between; gap:1rem; }

    .msg-header-user { display:flex; align-items:center; gap:0.75rem; flex:1; min-width:0; }

    .msg-header-user h3 { margin:0; font-size:1rem; font-weight:700; }

    .msg-header-user p { margin:0.15rem 0 0; font-size:0.78rem; color:#777; }

    .msg-btn { padding:0.45rem 0.9rem; border-radius:8px; font-size:0.82rem; font-weight:600; border:1px solid #ddd; background:#fff; cursor:pointer; }

    .msg-body { flex:1; overflow-y:auto; padding:1.25rem; display:flex; flex-direction:column; gap:0.65rem; }

    .msg-bubble-wrap { max-width:72%; display:flex; flex-direction:column; gap:0.2rem; }
    .msg-bubble-wrap.in { align-self:flex-start; }
    .msg-bubble-wrap.out { align-self:flex-end; align-items:flex-end; }

    .msg-bubble-label { font-size:0.72rem; font-weight:600; color:#666; padding:0 0.15rem; }
    .msg-bubble-wrap.out .msg-bubble-label { color:#444; text-align:right; }

    .msg-bubble { padding:0.65rem 0.9rem; border-radius:12px; font-size:0.92rem; line-height:1.45; word-break:break-word; box-shadow:0 1px 2px rgba(0,0,0,0.06); }

    .msg-bubble.in { align-self:flex-start; background:#fff; border-bottom-left-radius:4px; }

    .msg-bubble.out { align-self:flex-end; background:{{ $staffBubble }}; color:#fff; border-bottom-right-radius:4px; }

    .msg-bubble-time { font-size:0.68rem; opacity:0.7; margin-top:0.3rem; text-align:right; }

    .msg-composer { padding:0.85rem 1.25rem; background:#fff; border-top:1px solid #eee; display:flex; gap:0.65rem; align-items:flex-end; }

    .msg-composer textarea { flex:1; min-height:44px; max-height:120px; padding:0.65rem 1rem; border:1px solid #ddd; border-radius:24px; font-family:inherit; font-size:0.95rem; resize:none; }

    .msg-composer textarea:focus { outline:none; border-color:{{ $accent }}; }

    .msg-send { width:44px; height:44px; border-radius:50%; border:none; background:{{ $accent }}; color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; }

    .msg-pagination { padding:0.65rem 1rem; border-top:1px solid #eee; background:#fff; font-size:0.8rem; }

    @media (max-width:900px) { .msg-app { flex-direction:column; height:auto; min-height:70vh; } .msg-sidebar { width:100%; max-height:280px; border-right:none; border-bottom:1px solid #eee; } }

</style>



<div class="msg-app" id="msgApp">

    <aside class="msg-sidebar">

        <div class="msg-sidebar-head">

            <h2>Messagerie</h2>

            <p>{{ $subtitle ?? 'Conversations app mobile' }}</p>

            <span class="msg-unread-pill" id="msgUnreadPill" @if(($unreadTotal ?? 0) <= 0) style="display:none" @endif>

                @if(($unreadTotal ?? 0) > 0){{ $unreadTotal }} non lu(s)@endif

            </span>

        </div>



        <div class="msg-thread-list" id="msgThreadList">

            @forelse($threads as $thread)

                @php

                    $u = $thread->user;

                    $initial = $u?->name ? mb_strtoupper(mb_substr($u->name, 0, 1)) : '?';

                    $last = $thread->latestMessage;

                    $preview = $last
                        ? ($last->sender === 'admin' ? 'Vous : ' : '')
                            . \Illuminate\Support\Str::limit(\App\Services\SupportChatService::cleanMessageBodyForDisplay($last->body), 60)
                        : 'Aucun message';

                @endphp

                <div role="button" tabindex="0" data-thread-id="{{ $thread->id }}"

                     class="msg-thread-item {{ $activeId === $thread->id ? 'active' : '' }} {{ $thread->unread_admin > 0 ? 'unread' : '' }}">

                    <div class="msg-avatar">{{ $initial }}</div>

                    <div class="msg-thread-body">

                        <div class="msg-thread-top">

                            <span class="msg-thread-name">{{ $u?->name ?? 'Utilisateur' }}</span>

                            <span class="msg-thread-time">{{ $thread->last_message_at?->format('H:i') ?? '' }}</span>

                        </div>

                        <div class="msg-thread-preview">{{ $preview }}</div>

                        <div class="msg-thread-meta">

                            <span class="msg-role-tag">{{ \App\Services\SupportChatService::roleLabel($u?->role) }}</span>

                            @if($thread->status === 'closed')<span class="msg-role-tag">Fermée</span>@endif

                            @if($thread->unread_admin > 0)<span class="msg-badge">{{ $thread->unread_admin }}</span>@endif

                        </div>

                    </div>

                </div>

            @empty

                <p style="padding:2rem;text-align:center;color:#999;">Aucune conversation.</p>

            @endforelse

        </div>

        @if($threads->hasPages())<div class="msg-pagination">{{ $threads->links() }}</div>@endif

    </aside>



    <section class="msg-main" id="msgMainPanel">

        <div class="msg-main-empty {{ $activeThread ? 'hidden' : '' }}">

            <h3 style="margin:0 0 0.5rem;">Messagerie support</h3>

            <p style="margin:0;">Sélectionnez une conversation pour répondre en direct.</p>

        </div>

        <div class="msg-chat-active {{ $activeThread ? '' : 'hidden' }}">

            @php $u = $activeThread?->user; @endphp

            <header class="msg-header">

                <div class="msg-header-user">

                    <div class="msg-avatar" id="msgHeaderAvatar">{{ $u?->name ? mb_strtoupper(mb_substr($u->name, 0, 1)) : '?' }}</div>

                    <div>

                        <h3 id="msgHeaderName">{{ $u?->name ?? 'Utilisateur' }}</h3>

                        <p id="msgHeaderMeta">{{ $u ? implode(' · ', array_filter([$u->email, $u->telephone, \App\Services\SupportChatService::roleLabel($u->role)])) : '' }}</p>

                    </div>

                </div>

                <button type="button" class="msg-btn" id="msgStatusBtn" data-action="{{ ($activeThread?->status ?? 'open') === 'closed' ? 'reopen' : 'close' }}">

                    {{ ($activeThread?->status ?? 'open') === 'closed' ? 'Rouvrir' : 'Fermer' }}

                </button>

            </header>

            <div class="msg-body" id="msgBody">

                @if($activeThread)

                    @forelse($activeThread->messages as $msg)
                        @php
                            $msgData = \App\Services\SupportChatService::messageToArray($msg);
                            $isOut = $msg->sender === 'admin';
                        @endphp
                        <div class="msg-bubble-wrap {{ $isOut ? 'out' : 'in' }}" data-message-id="{{ $msg->id }}">
                            <div class="msg-bubble-label">
                                @if($isOut)
                                    Vous · {{ $msgData['sender_label'] }}
                                @else
                                    {{ $msgData['sender_name'] }} · {{ $msgData['sender_label'] }}
                                @endif
                            </div>
                            <div class="msg-bubble {{ $isOut ? 'out' : 'in' }}">
                                {{ $msgData['display_body'] }}
                                <div class="msg-bubble-time">{{ $msg->created_at?->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>

                    @empty

                        <p style="text-align:center;color:#888;margin:auto;">En attente du premier message…</p>

                    @endforelse

                @endif

            </div>

            <form class="msg-composer" id="msgComposerForm">

                <textarea id="msgComposerInput" rows="1" placeholder="Écrivez un message…" required></textarea>

                <button type="submit" class="msg-send" title="Envoyer">➤</button>

            </form>

        </div>

    </section>

</div>



<script>

window.DOMINI_SUPPORT_MESSENGER = {

    csrf: @json(csrf_token()),

    apiBase: @json('/admin/messenger-api'),

    pusherKey: @json(env('PUSHER_APP_KEY')),

    pusherCluster: @json(env('PUSHER_APP_CLUSTER', 'mt1')),

    activeThreadId: @json($activeId),

    accent: @json($accent),

    staffBubble: @json($staffBubble),

    initialMessages: @json($initialMessages),

};

</script>


