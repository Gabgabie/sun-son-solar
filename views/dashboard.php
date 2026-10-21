<section class="dashboard-card">
    <p class="eyebrow">CUSTOMER DASHBOARD</p>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Customer'); ?>!</h1>
    <p>You are logged in to Sun Son Solar.</p>
    <p class="muted">This is the starter dashboard. More customer features can be added as your project develops.</p>
    <a class="button" href="index.php?page=logout">Log out</a>
</section>
