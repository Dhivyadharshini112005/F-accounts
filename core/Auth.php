<?php
declare(strict_types=1);

class Auth {
    private static ?Database $db = null;
    private static ?object $user = null;

    public static function init(Database $db): void {
        self::$db=$db;
        if (!empty($_SESSION['user_id'])) {
            self::$user=$db->first("SELECT * FROM users WHERE id=?", [(int)$_SESSION['user_id']]);
            if (!self::$user) unset($_SESSION['user_id']);
        }
    }
    public static function check(): bool { return self::$user !== null; }
    public static function user(): ?object { return self::$user; }
    public static function login(object $user): void {
        session_regenerate_id(true);
        $_SESSION['user_id']=(int)$user->id;
        self::$user=$user;
    }
    public static function logout(): void {
        self::$user=null; unset($_SESSION['user_id']); session_regenerate_id(true);
    }
    public static function isOwner(): bool {
        return self::check() && strtoupper((string)(self::$user->role ?? '')) === 'OWNER';
    }
}
