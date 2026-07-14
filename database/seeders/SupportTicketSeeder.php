<?php

namespace Database\Seeders;

use App\Models\SupportMessage;
use App\Models\SupportThread;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SupportTicketSeeder extends Seeder
{
    /**
     * Conversations support de démo (app mobile → admin / commercial web).
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@domini.com')->first();
        $commercial = User::where('email', 'commercial@domini.com')->first();

        if (! $admin) {
            $this->command?->warn('SupportTicketSeeder : admin@domini.com introuvable — exécutez AdminUserSeeder.');

            return;
        }

        $tickets = [
            [
                'user_email' => 'k.yao@orange.ci',
                'status' => 'open',
                'unread_admin' => 1,
                'unread_user' => 0,
                'messages' => [
                    ['sender' => 'user', 'body' => 'Bonjour, ma commande du midi n’est pas encore arrivée. Pouvez-vous vérifier ?', 'minutes_ago' => 45],
                    ['sender' => 'admin', 'staff_email' => 'admin@domini.com', 'body' => 'Bonjour Kouassi, nous vérifions auprès du livreur. Merci de patienter quelques minutes.', 'minutes_ago' => 30],
                    ['sender' => 'user', 'body' => 'D’accord, merci. Je suis au box A-101.', 'minutes_ago' => 5],
                ],
            ],
            [
                'user_email' => 'f.traore@mtn.ci',
                'status' => 'open',
                'unread_admin' => 1,
                'unread_user' => 0,
                'messages' => [
                    ['sender' => 'user', 'body' => 'Bonjour, je n’arrive pas à valider mon panier depuis ce matin.', 'minutes_ago' => 20],
                ],
            ],
            [
                'user_email' => 'a.diallo@orange.ci',
                'status' => 'open',
                'unread_admin' => 0,
                'unread_user' => 1,
                'messages' => [
                    ['sender' => 'user', 'body' => 'Bonjour, pouvez-vous changer mon adresse de livraison par défaut ?', 'minutes_ago' => 120],
                    [
                        'sender' => 'admin',
                        'staff_email' => $commercial ? 'commercial@domini.com' : 'admin@domini.com',
                        'body' => 'Bonjour Aminata, indiquez la nouvelle adresse dans Profil → Adresse de livraison, ou envoyez-la ici.',
                        'minutes_ago' => 90,
                    ],
                ],
            ],
            [
                'user_email' => 's.bakayoko@domini.ci',
                'status' => 'open',
                'unread_admin' => 0,
                'unread_user' => 0,
                'messages' => [
                    ['sender' => 'user', 'body' => 'Bonjour, quand seront versées les courses de la semaine dernière ?', 'minutes_ago' => 180],
                    ['sender' => 'admin', 'staff_email' => 'admin@domini.com', 'body' => 'Bonjour Souleymane, le paiement est traité chaque mardi. Vous serez crédité demain.', 'minutes_ago' => 150],
                    ['sender' => 'user', 'body' => 'Parfait, merci pour la réponse.', 'minutes_ago' => 140],
                ],
            ],
            [
                'user_email' => 'e.kouadio@sgci.ci',
                'status' => 'closed',
                'unread_admin' => 0,
                'unread_user' => 0,
                'messages' => [
                    ['sender' => 'user', 'body' => 'J’ai reçu le mauvais plat hier.', 'minutes_ago' => 2880],
                    ['sender' => 'admin', 'staff_email' => 'admin@domini.com', 'body' => 'Nous sommes désolés Eric. Un avoir a été crédité sur votre prochaine commande.', 'minutes_ago' => 2820],
                    ['sender' => 'user', 'body' => 'Merci, c’est réglé.', 'minutes_ago' => 2760],
                ],
            ],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($tickets as $ticket) {
            $user = User::where('email', $ticket['user_email'])->first();
            if (! $user) {
                $this->command?->warn("SupportTicketSeeder : utilisateur {$ticket['user_email']} introuvable.");
                continue;
            }

            $thread = SupportThread::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'status' => $ticket['status'],
                    'unread_admin' => 0,
                    'unread_user' => 0,
                    'last_message_at' => now(),
                ]
            );

            if ($thread->messages()->exists()) {
                $skipped++;
                continue;
            }

            $lastAt = null;

            foreach ($ticket['messages'] as $msg) {
                $staff = null;
                if ($msg['sender'] === 'admin') {
                    $staffEmail = $msg['staff_email'] ?? 'admin@domini.com';
                    $staff = User::where('email', $staffEmail)->first() ?? $admin;
                }

                $createdAt = Carbon::now()->subMinutes($msg['minutes_ago']);

                SupportMessage::create([
                    'support_thread_id' => $thread->id,
                    'sender' => $msg['sender'],
                    'sender_user_id' => $msg['sender'] === 'user' ? $user->id : $staff?->id,
                    'body' => $msg['body'],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $lastAt = $createdAt;
            }

            $thread->update([
                'status' => $ticket['status'],
                'unread_admin' => $ticket['unread_admin'],
                'unread_user' => $ticket['unread_user'],
                'last_message_at' => $lastAt ?? now(),
            ]);

            $created++;
        }

        $this->command?->info("Support tickets : {$created} conversation(s) créée(s), {$skipped} déjà présente(s).");
    }
}
