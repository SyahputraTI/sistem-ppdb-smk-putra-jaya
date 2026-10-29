<?php

declare(strict_types=1);

session_start();

require __DIR__ . '/lib/helpers.php';
require __DIR__ . '/lib/db.php';
require __DIR__ . '/lib/auth.php';
require __DIR__ . '/lib/validation.php';
require __DIR__ . '/lib/upload.php';
require __DIR__ . '/lib/excel.php';
require __DIR__ . '/lib/whatsapp.php';

db();
