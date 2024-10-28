<?php
class Auth {
    public static function check() {
        return isset($_SESSION['user_id']);
    }
    
    public static function user() {
        if (self::check()) {
            $user = new User();
            return $user->find($_SESSION['user_id']);
        }
        return null;
    }
    
    public static function login($email, $password) {
        $user = new User();
        $found = $user->findByEmail($email);
        
        if ($found && password_verify($password, $found['password'])) {
            $_SESSION['user_id'] = $found['id'];
            return true;
        }
        return false;
    }
    
    public static function logout() {
        unset($_SESSION['user_id']);
        session_destroy();
    }
    
    public static function require_login() {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }
    }
}