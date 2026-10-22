<section class="page-heading">
    <p>YOUR ACCOUNT</p>
    <h1>Welcome, <?php echo h($_SESSION['user_name']??'Customer'); ?>.</h1>
    <p>Manage your solar service requests and account details in one place.</p>
</section>
<div class="dashboard-tiles">
    <a class="dash-tile" href="index.php?page=request">
        <span>＋</span>
        <strong>Request a service</strong>
        <small>Consultation, installation, maintenance and more</small>
    </a>
    <a class="dash-tile" href="index.php?page=history">
        <span>◷</span>
        <strong>Service history</strong>
        <small>Review your requests and their status</small>
    </a>
    <a class="dash-tile" href="index.php?page=profile">
        <span>♙</span>
        <strong>Profile & security</strong>
        <small>Update details and password</small>
    </a>
    <a class="dash-tile" href="index.php?page=support">
        <span>?</span>
        <strong>Help & support</strong>
        <small>Contact the Sun Son Solar team</small>
    </a>
</div>
<section class="feature">
    <div class="section-title-row">
        <h2>Your upcoming and recent requests</h2>
        <a class="text-link" href="index.php?page=history">View all →</a>
    </div>
    <?php $s=$conn->prepare('SELECT id,service_type,preferred_date,preferred_time,status FROM service_requests WHERE customer_id=? ORDER BY preferred_date DESC,id DESC LIMIT 8');$s->bind_param('i',$_SESSION['user_id']);$s->execute();$rows=$s->get_result();if($rows->num_rows): ?>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Service</th>
                <th>Preferred date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
            <?php while($r=$rows->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php echo h($r['service_type']); ?>
                </td>
                <td>
                    <?php echo h($r['preferred_date']); ?>
                </td>
                <td>
                    <?php echo h($r['preferred_time']?:'To be confirmed'); ?>
                </td>
                <td>
                    <span class="status status-<?php echo strtolower(str_replace(' ','-',$r['status'])); ?>">
                        <?php echo h($r['status']); ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <?php else: ?>
    <p>You have no service requests yet. When you're ready, request a consultation or another service.</p>
    <?php endif;$s->close(); ?>
</section>
<section class="feature">
    <h2>Notifications</h2>
    <?php $preferenceQuery=$conn->prepare('SELECT receive_reminders,receive_promotions FROM users WHERE id=?');$preferenceQuery->bind_param('i',$_SESSION['user_id']);$preferenceQuery->execute();$preferences=$preferenceQuery->get_result()->fetch_assoc();$preferenceQuery->close();$receiveReminders=(int)$preferences['receive_reminders'];$receivePromotions=(int)$preferences['receive_promotions'];$customerId=(int)$_SESSION['user_id'];$s=$conn->prepare("SELECT n.title,n.message,n.type,n.created_at FROM notifications n
        WHERE n.audience IN ('all','customers')
        AND (NOT EXISTS (SELECT 1 FROM user_notifications u WHERE u.notification_id=n.id)
             OR EXISTS (SELECT 1 FROM user_notifications u WHERE u.notification_id=n.id AND u.user_id=?))
        AND (n.type <> 'Reminder' OR ?=1)
        AND (n.type <> 'Promotion' OR ?=1)
        ORDER BY n.created_at DESC LIMIT 5");$s->bind_param('iii',$customerId,$receiveReminders,$receivePromotions);$s->execute();$n=$s->get_result();if($n->num_rows):while($r=$n->fetch_assoc()): ?>
    <div class="notice">
        <strong>
            <?php echo h($r['title']); ?>
        </strong>
        <span class="tag"><?php echo h($r['type']); ?></span>
        <p>
            <?php echo nl2br(h($r['message'])); ?>
        </p>
        <small>
            <?php echo h($r['created_at']); ?>
        </small>
    </div>
    <?php endwhile;else: ?>
    <p>No new announcements at the moment.</p>
    <?php endif;$s->close(); ?>
</section>
