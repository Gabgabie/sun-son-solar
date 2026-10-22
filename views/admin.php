<section class="page-heading">
    <p>MANAGEMENT CONSOLE</p>
    <h1>Admin overview</h1>
    <p>Monitor customers, bookings and field operations.</p>
</section>
<?php
$allAccounts = $conn->query('SELECT COUNT(*) AS total FROM users')->fetch_assoc();
$customers = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='customer'")->fetch_assoc();
$serviceRequests = $conn->query('SELECT COUNT(*) AS total FROM service_requests')->fetch_assoc();
$pendingRequests = $conn->query("SELECT COUNT(*) AS total FROM service_requests WHERE status='Pending'")->fetch_assoc();
$employees = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role<>'customer'")->fetch_assoc();

$stats = [
    ['label' => 'All accounts', 'value' => (int) $allAccounts['total']],
    ['label' => 'Customers', 'value' => (int) $customers['total']],
    ['label' => 'Service requests', 'value' => (int) $serviceRequests['total']],
    ['label' => 'Pending requests', 'value' => (int) $pendingRequests['total']],
    ['label' => 'Employees', 'value' => (int) $employees['total']]
];
?>
<div class="stats-grid">
    <?php foreach($stats as $st): ?>
    <article class="stat-card">
        <span>
            <?php echo h($st['label']); ?>
        </span>
        <strong>
            <?php echo $st['value']; ?>
        </strong>
    </article>
    <?php endforeach; ?>
</div>
<div class="dashboard-tiles">
    <a class="dash-tile" href="index.php?page=users">
        <span>♙</span>
        <strong>User management</strong>
        <small>Create employee accounts and activate/deactivate users</small>
    </a>
    <a class="dash-tile" href="index.php?page=schedules">
        <span>▦</span>
        <strong>Schedule overview</strong>
        <small>Approve requests and assign technicians</small>
    </a>
    <a class="dash-tile" href="index.php?page=notifications">
        <span>♢</span>
        <strong>Notifications</strong>
        <small>Publish updates and announcements</small>
    </a>
    <a class="dash-tile" href="index.php?page=reports">
        <span>▥</span>
        <strong>Reports & analytics</strong>
        <small>Review attendance, services and revenue placeholders</small>
    </a>
    <a class="dash-tile" href="index.php?page=settings">
        <span>⚙</span>
        <strong>Service settings</strong>
        <small>Manage service names, prices and availability</small>
    </a>
</div>
<section class="feature">
    <h2>Recent service requests</h2>
    <?php $q=$conn->query('SELECT s.id,s.service_type,s.preferred_date,s.status,u.first_name,u.last_name FROM service_requests s JOIN users u ON s.customer_id=u.id ORDER BY s.created_at DESC LIMIT 15'); ?>
    <div class="table-wrap">
        <table>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Service</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
            <?php while($r=$q->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo (int)$r['id']; ?>
                </td>
                <td>
                    <?php echo h($r['first_name'].' '.$r['last_name']); ?>
                </td>
                <td>
                    <?php echo h($r['service_type']); ?>
                </td>
                <td>
                    <?php echo h($r['preferred_date']); ?>
                </td>
                <td>
                    <?php echo h($r['status']); ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</section>
<section class="feature">
    <h2>Recent technician reports</h2>
    <?php $q=$conn->query('SELECT r.issue_type,r.description,r.status,r.created_at,u.first_name,u.last_name FROM service_reports r JOIN users u ON u.id=r.employee_id ORDER BY r.created_at DESC LIMIT 10'); ?>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Technician</th>
                <th>Issue</th>
                <th>Description</th>
                <th>Status</th>
                <th>Submitted</th>
            </tr>
            <?php while($r=$q->fetch_assoc()): ?>
            <tr>
                <td><?php echo h($r['first_name'] . ' ' . $r['last_name']); ?></td>
                <td><?php echo h($r['issue_type']); ?></td>
                <td><?php echo h($r['description']); ?></td>
                <td><?php echo h($r['status']); ?></td>
                <td><?php echo h($r['created_at']); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</section>
<section class="feature">
    <h2>Latest field check-ins</h2>
    <?php $q=$conn->query('SELECT c.checked_in_at,c.latitude,c.longitude,u.first_name,u.last_name FROM checkins c JOIN users u ON u.id=c.employee_id ORDER BY c.checked_in_at DESC LIMIT 10'); ?>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Employee</th>
                <th>Time (server)</th>
                <th>Coordinates</th>
            </tr>
            <?php while($r=$q->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php echo h($r['first_name'].' '.$r['last_name']); ?>
                </td>
                <td>
                    <?php echo h($r['checked_in_at']); ?>
                </td>
                <td>
                    <?php echo $r['latitude']!==null?h($r['latitude'].', '.$r['longitude']):'Not provided'; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</section>
