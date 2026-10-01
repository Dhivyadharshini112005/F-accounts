<?php
declare(strict_types=1);

session_start();

$config = require __DIR__ . '/config.php';

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/BladeLite.php';
require_once __DIR__ . '/compat.php';
require_once __DIR__ . '/Carbon/Carbon.php';
require_once __DIR__ . '/App/Models/Compat.php';

$db = new Database($config['db']);
Auth::init($db);

$errors = new ValidationErrors($_SESSION['_errors'] ?? []);
unset($_SESSION['_errors']);

if (!isset($_SESSION['_old'])) $_SESSION['_old'] = [];
$oldInput = $_SESSION['_old'];
unset($_SESSION['_old']);
