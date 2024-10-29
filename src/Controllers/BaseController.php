<?php
class BaseController
{
    protected function view($view, $data = [])
    {
        ViewHelper::init($data);
        
        // Make data available to both view and layout
        foreach ($data as $key => $value) {
            global $$key;
            $$key = $value;
        }

        // Start output buffering
        ob_start();

        try {
            $viewPath = __DIR__ . '/../../views/' . $view . '.php';
            if (!file_exists($viewPath)) {
                throw new Exception("View file not found: {$view}");
            }

            require $viewPath;

            $content = ob_get_clean();

            $layoutPath = __DIR__ . '/../../views/layouts/app.php';
            if (!file_exists($layoutPath)) {
                throw new Exception("Layout file not found");
            }

            require $layoutPath;
        } catch (Exception $e) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            echo "Error: " . $e->getMessage();
        }
    }

    protected function redirect($path)
    {
        $basePath = '/timetracker-php';
        header("Location: {$basePath}{$path}");
        exit;
    }

    protected function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
