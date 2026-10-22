<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Sun Son Solar | Power Your Future</title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body>
        <header class="site-header">
            <a class="brand" href="index.php">
                <span class="brand-mark">&#9728;</span>
                <span>Sun Son Solar<small>Clean energy. Brighter future.</small>
                </span>
            </a>
            <nav class="menu">
                <a href="index.php">Home</a>
                <a href="index.php?page=products">Products</a>
                <a href="index.php?page=services">Services</a>
                <a href="index.php?page=about">About</a>
                <a href="index.php?page=contact">Contact</a>
                <?php if(isset($_SESSION['user_id'])): ?> &middot; <a href="index.php?page=dashboard">Dashboard</a>
                <a href="index.php?page=profile">Profile</a>
                <a class="nav-cta" href="index.php?page=logout">Log out</a>
                <?php else: ?> &middot; <a href="index.php?page=login">Log in</a>
                <a class="nav-cta" href="index.php?page=register">Get started</a>
                <?php endif; ?>
            </nav>
        </header>
        <main class="main">
            <?php if(isset($_SESSION['user_id'])): ?>
            <div class="quick-nav">
                <span>Signed in as <strong>
                        <?php echo htmlspecialchars($_SESSION['user_name']??'User'); ?>
                    </strong>
                </span>
                <div>
                    <a href="index.php?page=dashboard">Dashboard</a>
                    <?php if(($_SESSION['user_role']??'')==='customer'): ?> &middot; <a href="index.php?page=request">Book service</a> &middot; <a href="index.php?page=history">History</a> &middot; <a href="index.php?page=support">Help</a>
                    <?php elseif(($_SESSION['user_role']??'')==='admin'): ?> &middot; <a href="index.php?page=users">Users</a> &middot; <a href="index.php?page=schedules">Schedules</a> &middot; <a href="index.php?page=reports">Reports</a> &middot; <a href="index.php?page=notifications">Notifications</a> &middot; <a href="index.php?page=settings">Settings</a>
                    <?php elseif(in_array(($_SESSION['user_role']??''),['technician','employee'],true)): ?> &middot; <a href="index.php?page=checkin">Check-in</a> &middot; <a href="index.php?page=service-reports">Job reports</a> &middot; <a href="index.php?page=announcements">Announcements</a>
                    <?php else: ?> &middot; <a href="index.php?page=<?php echo h(roleDashboard()); ?>">Department dashboard</a> &middot; <a href="index.php?page=announcements">Announcements</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
