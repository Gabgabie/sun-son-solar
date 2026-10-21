<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sun Son Solar</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php">☀ Sun Son Solar</a>
    <nav>
        <a href="index.php">Home</a>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="index.php?page=dashboard">Dashboard</a>
            <a href="index.php?page=logout">Log out</a>
        <?php else: ?>
            <a href="index.php?page=login">Log in</a>
            <a class="nav-button" href="index.php?page=register">Register</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
