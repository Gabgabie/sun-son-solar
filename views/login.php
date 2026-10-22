<section class="form-card login-card">
    <div class="login-icon">☀</div>
    <p>WELCOME BACK</p>
    <h1>Log in to Sun Son Solar</h1>
    <p>Access your bookings, updates and account.</p>
    <form method="post" action="index.php?page=login">
        <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
        <label>Username or email<input name="login" autocomplete="username" required autofocus>
        </label>
        <label>Password<input type="password" name="password" autocomplete="current-password" required>
        </label>
        <button class="button" type="submit">Log in securely</button>
    </form>
    <p>New customer? <a href="index.php?page=register">Create an account</a>
    </p>
</section>
