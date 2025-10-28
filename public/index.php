<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../helpers.php';

use RedBeanPHP\R as R;

$host = '127.0.0.1';
$dbname = 'binsta';
$username = 'bit_academy';
$password = 'bit_academy';
$charset = 'utf8mb4';
R::setup(
    "mysql:host=$host;dbname=$dbname;charset=$charset",
    $username,
    $password
);

session_start();

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$parts = explode('/', $path);

$controllerPart = $parts[0] ?? '';
$controllerName = $controllerPart ? 'Binsta\\Controllers\\' . ucfirst($controllerPart) . 'Controller' : (!isset($_SESSION['user']) ? 'Binsta\\Controllers\\AuthController' : 'Binsta\\Controllers\\FeedController');

if (!class_exists($controllerName)) {
    error(404, "Controller '$controllerName' not found");
}

$controller = new $controllerName();

$defaultMethods = [
    'Binsta\\Controllers\\AuthController' => 'login',
    'Binsta\\Controllers\\CommentController' => 'getCommentsBySnippetId',
    'Binsta\\Controllers\\SearchController' => 'search',
    'Binsta\\Controllers\\UserController' => 'profile',
];

$methodPart = $parts[1] ?? '';
$method = $methodPart ?: ($defaultMethods[$controllerName] ?? 'index');
$method = str_replace('-', '', lcfirst(ucwords($method, '-')));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method .= 'post';
}

$params = array_slice($parts, 2);

if (!method_exists($controller, $method)) {
    error(404, "Method '$method' not found in controller '$controllerName'");
}

call_user_func_array([$controller, $method], $params);
exit;
