<?php
// public/index.php
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load configuration
$config = require_once __DIR__ . '/../config/config.php';

// Autoload classes
spl_autoload_register(function ($class) {
    // Check in Helpers
    $helpers_file = __DIR__ . '/../src/Helpers/' . $class . '.php';
    if (file_exists($helpers_file)) {
        require_once $helpers_file;
        return;
    }

    // Check in Models
    $models_file = __DIR__ . '/../src/Models/' . $class . '.php';
    if (file_exists($models_file)) {
        require_once $models_file;
        return;
    }

    // Check in Controllers
    $controllers_file = __DIR__ . '/../src/Controllers/' . $class . '.php';
    if (file_exists($controllers_file)) {
        require_once $controllers_file;
        return;
    }
});

// Get the request path
$request = $_SERVER['REQUEST_URI'];
$basePath = '/timetracker-php';

// Remove base path and trim slashes
$request = trim(str_replace($basePath, '', $request), '/');

// Routes
switch ($request) {
    case '':
    case 'home':
        if (Auth::check()) {
            header("Location: {$basePath}/dashboard");
            exit;
        } else {
            require __DIR__ . '/../views/welcome.php';
        }
        break;
        
    case 'login':
        $controller = new UserController();
        $controller->login();
        break;
        
    case 'register':
        $controller = new UserController();
        $controller->register();
        break;
        
    case 'logout':
        $controller = new UserController();
        $controller->logout();
        break;
        
    case 'dashboard':
        if (!Auth::check()) {
            header("Location: {$basePath}/login");
            exit;
        }
        require __DIR__ . '/../views/dashboard/index.php';
        break;
        
    default:
        http_response_code(404);
        require __DIR__ . '/../views/404.php';
        break;
}