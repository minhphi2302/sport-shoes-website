<?php

namespace App\Core;

class Auth
{
    public static function check(): bool
    {
        self::initSession();
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        self::initSession();
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        self::initSession();
        return $_SESSION['user_id'] ?? null;
    }

    public static function login(array $user): void
    {
        self::initSession();
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['user_id'];
        
        // Remove password hash from session data for security
        unset($user['password']);
        $_SESSION['user'] = $user;
        $_SESSION['last_activity'] = time();
    }

    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }

        // Check for session timeout (30 minutes)
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > 1800) {
                self::logout();
                return;
            }
        }
        
        if (isset($_SESSION['user_id'])) {
            $_SESSION['last_activity'] = time();
        }
    }
}
