<?php
abstract class BaseController
{
    protected function view($view, $data = [])
    {
        error_log("Rendering view: {$view}");
        error_log("View data: " . print_r($data, true));

        // Make data available
        foreach ($data as $key => $value) {
            $$key = $value;
        }

        try {
            // Start output buffering
            ob_start();

            // Load the view
            $viewPath = __DIR__ . '/../../views/' . $view . '.php';
            if (!file_exists($viewPath)) {
                throw new Exception("View file not found: {$view}");
            }
            require $viewPath;

            // Get view content
            $content = ob_get_clean();

            // Load the layout with the content
            $layoutPath = __DIR__ . '/../../views/layouts/app.php';
            if (!file_exists($layoutPath)) {
                throw new Exception("Layout file not found");
            }
            require $layoutPath;
        } catch (Exception $e) {
            error_log("View error: " . $e->getMessage());
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            echo "Error: " . $e->getMessage();
        }
    }

    protected function redirect($path)
    {
        $basePath = '/timetracker-php';
        // Add logging to track redirects
        error_log("Redirecting to: {$basePath}{$path}");
        header("Location: {$basePath}{$path}");
        exit;
    }

    protected function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Common utility methods
    protected function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    protected function validateMethod($method = 'POST')
    {
        error_log("Validating method: Expected {$method}, Got {$_SERVER['REQUEST_METHOD']}");
        if ($_SERVER['REQUEST_METHOD'] !== $method) {
            if ($this->isAjaxRequest()) {
                $this->json(['error' => 'Invalid request method']);
            } else {
                Session::setFlash('error', 'Invalid request method');
                // Instead of redirecting to root, return false and let the controller handle it
                return false;
            }
            return false;
        }
        return true;
    }

    protected function requireAuth()
    {
        if (!Auth::check()) {
            if ($this->isAjaxRequest()) {
                $this->json(['error' => 'Unauthorized']);
            } else {
                Session::setFlash('error', 'Please login to continue');
                $this->redirect('/login');
            }
            return false;
        }
        return true;
    }

    protected function validateData($validator, $redirectPath = null, $viewData = [])
    {
        if ($validator->hasErrors()) {
            if ($this->isAjaxRequest()) {
                $this->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->getErrors()
                ]);
                return false;
            }

            Session::setFlash('error', 'Please fix the errors below');

            if ($redirectPath) {
                $this->redirect($redirectPath);
            } else {
                return false;
            }
        }
        return true;
    }

    protected function handleErrorResponse($message, $redirectPath = null)
    {
        if ($this->isAjaxRequest()) {
            $this->json(['error' => $message]);
        } else {
            Session::setFlash('error', $message);
            if ($redirectPath) {
                $this->redirect($redirectPath);
            }
        }
    }

    protected function handleSuccessResponse($message, $redirectPath = null, $data = [])
    {
        if ($this->isAjaxRequest()) {
            $this->json([
                'success' => true,
                'message' => $message,
                'data' => $data
            ]);
        } else {
            Session::setFlash('success', $message);
            if ($redirectPath) {
                $this->redirect($redirectPath);
            }
        }
    }

    protected function validateOwnership($model, $id, $redirectPath = '/')
    {
        $item = $model->find($id);

        if (!$item || $item['user_id'] !== Auth::user()['id']) {
            if ($this->isAjaxRequest()) {
                $this->json(['error' => 'Not found or unauthorized']);
            } else {
                Session::setFlash('error', 'Not found or unauthorized');
                $this->redirect($redirectPath);
            }
            return false;
        }

        return $item;
    }

    protected function getPaginationData($page = 1, $perPage = 10)
    {
        $page = max(1, intval($page));
        return [
            'page' => $page,
            'perPage' => $perPage,
            'offset' => ($page - 1) * $perPage
        ];
    }

    protected function formatValidationErrors($errors)
    {
        $formatted = [];
        foreach ($errors as $field => $message) {
            $formatted[] = [
                'field' => $field,
                'message' => $message
            ];
        }
        return $formatted;
    }

    protected function sanitizeInput($data, $fields)
    {
        $sanitized = [];
        foreach ($fields as $field => $type) {
            if (isset($data[$field])) {
                switch ($type) {
                    case 'string':
                        $sanitized[$field] = trim(htmlspecialchars($data[$field]));
                        break;
                    case 'int':
                        $sanitized[$field] = intval($data[$field]);
                        break;
                    case 'float':
                        $sanitized[$field] = floatval($data[$field]);
                        break;
                    case 'bool':
                        $sanitized[$field] = !empty($data[$field]);
                        break;
                    case 'email':
                        $sanitized[$field] = filter_var($data[$field], FILTER_SANITIZE_EMAIL);
                        break;
                    default:
                        $sanitized[$field] = $data[$field];
                }
            }
        }
        return $sanitized;
    }

    protected function getRequestData($method = 'POST')
    {
        return $method === 'POST' ? $_POST : $_GET;
    }
}
