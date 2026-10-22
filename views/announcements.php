<section class="page-heading">
    <p>TEAM UPDATES</p>
    <h1>Announcements</h1>
    <p>Company updates, safety reminders and notices from administration or HR.</p>
</section>
<section class="feature">
    <?php $role=$_SESSION['user_role'];$aud=$role==='customer'?'customers':'employees';$s=$conn->prepare("SELECT title,message,created_at FROM notifications WHERE audience IN ('all',?) ORDER BY created_at DESC");$s->bind_param('s',$aud);$s->execute();$rs=$s->get_result();if($rs->num_rows):while($r=$rs->fetch_assoc()): ?>
    <article class="notice">
        <h3>
            <?php echo h($r['title']); ?>
        </h3>
        <p>
            <?php echo nl2br(h($r['message'])); ?>
        </p>
        <small>
            <?php echo h($r['created_at']); ?>
        </small>
    </article>
    <?php endwhile;else: ?>
    <p>No announcements available.</p>
    <?php endif;$s->close(); ?>
</section>
