<section class="form-card wide">
    <p>FIELD REPORTING</p>
    <h1>Submit a service report</h1>
    <p>Report equipment problems, safety concerns or customer issues. The report will be visible to administrators.</p>
    <form method="post" action="index.php?page=service-reports">
        <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
        <label>Assigned job (optional)<select name="service_request_id">
                <option value="">Not tied to a job</option>
                <?php $s=$conn->prepare('SELECT id,service_type,preferred_date FROM service_requests WHERE assigned_technician_id=?');$s->bind_param('i',$_SESSION['user_id']);$s->execute();$jobs=$s->get_result();while($j=$jobs->fetch_assoc()): ?>
                <option value="<?php echo (int)$j['id']; ?>">#<?php echo (int)$j['id']; ?> — <?php echo h($j['service_type'].' · '.$j['preferred_date']); ?>
                </option>
                <?php endwhile;$s->close(); ?>
            </select>
        </label>
        <label>Issue type *<select name="issue_type" required>
                <option value="">Select issue</option>
                <option>Equipment issue</option>
                <option>Installation problem</option>
                <option>Safety concern</option>
                <option>Customer concern</option>
                <option>Parts or inventory</option>
                <option>Other</option>
            </select>
        </label>
        <label>Description *<textarea name="description" rows="5" required>
            </textarea>
        </label>
        <button class="button">Send report</button>
    </form>
</section>
<section class="feature">
    <h2>My submitted reports</h2>
    <?php $s=$conn->prepare('SELECT issue_type,description,status,created_at FROM service_reports WHERE employee_id=? ORDER BY created_at DESC');$s->bind_param('i',$_SESSION['user_id']);$s->execute();$rs=$s->get_result();if($rs->num_rows):while($r=$rs->fetch_assoc()): ?>
    <div class="notice">
        <strong>
            <?php echo h($r['issue_type']); ?>
        </strong>
        <span class="status">
            <?php echo h($r['status']); ?>
        </span>
        <p>
            <?php echo nl2br(h($r['description'])); ?>
        </p>
        <small>
            <?php echo h($r['created_at']); ?>
        </small>
    </div>
    <?php endwhile;else: ?>
    <p>No reports submitted yet.</p>
    <?php endif;$s->close(); ?>
</section>
