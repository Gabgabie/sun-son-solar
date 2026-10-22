<section class="page-heading">
    <p>SERVICE HISTORY</p>
    <h1>Your service history</h1>
    <p>Track your bookings, change the date or time of pending requests, and review recorded payments.</p>
</section>
<section class="feature">
    <?php $s=$conn->prepare('SELECT id,service_type,preferred_date,preferred_time,status,created_at,admin_notes FROM service_requests WHERE customer_id=? ORDER BY created_at DESC');$s->bind_param('i',$_SESSION['user_id']);$s->execute();$rs=$s->get_result();if($rs->num_rows): ?>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Request #</th>
                <th>Service</th>
                <th>Preferred date</th>
                <th>Status</th>
                <th>Team note</th>
                <th>Action</th>
            </tr>
            <?php while($r=$rs->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo (int)$r['id']; ?>
                </td>
                <td>
                    <?php echo h($r['service_type']); ?>
                </td>
                <td>
                    <?php echo h($r['preferred_date'].' '.($r['preferred_time']?:'')); ?>
                </td>
                <td>
                    <span class="status status-<?php echo strtolower($r['status']); ?>">
                        <?php echo h($r['status']); ?>
                    </span>
                </td>
                <td>
                    <?php echo h($r['admin_notes']?:'—'); ?>
                </td>
                <td>
                    <?php if($r['status']==='Pending'): ?>
                    <details>
                        <summary>Change schedule</summary>
                        <form method="post" action="index.php?page=history">
                            <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
                            <input type="hidden" name="action" value="update_request">
                            <input type="hidden" name="request_id" value="<?php echo (int)$r['id']; ?>">
                            <label>Date<input type="date" name="preferred_date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo h($r['preferred_date']); ?>" required></label>
                            <label>Time<input type="time" name="preferred_time" value="<?php echo h(substr($r['preferred_time'] ?? '', 0, 5)); ?>"></label>
                            <button class="button button-small" type="submit">Save schedule</button>
                        </form>
                    </details>
                    <form method="post" action="index.php?page=history">
                        <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
                        <input type="hidden" name="action" value="cancel_request">
                        <input type="hidden" name="request_id" value="<?php echo (int)$r['id']; ?>">
                        <button class="button button-small button-danger" type="submit">Cancel request</button>
                    </form>
                    <?php else: ?>—<?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <?php else: ?>
    <p>No history yet. <a href="index.php?page=request">Request your first service</a>.</p>
    <?php endif;$s->close(); ?>
    <h2>Payments & receipts</h2>
    <?php $p=$conn->prepare('SELECT p.amount,p.payment_method,p.status,p.receipt_number,p.paid_at,s.service_type FROM payments p JOIN service_requests s ON s.id=p.service_request_id WHERE s.customer_id=? ORDER BY COALESCE(p.paid_at,s.created_at) DESC');$p->bind_param('i',$_SESSION['user_id']);$p->execute();$payments=$p->get_result();if($payments->num_rows): ?>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Service</th>
                <th>Amount</th>
                <th>Payment method</th>
                <th>Status</th>
                <th>Receipt</th>
                <th>Date</th>
            </tr>
            <?php while($r=$payments->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php echo h($r['service_type']); ?>
                </td>
                <td>₱<?php echo number_format((float)$r['amount'],2); ?>
                </td>
                <td>
                    <?php echo h($r['payment_method']?:'—'); ?>
                </td>
                <td>
                    <?php echo h($r['status']); ?>
                </td>
                <td>
                    <?php echo h($r['receipt_number']?:'—'); ?>
                </td>
                <td>
                    <?php echo h($r['paid_at']?:'—'); ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <?php else: ?>
    <p class="muted">No payment records yet.</p>
    <?php endif;$p->close(); ?>
</section>
