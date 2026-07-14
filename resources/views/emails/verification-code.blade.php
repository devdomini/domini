<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Code Domini</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f4;font-family:'Segoe UI',Roboto,Arial,sans-serif;-webkit-font-smoothing:antialiased;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f4f4f4;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:520px;background-color:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e8e8e8;">
                    {{-- En-tête Domini --}}
                    <tr>
                        <td style="background-color:#0a0a0a;padding:28px 32px;text-align:center;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
                                <tr>
                                    <td style="vertical-align:middle;padding-right:12px;">
                                        <img src="{{ $message->embed(public_path('images/domini-email-icon.png')) }}" alt="Domini" width="56" height="56" style="display:block;border-radius:14px;border:2px solid #ff0000;">
                                    </td>
                                    <td style="vertical-align:middle;text-align:left;">
                                        <p style="margin:0;font-size:22px;font-weight:800;color:#ffffff;letter-spacing:1px;line-height:1.2;">DOMINI</p>
                                        <p style="margin:4px 0 0;font-size:12px;color:#ff0000;font-weight:600;letter-spacing:0.5px;">FOOD DELIVERY</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Corps --}}
                    <tr>
                        <td style="padding:36px 32px 28px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center" style="padding-bottom:20px;">
                                        <div style="width:64px;height:64px;background-color:#fff0f0;border-radius:50%;line-height:64px;text-align:center;font-size:28px;">
                                            ✉️
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 8px;font-size:16px;color:#111111;">Bonjour <strong>{{ $userName }}</strong>,</p>
                            <p style="margin:0 0 24px;font-size:15px;color:#444444;line-height:1.6;">
                                Voici votre code de sécurité pour
                                <strong style="color:#000000;">{{ $purposeLabel }}</strong>
                                sur l'application Domini.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center" style="background-color:#0a0a0a;border-radius:12px;padding:24px 16px;border-bottom:4px solid #ff0000;">
                                        <p style="margin:0 0 8px;font-size:11px;color:#aaaaaa;text-transform:uppercase;letter-spacing:2px;font-weight:600;">Votre code</p>
                                        <p style="margin:0;font-size:36px;font-weight:800;color:#ff0000;letter-spacing:8px;font-family:'Courier New',Courier,monospace;">{{ $code }}</p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:24px 0 0;font-size:14px;color:#666666;text-align:center;line-height:1.5;">
                                Ce code expire dans <strong style="color:#000000;">{{ $ttlMinutes }} minutes</strong>.
                            </p>
                        </td>
                    </tr>

                    {{-- Avertissement --}}
                    <tr>
                        <td style="padding:0 32px 32px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#fafafa;border-radius:10px;border-left:4px solid #ff0000;">
                                <tr>
                                    <td style="padding:14px 16px;">
                                        <p style="margin:0;font-size:13px;color:#555555;line-height:1.5;">
                                            Si vous n'êtes pas à l'origine de cette demande, ignorez cet e-mail. Ne partagez jamais ce code.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Pied de page --}}
                    <tr>
                        <td style="background-color:#0a0a0a;padding:20px 32px;text-align:center;">
                            <p style="margin:0 0 6px;font-size:13px;color:#ffffff;font-weight:600;">L'équipe Domini</p>
                            <p style="margin:0;font-size:12px;color:#888888;">
                                <a href="https://domini-food.com" style="color:#ff0000;text-decoration:none;">domini-food.com</a>
                            </p>
                        </td>
                    </tr>
                </table>

                <p style="margin:20px 0 0;font-size:11px;color:#999999;text-align:center;">
                    © {{ date('Y') }} Domini — Tous droits réservés
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
