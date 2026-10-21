<?php
session_start();

require_once "config/database.php";
require_once "models/User.php";
require_once "controllers/AuthController.php";

$userModel = new User($conn);
$auth = new AuthController($userModel);

$page = "home";
if (isset($_GET["page"])) {
    $page = $_GET["page"];
}

$error = "";
$success = "";

// Handle the registration form.
if ($_SERVER["REQUEST_METHOD"] == "POST" && $page == "register") {
    $result = $auth->register($_POST);

    if (isset($result["error"])) {
        $error = $result["error"];
    }

    if (isset($result["success"])) {
        $success = $result["success"];
        $page = "login";
    }
}

// Handle the login form.
if ($_SERVER["REQUEST_METHOD"] == "POST" && $page == "login") {
    $result = $auth->login($_POST);

    if (isset($result["error"])) {
        $error = $result["error"];
    }

    if (isset($result["success"])) {
        header("Location: index.php?page=dashboard");
        exit;
    }
}

// Log out the current user.
if ($page == "logout") {
    $auth->logout();
    header("Location: index.php");
    exit;
}

// Only logged-in users can open the dashboard.
if ($page == "dashboard" && !isset($_SESSION["user_id"])) {
    header("Location: index.php?page=login");
    exit;
}

// Only allow pages that exist in this project.
if ($page != "home" && $page != "register" && $page != "login" && $page != "dashboard") {
    $page = "home";
}

include "views/partials/header.php";
include "views/" . $page . ".php";
include "views/partials/footer.php";
