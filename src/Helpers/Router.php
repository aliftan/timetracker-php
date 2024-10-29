<?php
class Router
{
    private $routes = [];
    private $basePath;
    private $notFoundCallback;

    public function __construct($basePath = '')
    {
        $this->basePath = $basePath;
    }

    public function get($path, $callback)
    {
        $this->addRoute('GET', $path, $callback);
        return $this;
    }

    public function post($path, $callback)
    {
        $this->addRoute('POST', $path, $callback);
        return $this;
    }

    private function addRoute($method, $path, $callback)
    {
        $pattern = $this->convertPathToRegex($path);
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'callback' => $callback
        ];
    }

    private function convertPathToRegex($path)
    {
        // Convert parameters like :id to regex pattern
        $pattern = preg_replace('/:[a-zA-Z]+/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    public function setNotFound($callback)
    {
        $this->notFoundCallback = $callback;
    }

    public function dispatch($requestUri, $requestMethod = 'GET')
    {
        // Remove base path and trim slashes
        $path = trim(str_replace($this->basePath, '', $requestUri), '/');

        foreach ($this->routes as $route) {
            if ($requestMethod !== $route['method']) {
                continue;
            }

            if (preg_match($route['pattern'], $path, $matches)) {
                // Remove the full match from the matches array
                array_shift($matches);

                // Execute the callback with the parameters
                return call_user_func_array($route['callback'], $matches);
            }
        }

        // No route found
        if ($this->notFoundCallback) {
            return call_user_func($this->notFoundCallback);
        }

        http_response_code(404);
        echo '404 Not Found';
    }
}
