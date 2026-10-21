<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sun Son Solar</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="header">
    <a class="logo" href="index.php">Sun Son Solar</a>

    <div class="menu">
        <a href="index.php">Home</a>

        <?php if (isset($_SESSION["user_id"])) { ?>
            <a href="index.php?page=dashboard">Dashboard</a>
            <a href="index.php?page=logout">Logout</a>
        <?php } else { ?>
            <a href="index.php?page=login">Login</a>
            <a href="index.php?page=register">Register</a>
        <?php } ?>
    </div>
</div>

<div class="main">
