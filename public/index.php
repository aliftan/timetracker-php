<?php
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

// Initialize Router
$router = new Router('/timetracker-php');

// Authentication middleware
function authMiddleware($callback) {
    return function(...$params) use ($callback) {
        if (!Auth::check()) {
            Session::setFlash('error', 'Please login first');
            header('Location: /timetracker-php/login');
            exit;
        }
        return call_user_func_array($callback, $params);
    };
}

// Static/Home Routes
$router->get('', function() {
    if (Auth::check()) {
        header('Location: /timetracker-php/dashboard');
        exit;
    }
    require __DIR__ . '/../views/welcome.php';
});

// Auth Routes
$router->get('login', function() {
    $controller = new UserController();
    $controller->login();
});

$router->post('login', function() {
    $controller = new UserController();
    $controller->login();
});

$router->get('register', function() {
    $controller = new UserController();
    $controller->register();
});

$router->post('register', function() {
    $controller = new UserController();
    $controller->register();
});

$router->get('logout', authMiddleware(function() {
    $controller = new UserController();
    $controller->logout();
}));

// Dashboard Route
$router->get('dashboard', authMiddleware(function() {
    $controller = new DashboardController();
    $controller->index();
}));

// Project Routes
$router->get('projects', authMiddleware(function() {
    $controller = new ProjectController();
    $controller->index();
}));

$router->get('projects/create', authMiddleware(function() {
    $controller = new ProjectController();
    $controller->create();
}));

$router->post('projects/create', authMiddleware(function() {
    $controller = new ProjectController();
    $controller->create();
}));

$router->get('projects/:id/edit', authMiddleware(function($id) {
    $controller = new ProjectController();
    $controller->edit($id);
}));

$router->post('projects/:id/edit', authMiddleware(function($id) {
    $controller = new ProjectController();
    $controller->edit($id);
}));

// Task Routes
$router->get('projects/:id/tasks', authMiddleware(function($id) {
    $controller = new TaskController();
    $controller->index($id);
}));

$router->get('projects/:projectId/tasks/create', authMiddleware(function($projectId) {
    $controller = new TaskController();
    $controller->create($projectId);
}));

$router->post('projects/:projectId/tasks/create', authMiddleware(function($projectId) {
    $controller = new TaskController();
    $controller->create($projectId);
}));

$router->get('projects/:projectId/tasks/:taskId/edit', authMiddleware(function($projectId, $taskId) {
    $controller = new TaskController();
    $controller->edit($taskId, $projectId);
}));

$router->post('projects/:projectId/tasks/:taskId/edit', authMiddleware(function($projectId, $taskId) {
    $controller = new TaskController();
    $controller->edit($taskId, $projectId);
}));

// Timer Routes
$router->post('tasks/:id/timer/start', authMiddleware(function($id) {
    $controller = new TimerController();
    $controller->start($id);
}));

$router->post('timer/:id/stop', authMiddleware(function($id) {
    $controller = new TimerController();
    $controller->stop($id);
}));

$router->get('timer/current', authMiddleware(function() {
    $controller = new TimerController();
    $controller->current();
}));

// 404 Handler
$router->setNotFound(function() {
    http_response_code(404);
    require __DIR__ . '/../views/404.php';
});

// Dispatch the route
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);