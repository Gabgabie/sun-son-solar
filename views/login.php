<section class="form-card">
    <p class="eyebrow">WELCOME BACK</p>
    <h1>Log in to your account</h1>
    <?php if (!empty($error)): ?><div class="alert error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if (!empty($success)): ?><div class="alert success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <form method="post" action="index.php?page=login">
        <label>Username or email<input name="login" required autocomplete="username" value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>"></label>
        <label>Password<input type="password" name="password" required autocomplete="current-password"></label>
        <button class="button full" type="submit">Log in</button>
    </form>
    <p class="form-bottom">New customer? <a href="index.php?page=register">Create an account</a></p>
</section>
