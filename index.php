<?php
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/controllers/AuthController.php';

$userModel = new User($conn);
$auth = new AuthController($userModel);

$page = $_GET['page'] ?? 'home';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'register') {
    $result = $auth->register($_POST);
    $error = $result['error'] ?? '';
    $success = $result['success'] ?? '';
    if ($success !== '') {
        $page = 'login';
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'login') {
    $result = $auth->login($_POST);
    $error = $result['error'] ?? '';
    if (!empty($result['success'])) {
        header('Location: index.php?page=dashboard');
        exit;
    }
}

if ($page === 'logout') {
    $auth->logout();
    header('Location: index.php');
    exit;
}

if ($page === 'dashboard' && empty($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$allowedPages = ['home', 'register', 'login', 'dashboard'];
if (!in_array($page, $allowedPages, true)) {
    $page = 'home';
}

require __DIR__ . '/views/partials/header.php';
require __DIR__ . '/views/' . $page . '.php';
require __DIR__ . '/views/partials/footer.php';
