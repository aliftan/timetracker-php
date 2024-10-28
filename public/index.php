<?php
// public/index.php
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load configuration
$config = require_once __DIR__ . '/../config/config.php';

// Set timezone
date_default_timezone_set($config['timezone']);

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

    case 'projects':
        $controller = new ProjectController();
        $controller->index();
        break;

    case 'projects/create':
        $controller = new ProjectController();
        $controller->create();
        break;

    case (preg_match('/^projects\/(\d+)\/edit$/', $request, $matches) ? true : false):
        $controller = new ProjectController();
        $controller->edit($matches[1]);
        break;

        // For task listing within a project
    case (preg_match('/^projects\/(\d+)\/tasks$/', $request, $matches) ? true : false):
        $controller = new TaskController();
        $controller->index($matches[1]);
        break;

        // For task creation
    case (preg_match('/^projects\/(\d+)\/tasks\/create$/', $request, $matches) ? true : false):
        $controller = new TaskController();
        $controller->create($matches[1]);
        break;

        // For task editing - matches both /tasks/1/edit and /projects/1/tasks/1/edit
    case (preg_match('/^(?:projects\/\d+\/)?tasks\/(\d+)\/edit$/', $request, $matches) ? true : false):
        $controller = new TaskController();
        $controller->edit($matches[1]);
        break;

        // For task status updates
    case (preg_match('/^tasks\/(\d+)\/status$/', $request, $matches) ? true : false):
        $controller = new TaskController();
        $controller->updateStatus($matches[1]);
        break;

        // For task deletion
    case (preg_match('/^tasks\/(\d+)\/delete$/', $request, $matches) ? true : false):
        $controller = new TaskController();
        $controller->delete($matches[1]);
        break;

    case (preg_match('/^tasks\/(\d+)\/timer\/start$/', $request, $matches) ? true : false):
        $controller = new TimerController();
        $controller->start($matches[1]);
        break;

    case (preg_match('/^timer\/(\d+)\/stop$/', $request, $matches) ? true : false):
        $controller = new TimerController();
        $controller->stop($matches[1]);
        break;

    case 'timer/current':
        $controller = new TimerController();
        $controller->current();
        break;

    default:
        http_response_code(404);
        require __DIR__ . '/../views/404.php';
        break;
}
