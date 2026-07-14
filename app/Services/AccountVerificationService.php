<?php

namespace App\Services;

use App\Mail\VerificationCodeMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AccountVerificationService
{
    public function defaultChannelForUser(User $user): string
    {
        $role = $user->role ?? 'employe';

        return config("verification.channels.{$role}", config('verification.channels.default', 'sms'));
    }

    /**
     * @deprecated Utiliser defaultChannelForUser()
     */
    public function channelForUser(User $user): string
    {
        return $this->defaultChannelForUser($user);
    }

    public function allowedChannels(): array
    {
        return config('verification.allowed_channels', ['sms', 'email']);
    }

    public function resolveChannel(User $user, ?string $requestedChannel = null): string
    {
        $allowed = $this->allowedChannels();

        if ($requestedChannel !== null && in_array($requestedChannel, $allowed, true)) {
            if ($requestedChannel === 'email' && empty($user->email)) {
                return 'sms';
            }
            if ($requestedChannel === 'sms' && empty($user->telephone)) {
                return 'email';
            }

            return $requestedChannel;
        }

        return $this->defaultChannelForUser($user);
    }

    public function isVerified(User $user): bool
    {
        return $user->email_verified_at !== null || $user->telephone_verified_at !== null;
    }

    /**
     * @return array{channel: string, sent_to: string, success: bool, message?: string}
     */
    public function issueAndSendCode(User $user, string $purpose = 'verification', ?string $channel = null): array
    {
        $smsService = new OrangeSmsService();
        $code = $smsService->generateVerificationCode();
        $ttl = (int) config('verification.code_ttl_minutes', 10);

        $user->update([
            'code_verification' => $code,
            'code_expires_at' => Carbon::now()->addMinutes($ttl),
        ]);

        $channel = $this->resolveChannel($user, $channel);
        $purposeLabel = $purpose === 'reset' ? 'réinitialisation du mot de passe' : 'vérification du compte';

        if ($channel === 'email') {
            $result = $this->sendEmailCode($user, $code, $purposeLabel, $ttl);
        } else {
            $result = $this->sendSmsCode($user, $code, $purposeLabel);
        }

        return [
            'channel' => $channel,
            'sent_to' => $this->maskDestination($user, $channel),
            'success' => $result['success'],
            'message' => $result['message'] ?? null,
        ];
    }

    public function markVerified(User $user, ?string $channel = null): void
    {
        $channel = $this->resolveChannel($user, $channel);
        $updates = [
            'code_verification' => null,
            'code_expires_at' => null,
        ];

        if ($channel === 'email') {
            $updates['email_verified_at'] = Carbon::now();
        } else {
            $updates['telephone_verified_at'] = Carbon::now();
        }

        $user->update($updates);
    }

    public function findUser(?string $email, ?string $phone): ?User
    {
        if ($email !== null && trim($email) !== '') {
            $user = User::where('email', trim($email))->first();
            if ($user) {
                return $user;
            }
        }

        if ($phone !== null && trim($phone) !== '') {
            return $this->findUserByPhone($phone);
        }

        return null;
    }

    public function findUserByPhone(string $phoneNumber): ?User
    {
        $phone = preg_replace('/[^0-9+]/', '', trim($phoneNumber));
        $formats = [$phone];

        if (str_starts_with($phone, '+')) {
            $formats[] = substr($phone, 1);
        }

        if (str_starts_with($phone, '225') && strlen($phone) >= 12) {
            $formats[] = '+'.$phone;
        }

        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            $localPart = substr($phone, 1);
            $formats[] = '+2250'.$localPart;
            $formats[] = '2250'.$localPart;
        }

        foreach (array_unique($formats) as $format) {
            $user = User::where('telephone', $format)->first();
            if ($user) {
                return $user;
            }
        }

        return null;
    }

    public function validateCode(User $user, string $code): ?string
    {
        if (! $user->code_verification) {
            return 'Aucun code trouvé. Veuillez demander un nouveau code.';
        }

        if ($user->code_expires_at && Carbon::now()->gt($user->code_expires_at)) {
            return 'Le code a expiré. Veuillez demander un nouveau code.';
        }

        if ($user->code_verification !== $code) {
            return 'Code incorrect';
        }

        return null;
    }

    /**
     * @return array{success: bool, message?: string}
     */
    private function sendSmsCode(User $user, string $code, string $purposeLabel): array
    {
        if (empty($user->telephone)) {
            return [
                'success' => false,
                'message' => 'Aucun numéro de téléphone associé à ce compte.',
            ];
        }

        $smsService = new OrangeSmsService();
        $message = "Domini — {$purposeLabel}. Votre code : {$code}. Valide ".config('verification.code_ttl_minutes', 10).' minutes.';

        return $smsService->sendSms($user->telephone, $message);
    }

    /**
     * @return array{success: bool, message?: string}
     */
    private function sendEmailCode(User $user, string $code, string $purposeLabel, int $ttl): array
    {
        if (empty($user->email)) {
            return [
                'success' => false,
                'message' => 'Aucune adresse e-mail associée à ce compte.',
            ];
        }

        try {
            Mail::to($user->email)->send(new VerificationCodeMail(
                userName: $user->name,
                code: $code,
                purposeLabel: $purposeLabel,
                ttlMinutes: $ttl,
            ));

            return ['success' => true];
        } catch (\Throwable $e) {
            Log::error('Envoi e-mail vérification échoué', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'e-mail : '.$e->getMessage(),
            ];
        }
    }

    public function maskDestination(User $user, string $channel): string
    {
        if ($channel === 'email') {
            $email = (string) $user->email;
            if (! str_contains($email, '@')) {
                return $email;
            }
            [$local, $domain] = explode('@', $email, 2);
            $visible = substr($local, 0, min(2, strlen($local)));

            return $visible.'***@'.$domain;
        }

        $phone = preg_replace('/\s+/', '', (string) $user->telephone);
        if (strlen($phone) <= 4) {
            return $phone;
        }

        return substr($phone, 0, 4).'***'.substr($phone, -2);
    }

    public function verificationMeta(User $user, ?string $preferredChannel = null): array
    {
        $channel = $this->resolveChannel($user, $preferredChannel);

        return [
            'verification_channel' => $channel,
            'verification_sent_to' => $this->maskDestination($user, $channel),
            'available_channels' => $this->allowedChannels(),
            'is_verified' => $this->isVerified($user),
        ];
    }
}
