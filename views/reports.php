<section class="page-heading">
    <p>BUSINESS INSIGHTS</p>
    <h1>Reports & analytics</h1>
    <p>Live summary counts from the current database. Revenue analytics require verified payment records.</p>
</section>
<?php $metrics=[['Total customers',"SELECT COUNT(*) n FROM users WHERE role='customer'"],['Completed services',"SELECT COUNT(*) n FROM service_requests WHERE status='Completed'"],['Open service reports',"SELECT COUNT(*) n FROM service_reports WHERE status<>'Resolved'"],['Support tickets',"SELECT COUNT(*) n FROM support_tickets WHERE status<>'Closed'"],['Field check-ins','SELECT COUNT(*) n FROM checkins'],['Verified payments',"SELECT COUNT(*) n FROM payments WHERE status='Paid'"]]; ?>
<div class="stats-grid">
    <?php foreach($metrics as $m): ?>
    <article class="stat-card">
        <span>
            <?php echo h($m[0]); ?>
        </span>
        <strong>
            <?php echo (int)$conn->query($m[1])->fetch_assoc()['n']; ?>
        </strong>
    </article>
    <?php endforeach; ?>
</div>
<section class="feature">
    <h2>Customer activity</h2>
    <p>Customers with the most service requests.</p>
    <?php
    $customerQuery = $conn->query("SELECT u.first_name, u.last_name, COUNT(s.id) AS request_count
        FROM users u LEFT JOIN service_requests s ON s.customer_id = u.id
        WHERE u.role = 'customer' GROUP BY u.id ORDER BY request_count DESC LIMIT 10");
    $customers = [];
    $maxCustomerRequests = 1;
    while ($customer = $customerQuery->fetch_assoc()) {
        $customers[] = $customer;
        $maxCustomerRequests = max($maxCustomerRequests, (int) $customer['request_count']);
    }
    ?>
    <?php if ($customers): foreach ($customers as $customer): ?>
    <div class="bar-row">
        <span><?php echo h($customer['first_name'] . ' ' . $customer['last_name']); ?></span>
        <div class="bar-track"><div class="bar-fill" style="width:<?php echo ((int) $customer['request_count'] / $maxCustomerRequests) * 100; ?>%"></div></div>
        <strong><?php echo (int) $customer['request_count']; ?></strong>
    </div>
    <?php endforeach; else: ?>
    <p>No customer activity yet.</p>
    <?php endif; ?>
</section>
<section class="feature">
    <h2>Service usage</h2>
    <?php
    $serviceQuery = $conn->query('SELECT service_type, COUNT(*) AS request_count FROM service_requests GROUP BY service_type ORDER BY request_count DESC');
    $services = [];
    $maxServiceRequests = 1;
    while ($service = $serviceQuery->fetch_assoc()) {
        $services[] = $service;
        $maxServiceRequests = max($maxServiceRequests, (int) $service['request_count']);
    }
    ?>
    <?php if ($services): foreach ($services as $service): ?>
    <div class="bar-row">
        <span><?php echo h($service['service_type']); ?></span>
        <div class="bar-track"><div class="bar-fill" style="width:<?php echo ((int) $service['request_count'] / $maxServiceRequests) * 100; ?>%"></div></div>
        <strong><?php echo (int) $service['request_count']; ?></strong>
    </div>
    <?php endforeach; else: ?>
    <p>No service requests yet.</p>
    <?php endif; ?>
</section>
<section class="feature">
    <h2>Service request status</h2>
    <?php $q=$conn->query('SELECT status,COUNT(*) total FROM service_requests GROUP BY status');$data=[];$max=1;while($r=$q->fetch_assoc()){$data[]=$r;$max=max($max,(int)$r['total']);}if($data):foreach($data as $d): ?>
    <div class="bar-row">
        <span>
            <?php echo h($d['status']); ?>
        </span>
        <div class="bar-track">
            <div class="bar-fill" style="width:<?php echo ((int)$d['total']/$max)*100; ?>%">
            </div>
        </div>
        <strong>
            <?php echo (int)$d['total']; ?>
        </strong>
    </div>
    <?php endforeach;else: ?>
    <p>No service data yet.</p>
    <?php endif; ?>
</section>
<section class="feature">
    <h2>Revenue</h2>
    <p>Revenue should be calculated only from verified records in the payments table. No payment gateway is configured in this starter project.</p>
    <?php $sum=$conn->query("SELECT COALESCE(SUM(amount),0) total FROM payments WHERE status='Paid'")->fetch_assoc()['total']; ?>
    <div class="stat-card">
        <span>Recorded paid total</span>
        <strong>₱<?php echo number_format((float)$sum,2); ?>
        </strong>
    </div>
</section>
<section class="feature">
    <h2>Recent service reports</h2>
    <?php $q=$conn->query('SELECT r.issue_type,r.description,r.status,r.created_at,u.first_name,u.last_name FROM service_reports r JOIN users u ON u.id=r.employee_id ORDER BY r.created_at DESC LIMIT 20'); ?>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Employee</th>
                <th>Issue</th>
                <th>Description</th>
                <th>Status</th>
            </tr>
            <?php while($r=$q->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php echo h($r['first_name'].' '.$r['last_name']); ?>
                </td>
                <td>
                    <?php echo h($r['issue_type']); ?>
                </td>
                <td>
                    <?php echo h($r['description']); ?>
                </td>
                <td>
                    <?php echo h($r['status']); ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</section>
