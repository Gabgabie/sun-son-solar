<section class="form-card">
    <p>JOIN SUN SON SOLAR</p>
    <h1>Create your customer account</h1>
    <p class="muted">Fields marked * are required.</p>
    <?php if (!empty($error)): ?><div class="alert error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if (!empty($success)): ?><div class="alert success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <form method="post" action="index.php?page=register">
        <label>Full name *<input name="name" required maxlength="100" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"></label>
        <label>Email address *<input type="email" name="email" required maxlength="190" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"></label>
        <label>Phone number<input type="tel" name="phone" maxlength="30" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"></label>
        <label>Username *<input name="username" required maxlength="50" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"></label>
        <label>Password *<input type="password" name="password" required minlength="8" autocomplete="new-password"></label>
        <label>Confirm password *<input type="password" name="confirm_password" required minlength="8" autocomplete="new-password"></label>
        <button class="button full" type="submit">Register</button>
    </form>
    <p class="form-bottom">Already registered? <a href="index.php?page=login">Log in</a></p>
</section>
