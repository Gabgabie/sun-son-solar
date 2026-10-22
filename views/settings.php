<section class="page-heading">
    <p>ADMIN SETTINGS</p>
    <h1>Service categories and availability</h1>
    <p>Add services, set a base price, and choose the days and hours when customers can request them.</p>
</section>

<section class="form-card wide">
    <h2>Add a service</h2>
    <form method="post" action="index.php?page=settings">
        <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
        <input type="hidden" name="action" value="save_service">
        <div class="form-grid">
            <label>Service name *<input name="name" maxlength="80" required></label>
            <label>Base price<input type="number" name="base_price" min="0" step="0.01" value="0" required></label>
            <label class="full">Available days *<input name="available_days" value="Monday,Tuesday,Wednesday,Thursday,Friday" required></label>
            <label>Start time<input type="time" name="start_time"></label>
            <label>End time<input type="time" name="end_time"></label>
        </div>
        <p class="muted">Use weekday names separated by commas. Leave both times blank if the service has no fixed time range.</p>
        <button class="button" type="submit">Add service</button>
    </form>
</section>

<section class="feature">
    <h2>Current services</h2>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Service details</th>
                <th>Availability</th>
                <th>Status</th>
            </tr>
            <?php
            $services = $conn->query('SELECT id, name, base_price, available_days, start_time, end_time, active FROM service_catalog ORDER BY name');
            while ($service = $services->fetch_assoc()):
            ?>
            <tr>
                <td colspan="3">
                    <form method="post" action="index.php?page=settings" class="settings-service-form">
                        <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
                        <input type="hidden" name="action" value="save_service">
                        <input type="hidden" name="service_id" value="<?php echo (int) $service['id']; ?>">
                        <div class="form-grid">
                            <label>Service name<input name="name" value="<?php echo h($service['name']); ?>" required></label>
                            <label>Base price<input type="number" name="base_price" min="0" step="0.01" value="<?php echo h($service['base_price']); ?>" required></label>
                            <label class="full">Available days<input name="available_days" value="<?php echo h($service['available_days']); ?>" required></label>
                            <label>Start time<input type="time" name="start_time" value="<?php echo h($service['start_time']); ?>"></label>
                            <label>End time<input type="time" name="end_time" value="<?php echo h($service['end_time']); ?>"></label>
                        </div>
                        <p class="muted">Status: <?php echo $service['active'] ? 'Active' : 'Inactive'; ?></p>
                        <button class="button button-small" type="submit">Save changes</button>
                    </form>
                    <form method="post" action="index.php?page=settings" class="inline-form">
                        <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
                        <input type="hidden" name="action" value="toggle_service">
                        <input type="hidden" name="service_id" value="<?php echo (int) $service['id']; ?>">
                        <button class="button button-small" type="submit"><?php echo $service['active'] ? 'Deactivate' : 'Activate'; ?></button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</section>
