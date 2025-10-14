<?php

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$parts = explode('/', $path);

if (!isset($_SESSION['user'])) {
    $controllerName = !empty($parts[0]) ? 'Binsta\\Controllers\\' . ucfirst($parts[0]) . 'Controller' : 'Binsta\\Controllers\\AuthController';
} else {
    $controllerName = !empty($parts[0]) ? 'Binsta\\Controllers\\' . ucfirst($parts[0]) . 'Controller' : 'Binsta\\Controllers\\FeedController';
}

if ($controllerName === 'Binsta\Controllers\AuthController') {
    $method = isset($parts[1]) && $parts[1] !== '' ? $parts[1] : 'login';
} elseif ($controllerName === 'Binsta\Controllers\CommentController') {
    $method = isset($parts[1]) && $parts[1] !== '' ? $parts[1] : 'getCommentsBySnippetId';
} elseif ($controllerName === 'Binsta\Controllers\SearchController') {
    $method = isset($parts[1]) && $parts[1] !== '' ? $parts[1] : 'search';
} else {
    $method = isset($parts[1]) && $parts[1] !== '' ? $parts[1] : 'index';
}

$method = str_replace('-', '_', $method);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method .= 'post';
}

$controllerShort = !empty($parts[0]) ? strtolower($parts[0]) : 'feed';
$params = array_slice($parts, 3);

if (!class_exists($controllerName)) {
    error(404, "Controller '$controllerName' not found");
} elseif (class_exists($controllerName)) {
    $controller = new $controllerName();
    if (!method_exists($controller, $method)) {
        error(404, "Method '$method' not found in controller '$controllerName'");
    } elseif (method_exists($controller, $method)) {
        call_user_func_array([$controller, $method], $params);
        exit;
    }
}
