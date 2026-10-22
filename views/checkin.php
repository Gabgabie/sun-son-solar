<section class="form-card checkin-card">
    <div class="login-icon">⌖</div>
    <p>FIELD ATTENDANCE</p>
    <h1>Site check-in</h1>
    <p>When you arrive at an assigned site, capture your location and submit check-in. Your name and time are recorded from your signed-in account and server.</p>
    <form method="post" action="index.php?page=checkin" id="checkinForm">
        <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
        <label>Assigned job (optional)<select name="service_request_id">
                <option value="">Select assigned job</option>
                <?php $s=$conn->prepare("SELECT id,service_type,preferred_date FROM service_requests WHERE assigned_technician_id=? AND status IN ('Approved','Pending') ORDER BY preferred_date");$s->bind_param('i',$_SESSION['user_id']);$s->execute();$jobs=$s->get_result();while($j=$jobs->fetch_assoc()): ?>
                <option value="<?php echo (int)$j['id']; ?>">#<?php echo (int)$j['id']; ?> — <?php echo h($j['service_type'].' · '.$j['preferred_date']); ?>
                </option>
                <?php endwhile;$s->close(); ?>
            </select>
        </label>
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <p id="locationMessage" class="muted" aria-live="polite">Press the button to save your name, arrival time and site coordinates.</p>
        <button class="button" type="button" onclick="recordCheckin()">Record site check-in</button>
    </form>
</section>
<section class="feature">
    <h2>My recent check-ins</h2>
    <?php $s=$conn->prepare('SELECT checked_in_at,latitude,longitude FROM checkins WHERE employee_id=? ORDER BY checked_in_at DESC LIMIT 10');$s->bind_param('i',$_SESSION['user_id']);$s->execute();$rows=$s->get_result();if($rows->num_rows): ?>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Server time</th>
                <th>Coordinates</th>
            </tr>
            <?php while($r=$rows->fetch_assoc()): ?>
            <tr>
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
    <?php else: ?>
    <p>No check-ins yet.</p>
    <?php endif;$s->close(); ?>
</section>
<script>function recordCheckin(){const form=document.getElementById("checkinForm");const message=document.getElementById("locationMessage");function saveWithoutLocation(){if(confirm("Could not get your location. Record check-in without coordinates?")){form.submit();}}if(!navigator.geolocation){message.textContent="This browser does not support location.";saveWithoutLocation();return;}message.textContent="Getting your location...";navigator.geolocation.getCurrentPosition(function(position){document.getElementById("latitude").value=position.coords.latitude;document.getElementById("longitude").value=position.coords.longitude;message.textContent="Location saved. Recording check-in...";form.submit();},function(){message.textContent="Location was not available.";saveWithoutLocation();},{enableHighAccuracy:true,timeout:10000,maximumAge:0});}</script>
