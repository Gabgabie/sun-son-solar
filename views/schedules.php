<?php
$requestedMonth = $_GET['month'] ?? date('Y-m');
$monthDate = DateTime::createFromFormat('!Y-m-d', $requestedMonth . '-01');

if (!$monthDate || $monthDate->format('Y-m') !== $requestedMonth) {
    $monthDate = new DateTime('first day of this month');
}

$monthStart = $monthDate->format('Y-m-01');
$monthEnd = $monthDate->format('Y-m-t');
$previousMonth = (clone $monthDate)->modify('-1 month')->format('Y-m');
$nextMonth = (clone $monthDate)->modify('+1 month')->format('Y-m');
$daysInMonth = (int) $monthDate->format('t');
$firstWeekday = (int) $monthDate->format('N');
$bookingsByDay = [];

$calendarStatement = $conn->prepare(
    'SELECT s.id, s.service_type, s.status, s.preferred_date, u.first_name, u.last_name
     FROM service_requests s
     JOIN users u ON u.id = s.customer_id
     WHERE s.preferred_date BETWEEN ? AND ?
     ORDER BY s.preferred_date, s.preferred_time'
);
$calendarStatement->bind_param('ss', $monthStart, $monthEnd);
$calendarStatement->execute();
$calendarBookings = $calendarStatement->get_result();

while ($booking = $calendarBookings->fetch_assoc()) {
    $day = (int) date('j', strtotime($booking['preferred_date']));
    $bookingsByDay[$day][] = $booking;
}

$calendarStatement->close();
?>

<section class="page-heading">
    <p>DISPATCH & SCHEDULING</p>
    <h1>Schedule overview</h1>
    <p>View bookings by date, update their status, and assign technicians.</p>
</section>

<section class="feature">
    <div class="calendar-heading">
        <a class="button button-small" href="index.php?page=schedules&amp;month=<?php echo h($previousMonth); ?>">Previous month</a>
        <h2><?php echo h($monthDate->format('F Y')); ?></h2>
        <a class="button button-small" href="index.php?page=schedules&amp;month=<?php echo h($nextMonth); ?>">Next month</a>
    </div>
    <div class="calendar-wrapper">
    <div class="calendar-grid">
        <?php foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $weekday): ?>
        <div class="calendar-weekday"><?php echo h($weekday); ?></div>
        <?php endforeach; ?>

        <?php for ($blankDay = 1; $blankDay < $firstWeekday; $blankDay++): ?>
        <div class="calendar-day empty-day"></div>
        <?php endfor; ?>

        <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
        <div class="calendar-day">
            <strong><?php echo $day; ?></strong>
            <?php if (!empty($bookingsByDay[$day])): ?>
                <?php foreach ($bookingsByDay[$day] as $booking): ?>
                <a class="calendar-booking" href="#booking-<?php echo (int) $booking['id']; ?>">
                    <?php echo h($booking['service_type'] . ' - ' . $booking['first_name'] . ' ' . $booking['last_name']); ?>
                    <small><?php echo h($booking['status']); ?></small>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php endfor; ?>
    </div>
    </div>
</section>

<section class="feature">
    <div class="table-wrap">
        <table>
            <tr>
                <th>Customer / request</th>
                <th>Preferred date</th>
                <th>Service</th>
                <th>Update booking</th>
            </tr>
            <?php $q=$conn->query("SELECT s.*,u.first_name,u.last_name FROM service_requests s JOIN users u ON u.id=s.customer_id ORDER BY s.preferred_date ASC");while($r=$q->fetch_assoc()): ?>
            <tr id="booking-<?php echo (int) $r['id']; ?>">
                <td>
                    <strong>
                        <?php echo h($r['first_name'].' '.$r['last_name']); ?>
                    </strong>
                    <small class="block">Request #<?php echo (int)$r['id']; ?>
                    </small>
                </td>
                <td>
                    <?php echo h($r['preferred_date'].' '.($r['preferred_time']?:'')); ?>
                </td>
                <td>
                    <?php echo h($r['service_type']); ?>
                    <small class="block">Current: <?php echo h($r['status']); ?>
                    </small>
                </td>
                <td>
                    <details class="schedule-edit">
                    <summary>Update booking</summary>
                    <form method="post" action="index.php?page=schedules" class="schedule-form">
                        <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
                        <input type="hidden" name="request_id" value="<?php echo (int)$r['id']; ?>">
                        <label>Appointment date<input type="date" name="preferred_date" value="<?php echo h($r['preferred_date']); ?>" required></label>
                        <label>Appointment time<input type="time" name="preferred_time" value="<?php echo h(substr($r['preferred_time'] ?? '', 0, 5)); ?>"></label>
                        <label>Status<select name="status">
                                <?php foreach(['Pending','Approved','Declined','Completed','Cancelled'] as $st): ?>
                                <option <?php echo $r['status']===$st?'selected':''; ?>>
                                    <?php echo h($st); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label>Technician<select name="technician_id">
                                <option value="">Unassigned</option>
                                <?php $techs=$conn->query("SELECT id,first_name,last_name FROM users WHERE active=1 AND (role='technician' OR (role='employee' AND department='IT')) ORDER BY first_name");while($t=$techs->fetch_assoc()): ?>
                                <option value="<?php echo (int)$t['id']; ?>" <?php echo (int)$r['assigned_technician_id']===(int)$t['id']?'selected':''; ?>>
                                    <?php echo h($t['first_name'].' '.$t['last_name']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </label>
                        <label>Team note<textarea name="admin_notes" rows="2">
                                <?php echo h($r['admin_notes']); ?>
                            </textarea>
                        </label>
                        <button class="button button-small">Save booking</button>
                        <?php if(!in_array($r['status'],['Cancelled','Completed'],true)): ?>
                        <button class="button button-small button-danger" type="submit" onclick="if(!confirm('Cancel this booking?')){return false;} this.form.querySelector('[name=status]').value='Cancelled';">Cancel booking</button>
                        <?php endif; ?>
                    </form>
                    </details>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</section>
