<section class="page-heading">
    <p>CUSTOMER CARE</p>
    <h1>Help & support</h1>
    <p>Send a question or describe an issue. Our team can review the ticket from the management system.</p>
</section>
<section class="form-card">
    <form method="post" action="index.php?page=support">
        <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
        <label>Subject *<input name="subject" maxlength="160" required>
        </label>
        <label>How can we help? *<textarea name="message" rows="5" required>
            </textarea>
        </label>
        <button class="button" type="submit">Submit support ticket</button>
    </form>
</section>
<section class="feature">
    <h2>Your tickets</h2>
    <?php $s=$conn->prepare('SELECT subject,message,status,created_at FROM support_tickets WHERE customer_id=? ORDER BY created_at DESC');$s->bind_param('i',$_SESSION['user_id']);$s->execute();$rs=$s->get_result();if($rs->num_rows):while($r=$rs->fetch_assoc()): ?>
    <div class="notice">
        <strong>
            <?php echo h($r['subject']); ?>
        </strong>
        <p>
            <?php echo nl2br(h($r['message'])); ?>
        </p>
        <span class="status">
            <?php echo h($r['status']); ?>
        </span> <small>
            <?php echo h($r['created_at']); ?>
        </small>
    </div>
    <?php endwhile;else: ?>
    <p>No support tickets yet.</p>
    <?php endif;$s->close(); ?>
</section>
