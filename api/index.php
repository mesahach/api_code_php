<?php
declare(strict_types=1);
error_reporting(0);
include "bootstrap.php";

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$parts = explode("/", $path);

$resource = $parts[3];

$api_key = $_SERVER['HTTP_X_API_KEY'];
$user_id = null;

$user_gateway = new UserGateway();

$auth = new Auth($user_gateway);

// if (!$auth->authenticateAPIKey()) {
//     exit;
// }

if (!$auth->accountAuth()) {
    exit;
} else {
    $user_id = $auth->getUser_id();
}
// print($user_id);
$controller = new TaskController;

$id = $parts[4] ?? null;

$controller->processRequest($_SERVER['REQUEST_METHOD'], $resource, (int) $user_id, (int) $id);