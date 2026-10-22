<section class="form-card wide">
    <p>CUSTOMER PORTAL</p>
    <h1>Create your customer account</h1>
    <p>Register to request and manage solar services. Employee accounts are created by HR or an administrator.</p>
    <form method="post" action="index.php?page=register">
        <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
        <div class="form-grid">
            <label>First name *<input name="first_name" autocomplete="given-name" required>
            </label>
            <label>Last name *<input name="last_name" autocomplete="family-name" required>
            </label>
            <label>Middle name<input name="middle_name">
            </label>
            <label>Birthdate<input type="date" name="birthdate" max="<?php echo date('Y-m-d'); ?>">
            </label>
            <label>Gender<select name="gender">
                    <option value="">Select (optional)</option>
                    <option>Female</option>
                    <option>Male</option>
                    <option>Prefer not to say</option>
                </select>
            </label>
            <label>Email *<input type="email" name="email" autocomplete="email" required>
            </label>
            <label>Phone number<input name="phone" autocomplete="tel">
            </label>
            <label class="full">Address<input name="address" autocomplete="street-address">
            </label>
            <label>Username *<input name="username" minlength="3" maxlength="50" autocomplete="username" required>
            </label>
            <label>Password *<input type="password" name="password" minlength="8" autocomplete="new-password" required>
            </label>
            <label class="full">Confirm password *<input type="password" name="confirm_password" minlength="8" autocomplete="new-password" required>
            </label>
        </div>
        <button class="button" type="submit">Create account</button>
    </form>
    <p>Already registered? <a href="index.php?page=login">Log in</a>
    </p>
</section>
