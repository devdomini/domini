<?php

namespace App\Services;

use App\Models\Entreprise;
use App\Models\SupportMessage;
use App\Models\SupportThread;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SupportChatService
{
    /** @return Builder<SupportThread> */
    public static function threadsQueryForStaff(User $staff): Builder
    {
        $query = SupportThread::query()->with('user');

        if ($staff->role === 'admin') {
            return $query;
        }

        if ($staff->role === 'commercial') {
            return $query;
        }

        return $query->whereRaw('1 = 0');
    }

    public static function staffCanAccessThread(User $staff, SupportThread $thread): bool
    {
        return self::threadsQueryForStaff($staff)
            ->where('support_threads.id', $thread->id)
            ->exists();
    }

    public static function unreadTotalForStaff(User $staff): int
    {
        return (int) self::threadsQueryForStaff($staff)->sum('unread_admin');
    }

    public static function threadForUser(User $user): SupportThread
    {
        return SupportThread::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => 'open', 'last_message_at' => now()]
        );
    }

    public static function addUserMessage(User $user, string $body): SupportMessage
    {
        $body = trim($body);
        if ($body === '') {
            throw new \InvalidArgumentException('Message vide');
        }

        return DB::transaction(function () use ($user, $body) {
            $thread = self::threadForUser($user);
            if ($thread->status === 'closed') {
                $thread->update(['status' => 'open']);
            }

            $message = SupportMessage::create([
                'support_thread_id' => $thread->id,
                'sender' => 'user',
                'sender_user_id' => $user->id,
                'body' => $body,
            ]);

            $thread->update([
                'last_message_at' => $message->created_at,
                'unread_admin' => $thread->unread_admin + 1,
            ]);

            self::broadcastMessage($message->fresh(), $thread->fresh(['user']));

            return $message;
        });
    }

    public static function addAdminMessage(SupportThread $thread, User $staff, string $body): SupportMessage
    {
        $body = trim($body);
        if ($body === '') {
            throw new \InvalidArgumentException('Message vide');
        }

        return DB::transaction(function () use ($thread, $staff, $body) {
            $message = SupportMessage::create([
                'support_thread_id' => $thread->id,
                'sender' => 'admin',
                'sender_user_id' => $staff->id,
                'body' => $body,
            ]);

            $thread->update([
                'status' => 'open',
                'last_message_at' => $message->created_at,
                'unread_user' => $thread->unread_user + 1,
            ]);

            $thread->load('user');
            if ($thread->user && FcmNotificationService::isConfigured()) {
                FcmNotificationService::sendToUser(
                    $thread->user,
                    'Réponse du support Domini',
                    \Illuminate\Support\Str::limit($body, 120),
                    ['type' => 'support_reply', 'thread_id' => (string) $thread->id]
                );
            }

            self::broadcastMessage($message->fresh(), $thread->fresh(['user']));

            return $message;
        });
    }

    public static function markReadByUser(SupportThread $thread): void
    {
        if ($thread->unread_user > 0) {
            $thread->update(['unread_user' => 0]);
        }
    }

    public static function markReadByAdmin(SupportThread $thread): void
    {
        if ($thread->unread_admin > 0) {
            $thread->update(['unread_admin' => 0]);
        }
    }

    /** Diffusion temps réel (Pusher) d’un nouveau message. */
    public static function broadcastMessage(SupportMessage $message, SupportThread $thread): void
    {
        $thread->loadMissing('user');
        $user = $thread->user;

        $payload = [
            'thread_id' => $thread->id,
            'message' => self::messageToArray($message),
            'thread' => [
                'id' => $thread->id,
                'status' => $thread->status,
                'unread_admin' => (int) $thread->unread_admin,
                'unread_user' => (int) $thread->unread_user,
                'last_message_at' => optional($thread->last_message_at)->toISOString(),
            ],
            'user_id' => $thread->user_id,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'telephone' => $user->telephone,
                'role' => $user->role,
                'id_entreprise' => $user->id_entreprise,
            ] : null,
        ];

        PusherService::trigger('support.thread.'.$thread->id, 'support.message', $payload);
        PusherService::trigger('support.user.'.$thread->user_id, 'support.message', $payload);
        PusherService::trigger('support.staff', 'support.message', $payload);
    }

    public static function broadcastThreadUpdate(SupportThread $thread): void
    {
        $thread->loadMissing('user');
        $payload = [
            'thread_id' => $thread->id,
            'thread' => [
                'id' => $thread->id,
                'status' => $thread->status,
                'unread_admin' => (int) $thread->unread_admin,
                'unread_user' => (int) $thread->unread_user,
                'last_message_at' => optional($thread->last_message_at)->toISOString(),
            ],
            'user_id' => $thread->user_id,
        ];
        PusherService::trigger('support.thread.'.$thread->id, 'support.thread.updated', $payload);
        PusherService::trigger('support.staff', 'support.thread.updated', $payload);
    }

    /** Retire les préfixes de test type [livreur], [commercial web], etc. */
    public static function cleanMessageBodyForDisplay(string $body): string
    {
        $clean = preg_replace('/^\[[^\]]+\]\s*/u', '', trim($body));

        return ($clean !== null && $clean !== '') ? $clean : trim($body);
    }

    public static function roleLabel(?string $role): string
    {
        return match ($role) {
            'employe' => 'Employé',
            'livreur' => 'Livreur',
            'admin' => 'Admin Domini',
            'commercial' => 'Commercial Domini',
            default => $role ? ucfirst($role) : 'Client',
        };
    }

    /** @return array<string, mixed> */
    public static function messageToArray(SupportMessage $message): array
    {
        $message->loadMissing('senderUser');
        $senderUser = $message->senderUser;
        $role = $senderUser?->role;
        $isStaff = $message->sender === 'admin';

        return [
            'id' => $message->id,
            'sender' => $message->sender,
            'body' => $message->body,
            'display_body' => self::cleanMessageBodyForDisplay($message->body),
            'sender_name' => $senderUser?->name ?? ($isStaff ? 'Domini' : 'Client'),
            'sender_role' => $role,
            'sender_label' => self::roleLabel($role),
            'created_at' => $message->created_at?->toISOString(),
        ];
    }

    /** @return array<string, mixed> */
    public static function threadPayloadForStaff(SupportThread $thread): array
    {
        self::markReadByAdmin($thread);

        $thread->loadMissing('user');

        $messages = $thread->messages()
            ->with('senderUser')
            ->orderBy('created_at')
            ->limit(200)
            ->get()
            ->map(fn (SupportMessage $m) => self::messageToArray($m))
            ->values()
            ->all();

        $user = $thread->user;

        return [
            'thread' => [
                'id' => $thread->id,
                'status' => $thread->status,
                'unread_admin' => 0,
                'last_message_at' => optional($thread->last_message_at)->toISOString(),
            ],
            'messages' => $messages,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'telephone' => $user->telephone,
                'role' => $user->role,
            ] : null,
        ];
    }

    /** @return array<string, mixed> */
    public static function threadSummary(SupportThread $thread): array
    {
        $thread->loadMissing('user');
        $user = $thread->user;

        return [
            'id' => $thread->id,
            'status' => $thread->status,
            'unread_admin' => (int) $thread->unread_admin,
            'last_message_at' => optional($thread->last_message_at)->toISOString(),
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'telephone' => $user->telephone,
                'role' => $user->role,
            ] : null,
        ];
    }

    /** @return array<string, mixed> */
    public static function threadPayloadForUser(SupportThread $thread, User $user): array
    {
        self::markReadByUser($thread);

        $messages = $thread->messages()
            ->with('senderUser')
            ->orderBy('created_at')
            ->limit(200)
            ->get()
            ->map(fn (SupportMessage $m) => self::messageToArray($m))
            ->values()
            ->all();

        return [
            'thread' => [
                'id' => $thread->id,
                'status' => $thread->status,
                'unread_user' => 0,
                'last_message_at' => optional($thread->last_message_at)->toISOString(),
            ],
            'messages' => $messages,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
            ],
        ];
    }
}
