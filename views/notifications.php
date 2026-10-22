<section class="page-heading">
    <p>COMMUNICATIONS</p>
    <h1>Notifications control panel</h1>
    <p>Administrators, HR and marketing can publish customer reminders, employee announcements or general updates.</p>
</section>
<section class="form-card wide">
    <form method="post" action="index.php?page=notifications">
        <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
        <label>Announcement title *<input name="title" maxlength="160" required>
        </label>
        <label>Message *<textarea name="message" rows="4" required>
            </textarea>
        </label>
        <label>Type<select name="type" required>
                <option>Announcement</option>
                <option>Reminder</option>
                <option>Promotion</option>
                <option>System update</option>
            </select>
        </label>
        <label>Audience<select name="audience">
                <option value="all">Everyone</option>
                <option value="customers">Customers only</option>
                <option value="employees">Employees only</option>
            </select>
        </label>
        <button class="button">Publish announcement</button>
    </form>
</section>
<section class="feature">
    <h2>Previously published</h2>
    <?php $q=$conn->query('SELECT title,message,audience,type,created_at FROM notifications ORDER BY created_at DESC LIMIT 30');while($r=$q->fetch_assoc()): ?>
    <article class="notice">
        <strong>
            <?php echo h($r['title']); ?>
        </strong>
        <span class="tag">
            <?php echo h($r['audience']); ?>
        </span>
        <span class="tag">
            <?php echo h($r['type']); ?>
        </span>
        <p>
            <?php echo nl2br(h($r['message'])); ?>
        </p>
        <small>
            <?php echo h($r['created_at']); ?>
        </small>
    </article>
    <?php endwhile; ?>
</section>
