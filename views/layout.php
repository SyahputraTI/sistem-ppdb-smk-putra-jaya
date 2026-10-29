<?php
$ok = flash('ok');
$error = flash('error');
$nav = [
    'dashboard' => ['Dashboard rekap', '📊'],
    'pendaftar' => ['Pendaftar', '👥'],
    'notifikasi' => ['Notifikasi', '🔔'],
    'pengaturan' => ['Pengaturan', '⚙️'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> · <?= e($schoolName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,560;9..144,700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/sistem-sekolah/assets/style.css">
</head>
<body class="<?= $page === 'login' ? 'is-login' : '' ?>">
<?php if ($page === 'login'): ?>
    <?php require $view; ?>
<?php else: ?>
    <aside class="sidebar">
        <div class="brand">
            <span><h1>🏫</h1></span>
            <div>
                <strong><?= e($schoolName) ?></strong>
                <small>Admin PPDB</small>
            </div>
        </div>
        <nav>
            <?php foreach ($nav as $key => [$label, $icon]): ?>
                <a class="<?= $page === $key || ($page === 'detail' && $key === 'pendaftar') ? 'active' : '' ?>" href="?page=<?= e($key) ?>">
                    <span class="nav-ico"><?= e($icon) ?></span>
                    <?= e($label) ?>
                </a>
            <?php endforeach; ?>
            <a href="?page=excel">
                <span class="nav-ico">📥</span>
                Unduh Excel
            </a>
            <a href="?page=beranda">
                <span class="nav-ico">🌐</span>
                Situs publik
            </a>
        </nav>
        <div class="sidebar-foot">
            <div><?= e($user['display_name'] ?? '') ?></div>
            <a href="?page=logout">🚪Keluar</a>
        </div>
    </aside>
    <main class="main">
        <header class="topbar">
            <div>
                <p class="eyebrow">Sistem Pendaftaran Siswa Baru</p>
                <h1><?= e($pageTitle) ?></h1>
            </div>
            <div class="topbar-meta"><?= e(format_date_id(date('Y-m-d'))) ?></div>
        </header>
        <?php if ($ok): ?><div class="alert ok"><?= e($ok) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
        <?php require $view; ?>
    </main>
<?php endif; ?>
<script src="/assets/app.js"></script>
</body>
</html>
