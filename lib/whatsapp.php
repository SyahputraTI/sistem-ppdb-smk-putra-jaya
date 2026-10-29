<?php

function wa_message_daftar(array $reg): string
{
    $sekolah = setting('school_name', (string) config('school_name'));
    return "Assalamu'alaikum wr. wb.\n\n"
        . "Pendaftaran *{$sekolah}* berhasil diterima.\n\n"
        . "Nomor pendaftaran: *{$reg['nomor']}*\n"
        . "Nama: {$reg['nama']}\n"
        . "Jurusan: {$reg['jurusan']}\n"
        . "Status: " . status_label($reg['status']) . "\n\n"
        . "Simpan nomor ini untuk cek status. Panitia akan meninjau berkas Anda.\n"
        . "Wassalamu'alaikum wr. wb.";
}

function wa_message_admin(array $reg): string
{
    return "PPDB baru masuk.\n"
        . "No: {$reg['nomor']}\n"
        . "Nama: {$reg['nama']}\n"
        . "Jurusan: {$reg['jurusan']}\n"
        . "WA: {$reg['no_hp']}";
}

function wa_message_status(array $reg): string
{
    $sekolah = setting('school_name', (string) config('school_name'));
    $catatan = trim((string) ($reg['catatan_admin'] ?? ''));
    $extra = $catatan !== '' ? "\nCatatan panitia: {$catatan}" : '';
    return "Assalamu'alaikum wr. wb.\n\n"
        . "Pembaruan status PPDB *{$sekolah}*.\n\n"
        . "Nomor: *{$reg['nomor']}*\n"
        . "Nama: {$reg['nama']}\n"
        . "Status: *" . status_label($reg['status']) . "*{$extra}\n\n"
        . "Wassalamu'alaikum wr. wb.";
}

function notify_log(string $channel, string $target, string $title, string $message, string $status, ?int $regId = null, ?string $response = null): int
{
    db()->prepare(
        'INSERT INTO notifications (registration_id, channel, target, title, message, status, provider_response, created_at, sent_at)
         VALUES (?,?,?,?,?,?,?,?,?)'
    )->execute([
        $regId,
        $channel,
        $target,
        $title,
        $message,
        $status,
        $response,
        date('Y-m-d H:i:s'),
        $status === 'sent' ? date('Y-m-d H:i:s') : null,
    ]);
    return (int) db()->lastInsertId();
}

function send_whatsapp(string $phone, string $message): array
{
    $target = normalize_phone($phone);
    if ($target === '') {
        return ['ok' => false, 'response' => 'Nomor kosong'];
    }
    if (setting('wa_enabled', '0') !== '1' || setting('wa_token') === '') {
        return [
            'ok' => false,
            'response' => 'WhatsApp API belum diaktifkan. Gunakan tautan wa.me.',
            'link' => wa_me_link($target, $message),
        ];
    }

    if (!function_exists('curl_init')) {
        return ['ok' => false, 'response' => 'Ekstensi cURL belum aktif.', 'link' => wa_me_link($target, $message)];
    }
    $payload = [
        'target' => $target,
        'message' => $message,
        'countryCode' => '62',
    ];
    $ch = curl_init('https://api.fonnte.com/send');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['Authorization: ' . setting('wa_token')],
        CURLOPT_TIMEOUT => 20,
    ]);
    $raw = curl_exec($ch);
    $err = curl_error($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($raw === false) {
        return ['ok' => false, 'response' => $err, 'link' => wa_me_link($target, $message)];
    }
    $json = json_decode((string) $raw, true);
    $ok = $code < 400 && (($json['status'] ?? false) === true || ($json['status'] ?? '') === 'true');
    return [
        'ok' => $ok,
        'response' => (string) $raw,
        'link' => wa_me_link($target, $message),
    ];
}

function wa_me_link(string $phone, string $message): string
{
    $target = normalize_phone($phone);
    return 'https://wa.me/' . $target . '?text=' . rawurlencode($message);
}

function dispatch_registration_notifications(array $reg): array
{
    $siswaMsg = wa_message_daftar($reg);
    $adminMsg = wa_message_admin($reg);
    $siswa = send_whatsapp($reg['no_hp'], $siswaMsg);
    notify_log(
        'whatsapp',
        normalize_phone($reg['no_hp']),
        'Konfirmasi pendaftaran ' . $reg['nomor'],
        $siswaMsg,
        ($siswa['ok'] ?? false) ? 'sent' : 'queued',
        (int) $reg['id'],
        (string) (($siswa['response'] ?? '') . (isset($siswa['link']) ? "\n" . $siswa['link'] : ''))
    );

    $adminPhone = setting('wa_admin');
    $admin = send_whatsapp($adminPhone, $adminMsg);
    notify_log(
        'whatsapp',
        normalize_phone($adminPhone),
        'PPDB baru ' . $reg['nomor'],
        $adminMsg,
        ($admin['ok'] ?? false) ? 'sent' : 'queued',
        (int) $reg['id'],
        (string) (($admin['response'] ?? '') . (isset($admin['link']) ? "\n" . $admin['link'] : ''))
    );

    notify_log(
        'sistem',
        $reg['nomor'],
        'Pendaftaran berhasil',
        'Pendaftaran ' . $reg['nama'] . ' tercatat dengan nomor ' . $reg['nomor'] . '. Status: ' . status_label($reg['status']) . '.',
        'sent',
        (int) $reg['id'],
        null
    );

    return [
        'siswa' => $siswa,
        'admin' => $admin,
        'siswa_link' => $siswa['link'] ?? wa_me_link($reg['no_hp'], $siswaMsg),
        'admin_link' => $admin['link'] ?? wa_me_link($adminPhone, $adminMsg),
    ];
}

function dispatch_status_whatsapp(array $reg): array
{
    $msg = wa_message_status($reg);
    $result = send_whatsapp($reg['no_hp'], $msg);
    notify_log(
        'whatsapp',
        normalize_phone($reg['no_hp']),
        'Update status ' . $reg['nomor'],
        $msg,
        ($result['ok'] ?? false) ? 'sent' : 'queued',
        (int) $reg['id'],
        (string) (($result['response'] ?? '') . (isset($result['link']) ? "\n" . $result['link'] : ''))
    );
    return $result;
}
