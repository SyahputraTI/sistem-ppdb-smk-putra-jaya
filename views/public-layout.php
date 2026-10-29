<?php
$ok = flash('ok');
$error = flash('error');
$open = setting('pendaftaran_buka', '1') === '1';
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
<body class="is-public">
<header class="pub-nav">
    <a class="brand" href="?page=beranda">
        <span><img src="assets/logo.png" alt="Foto saya" width="50"></span>
        <div>
            <strong><?= e($schoolName) ?></strong>
            <small>PPDB</small>
        </div>
    </a>
    <nav>
        <a href="?page=beranda">🏠Beranda</a>
        <a href="?page=daftar">📝Daftar</a>
        <a href="?page=status">🔍Cek status</a>
        <a href="?page=login">👨‍💼Admin</a>
    </nav>
</header>
<main class="pub-main">
    <?php if ($ok): ?><div class="alert ok"><?= e($ok) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
    <?php require $view; ?>
</main>
<footer class="pub-foot">
    <span><?= e($schoolName) ?> · <?= e(setting('school_address')) ?></span>
    <span>WA panitia: <?= e(setting('school_phone')) ?></span>
</footer>
<script src="/assets/app.js"></script>
</body>
</html>
