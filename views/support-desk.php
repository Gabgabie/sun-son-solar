<section class="page-heading">
    <p>CUSTOMER CARE</p>
    <h1>Support inbox</h1>
    <p>Review customer questions and update each ticket as your team follows up.</p>
</section>
<section class="feature">
    <?php $q=$conn->query('SELECT t.id,t.subject,t.message,t.status,t.created_at,u.first_name,u.last_name,u.email FROM support_tickets t JOIN users u ON u.id=t.customer_id ORDER BY FIELD(t.status,"Open","In Progress","Closed"),t.created_at DESC'); if($q->num_rows): ?>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Customer</th>
                <th>Ticket</th>
                <th>Submitted</th>
                <th>Status</th>
                <th>Update</th>
            </tr>
            <?php while($r=$q->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php echo h($r['first_name'].' '.$r['last_name']); ?>
                    <small class="block">
                        <?php echo h($r['email']); ?>
                    </small>
                </td>
                <td>
                    <strong>
                        <?php echo h($r['subject']); ?>
                    </strong>
                    <p>
                        <?php echo nl2br(h($r['message'])); ?>
                    </p>
                </td>
                <td>
                    <?php echo h($r['created_at']); ?>
                </td>
                <td>
                    <?php echo h($r['status']); ?>
                </td>
                <td>
                    <form method="post" action="index.php?page=support-desk">
                        <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
                        <input type="hidden" name="ticket_id" value="<?php echo (int)$r['id']; ?>">
                        <label>Status<select name="status">
                                <?php foreach(['Open','In Progress','Closed'] as $status): ?>
                                <option <?php echo $r['status']===$status?'selected':''; ?>>
                                    <?php echo h($status); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <button class="button button-small" type="submit">Save</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <?php else: ?>
    <p>No support tickets yet.</p>
    <?php endif; ?>
</section>
