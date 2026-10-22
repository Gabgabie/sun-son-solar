<?php $s=$conn->prepare('SELECT first_name,last_name,middle_name,birthdate,gender,email,phone,address,username,receive_reminders,receive_promotions FROM users WHERE id=?');$s->bind_param('i',$_SESSION['user_id']);$s->execute();$me=$s->get_result()->fetch_assoc();$s->close();$forcePasswordChange=!empty($_SESSION['must_change_password']); ?>
<section class="form-card wide">
    <p>ACCOUNT SETTINGS</p>
    <h1>
        <?php echo $forcePasswordChange?'Change your temporary password':'Your profile'; ?>
    </h1>
    <?php if($forcePasswordChange): ?>
    <p class="message">Choose a new password to finish setting up your account. You can update your profile after this step.</p>
    <?php else: ?>
    <form method="post" action="index.php?page=profile">
        <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
        <div class="form-grid">
            <label>First name *<input name="first_name" value="<?php echo h($me['first_name']); ?>" required>
            </label>
            <label>Last name *<input name="last_name" value="<?php echo h($me['last_name']); ?>" required>
            </label>
            <label>Middle name<input name="middle_name" value="<?php echo h($me['middle_name']); ?>">
            </label>
            <label>Birthdate<input type="date" name="birthdate" max="<?php echo date('Y-m-d'); ?>" value="<?php echo h($me['birthdate']); ?>">
            </label>
            <label>Gender<select name="gender">
                    <option value="">Select</option>
                    <?php foreach(['Female','Male','Prefer not to say'] as $g): ?>
                    <option <?php echo $me['gender']===$g?'selected':''; ?>>
                        <?php echo h($g); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Email *<input type="email" name="email" value="<?php echo h($me['email']); ?>" required>
            </label>
            <label>Phone<input name="phone" value="<?php echo h($me['phone']); ?>">
            </label>
            <label class="full">Address<input name="address" value="<?php echo h($me['address']); ?>">
            </label>
            <label>Username (cannot be changed here)<input value="<?php echo h($me['username']); ?>" disabled>
            </label>
        </div>
        <button class="button" type="submit">Save profile</button>
    </form>
    <?php if(($_SESSION['user_role']??'')==='customer'): ?>
    <hr>
    <h2>Notification preferences</h2>
    <form method="post" action="index.php?page=profile">
        <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
        <input type="hidden" name="action" value="save_preferences">
        <label><input type="checkbox" name="receive_reminders" value="1" <?php echo $me['receive_reminders']?'checked':''; ?>> Show service reminders</label>
        <label><input type="checkbox" name="receive_promotions" value="1" <?php echo $me['receive_promotions']?'checked':''; ?>> Show promotions and offers</label>
        <button class="button button-small" type="submit">Save preferences</button>
    </form>
    <?php endif; ?>
    <hr>
    <h2>Change password</h2>
    <?php endif; ?>
    <form method="post" action="index.php?page=profile">
        <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
        <?php if(!$forcePasswordChange): ?>
        <label>Current password<input type="password" name="current_password" required>
        </label>
        <?php endif; ?>
        <label>New password<input type="password" name="new_password" minlength="8" required>
        </label>
        <label>Confirm new password<input type="password" name="confirm_password" minlength="8" required>
        </label>
        <button class="button" type="submit">
            <?php echo $forcePasswordChange?'Set new password':'Change password'; ?>
        </button>
    </form>
</section>
