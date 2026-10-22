<?php
$role = currentRole();
$dashboards = [
'admin' => ['Administration dashboard','Manage users, schedules, reports and company announcements.',['User management','users','Create accounts and manage access.'],['Schedules','schedules','Review bookings and assign technicians.'],['Reports','reports','View service activity and summary figures.'],['Notifications','notifications','Publish company announcements.']],
'hr' => ['HR dashboard','Manage staff accounts and keep employee information organized.',['Staff accounts','users','Create staff accounts and update account status.'],['Publish team updates','notifications','Publish reminders and announcements for employees.'],['Announcements','announcements','Read company announcements.'],['My profile','profile','Update your own details and password.']],
'dispatcher' => ['Dispatch dashboard','Review service requests and coordinate technician assignments.',['Manage schedules','schedules','Review requests, set status and assign technicians.'],['Service reports','reports','Review basic service activity.'],['Announcements','announcements','Read company updates.']],
'technician' => ['Technician dashboard','View assigned jobs and report your field work.',['Assigned jobs','employee','See customer and appointment details for your jobs.'],['Site check-in','checkin','Record arrival time and optional location.'],['Job report','service-reports','Send an issue report to the office.'],['Announcements','announcements','Read safety reminders and updates.']],
'accounting' => ['Accounting dashboard','Review payment-related records and service activity.',['Reports','reports','View available service and revenue summary.'],['Announcements','announcements','Read company updates.'],['My profile','profile','Update your own details and password.']],
'sales' => ['Sales dashboard','Keep track of customer service requests and follow-up needs.',['Reports','reports','View service activity summary.'],['Announcements','announcements','Read company updates.'],['My profile','profile','Update your own details and password.']],
'customer_service' => ['Customer service dashboard','Help customers follow up on their service requests.',['Support inbox','support-desk','Review customer tickets and update their status.'],['My profile','profile','Update your own details and password.']],
'marketing' => ['Marketing dashboard','Share company news and announcements with customers and staff.',['Publish announcements','notifications','Create a company update.'],['Announcements','announcements','Read company updates.'],['My profile','profile','Update your own details and password.']]
];
$data = $dashboards[$role] ?? $dashboards['technician'];
?>
<section class="page-heading">
    <p>
        <?php echo h(str_replace('_',' ',$role)); ?>
    </p>
    <h1>
        <?php echo h($data[0]); ?>
    </h1>
    <p>Hello, <?php echo h($_SESSION['user_name'] ?? 'Staff member'); ?>. <?php echo h($data[1]); ?>
    </p>
</section>
<div class="dashboard-tiles">
    <?php for ($i=2; $i<count($data); $i++): $tile=$data[$i]; ?>
    <a class="dash-tile" href="index.php?page=<?php echo h($tile[1]); ?>">
        <strong>
            <?php echo h($tile[0]); ?>
        </strong>
        <small>
            <?php echo h($tile[2]); ?>
        </small>
    </a>
    <?php endfor; ?>
</div>
<section class="feature">
    <h2>Quick notes</h2>
    <p>This dashboard is for the <?php echo h(str_replace('_',' ',$role)); ?> role. Use the links above for your regular tasks. Some shared pages are still basic in this school project.</p>
</section>
