<?php
class NavigationHelper
{
    public static function isActive($path)
    {
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return strpos($currentPath, $path) !== false ? 'text-blue-600 font-medium' : 'text-gray-600';
    }
}
