<section class="form-card wide">
    <p>BOOK A SERVICE</p>
    <h1>Request a service</h1>
    <p>Choose your service and preferred appointment date. The team will confirm availability.</p>
    <form method="post" action="index.php?page=request">
        <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
        <div class="form-grid">
            <label>Service type *<select name="service_type" required>
                    <option value="">Choose a service</option>
                    <?php
                    $serviceCount = $conn->query('SELECT COUNT(*) AS total FROM service_catalog')->fetch_assoc();
                    if ((int) $serviceCount['total'] === 0) {
                        $conn->query("INSERT INTO service_catalog (name) VALUES
                            ('Consultation'),
                            ('Solar System Design'),
                            ('Permitting'),
                            ('Installation'),
                            ('Maintenance'),
                            ('Repair'),
                            ('Monitoring')");
                    }

                    $services = $conn->query('SELECT name, base_price FROM service_catalog WHERE active = 1 ORDER BY name');
                    while ($service = $services->fetch_assoc()):
                    ?>
                    <option value="<?php echo h($service['name']); ?>">
                        <?php
                        echo h($service['name']);
                        if ((float) $service['base_price'] > 0) {
                            echo ' - Base price: ' . number_format((float) $service['base_price'], 2);
                        }
                        ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </label>
            <label>Preferred date *<input type="date" name="preferred_date" min="<?php echo date('Y-m-d'); ?>" required>
            </label>
            <label>Preferred time<input type="time" name="preferred_time">
            </label>
            <label class="full">Notes / site details<textarea name="notes" rows="4" placeholder="Tell us about the property, equipment or issue">
                </textarea>
            </label>
        </div>
        <button class="button" type="submit">Submit request</button>
    </form>
</section>
