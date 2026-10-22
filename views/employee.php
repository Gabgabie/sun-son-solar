<section class="page-heading">
    <p>EMPLOYEE WORKSPACE</p>
    <h1>Hello, <?php echo h($_SESSION['user_name']); ?>.</h1>
    <p>See jobs assigned to you, record site attendance and report field issues.</p>
</section>
<div class="dashboard-tiles">
    <a class="dash-tile" href="index.php?page=checkin">
        <span>⌖</span>
        <strong>Site check-in</strong>
        <small>Record your name, server time and coordinates</small>
    </a>
    <a class="dash-tile" href="index.php?page=service-reports">
        <span>⚒</span>
        <strong>Submit job report</strong>
        <small>Send equipment or customer issues to admins</small>
    </a>
    <a class="dash-tile" href="index.php?page=announcements">
        <span>♢</span>
        <strong>Announcements</strong>
        <small>Safety reminders and company updates</small>
    </a>
    <a class="dash-tile" href="index.php?page=profile">
        <span>♙</span>
        <strong>My profile</strong>
        <small>Manage your own account security</small>
    </a>
</div>
<section class="feature">
    <h2>Jobs assigned to me</h2>
    <?php $s=$conn->prepare("SELECT id,customer_id,service_type,preferred_date,preferred_time,status FROM service_requests WHERE assigned_technician_id=? ORDER BY preferred_date");$s->bind_param('i',$_SESSION['user_id']);$s->execute();$rows=$s->get_result();if($rows->num_rows): ?>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Job</th>
                <th>Schedule</th>
                <th>Status</th>
                <th>Customer details</th>
            </tr>
            <?php while($r=$rows->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo (int)$r['id']; ?> — <?php echo h($r['service_type']); ?>
                </td>
                <td>
                    <?php echo h($r['preferred_date'].' '.($r['preferred_time']?:'')); ?>
                </td>
                <td>
                    <?php echo h($r['status']); ?>
                </td>
                <td>
                    <?php $c=$conn->prepare('SELECT first_name,last_name,phone,address FROM users WHERE id=?');$c->bind_param('i',$r['customer_id']);$c->execute();$cu=$c->get_result()->fetch_assoc();$c->close();echo h($cu['first_name'].' '.$cu['last_name']).'<br>'.h($cu['phone']?:'').'<br>'.h($cu['address']?:''); ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <?php else: ?>
    <p>No jobs have been assigned to your account yet.</p>
    <?php endif;$s->close(); ?>
</section>
