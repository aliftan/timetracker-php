<?php
class BaseController {
    protected function view($view, $data = []) {
        // Extract data to make it available in view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        require __DIR__ . '/../../views/' . $view . '.php';
        
        // Get the buffered content
        $content = ob_get_clean();
        
        // Include the layout with the content
        require __DIR__ . '/../../views/layouts/app.php';
    }

    protected function redirect($path) {
        $basePath = '/timetracker-php';
        header("Location: {$basePath}{$path}");
        exit;
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}