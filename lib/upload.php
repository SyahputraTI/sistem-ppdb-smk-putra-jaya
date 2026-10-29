<?php

function save_documents(int $registrationId, string $nomor, array $files): void
{
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'application/pdf' => 'pdf',
    ];
    $dir = upload_dir() . DIRECTORY_SEPARATOR . preg_replace('/[^A-Z0-9]/', '', $nomor);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $ins = db()->prepare(
        'INSERT INTO documents (registration_id, doc_type, original_name, stored_name, mime, size_bytes, created_at)
         VALUES (?,?,?,?,?,?,?)'
    );

    foreach (document_types() as $type => $_label) {
        $info = $files[$type];
        $mime = $finfo->file($info['tmp_name']) ?: 'application/octet-stream';
        $ext = $allowed[$mime] ?? 'bin';
        $stored = $type . '-' . bin2hex(random_bytes(6)) . '.' . $ext;
        $dest = $dir . DIRECTORY_SEPARATOR . $stored;
        if (!move_uploaded_file($info['tmp_name'], $dest)) {
            throw new RuntimeException('Gagal menyimpan berkas ' . $type);
        }
        $ins->execute([
            $registrationId,
            $type,
            (string) $info['name'],
            $stored,
            $mime,
            (int) $info['size'],
            date('Y-m-d H:i:s'),
        ]);
    }
}

function document_path(array $reg, array $doc): string
{
    $dir = upload_dir() . DIRECTORY_SEPARATOR . preg_replace('/[^A-Z0-9]/', '', $reg['nomor']);
    return $dir . DIRECTORY_SEPARATOR . $doc['stored_name'];
}
