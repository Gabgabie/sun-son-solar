<section class="page-heading">
    <p>ADMINISTRATION</p>
    <h1>User management</h1>
    <p>Administrators and HR can create and manage employee accounts.</p>
</section>
<section class="form-card wide">
    <h2>Create a staff account</h2>
    <form method="post" action="index.php?page=users">
        <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
        <input type="hidden" name="action" value="create_employee">
        <div class="form-grid">
            <label>First name *<input name="first_name" required>
            </label>
            <label>Last name *<input name="last_name" required>
            </label>
            <label>Middle name<input name="middle_name">
            </label>
            <label>Birthdate<input type="date" name="birthdate" max="<?php echo date('Y-m-d'); ?>">
            </label>
            <label>Gender<select name="gender">
                    <option value="">Select (optional)</option>
                    <option>Female</option>
                    <option>Male</option>
                    <option>Prefer not to say</option>
                </select>
            </label>
            <label>Email *<input type="email" name="email" required>
            </label>
            <label>Phone number<input name="phone">
            </label>
            <label class="full">Address<input name="address">
            </label>
            <label>Username *<input name="username" required minlength="3">
            </label>
            <label>Temporary password *<input type="password" name="password" minlength="8" required>
            </label>
            <label>Department *<select name="department" required>
                    <?php foreach(['Administration','IT','Dispatch','Accounting','HR','Marketing','Sales','Customer Service'] as $d): ?>
                    <option>
                        <?php echo h($d); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Account role<select name="role">
                    <option value="technician">Technician</option>
                    <option value="dispatcher">Dispatcher</option>
                    <option value="hr">HR</option>
                    <option value="accounting">Accounting</option>
                    <option value="sales">Sales</option>
                    <option value="customer_service">Customer service</option>
                    <option value="marketing">Marketing</option>
                    <option value="admin">Administrator</option>
                </select>
            </label>
        </div>
        <p class="muted">Temporary passwords are hashed, and employees must change them at first login.</p>
        <button class="button" type="submit">Create staff account</button>
    </form>
</section>
<section class="feature">
    <h2>All accounts</h2>
    <?php $q=$conn->query('SELECT id,first_name,last_name,middle_name,birthdate,gender,email,phone,address,username,role,department,active,created_at FROM users ORDER BY created_at DESC'); ?>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Role</th>
                <th>Department</th>
                <th>Status</th>
                <th>Action</th>
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
                    <?php echo h($r['username']); ?>
                </td>
                <td>
                    <?php echo h($r['role']); ?>
                </td>
                <td>
                    <?php echo h($r['department']?:'—'); ?>
                </td>
                <td>
                    <?php echo $r['active']?'Active':'Inactive'; ?>
                </td>
                <td>
                    <?php if((int)$r['id']!==(int)$_SESSION['user_id']): ?>
                    <form class="inline-form" method="post" action="index.php?page=users">
                        <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
                        <input type="hidden" name="action" value="toggle_user">
                        <input type="hidden" name="user_id" value="<?php echo (int)$r['id']; ?>">
                        <button class="button button-small <?php echo $r['active']?'button-danger':''; ?>" type="submit">
                            <?php echo $r['active']?'Deactivate':'Activate'; ?>
                        </button>
                    </form>
                    <?php else: ?>
                    <small>Current account</small>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <details class="account-edit">
                        <summary>Edit <?php echo h($r['first_name'] . ' ' . $r['last_name']); ?></summary>
                        <form method="post" action="index.php?page=users">
                            <input type="hidden" name="csrf" value="<?php echo h(csrfToken()); ?>">
                            <input type="hidden" name="action" value="update_user">
                            <input type="hidden" name="user_id" value="<?php echo (int) $r['id']; ?>">
                            <div class="form-grid">
                                <label>First name<input name="first_name" value="<?php echo h($r['first_name']); ?>" required></label>
                                <label>Last name<input name="last_name" value="<?php echo h($r['last_name']); ?>" required></label>
                                <label>Middle name<input name="middle_name" value="<?php echo h($r['middle_name'] ?? ''); ?>"></label>
                                <label>Birthdate<input type="date" name="birthdate" value="<?php echo h($r['birthdate'] ?? ''); ?>"></label>
                                <label>Gender<input name="gender" value="<?php echo h($r['gender'] ?? ''); ?>"></label>
                                <label>Email<input type="email" name="email" value="<?php echo h($r['email']); ?>" required></label>
                                <label>Role
                                    <select name="role">
                                        <?php foreach (['customer', 'admin', 'hr', 'dispatcher', 'technician', 'accounting', 'sales', 'customer_service', 'marketing'] as $role): ?>
                                        <option value="<?php echo h($role); ?>" <?php echo $r['role'] === $role ? 'selected' : ''; ?>><?php echo h($role); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>
                                <label>Department
                                    <select name="department">
                                        <option value="">No department</option>
                                        <?php foreach (['Administration','IT','Dispatch','Accounting','HR','Marketing','Sales','Customer Service'] as $department): ?>
                                        <option value="<?php echo h($department); ?>" <?php echo $r['department'] === $department ? 'selected' : ''; ?>><?php echo h($department); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>
                                <label>Phone<input name="phone" value="<?php echo h($r['phone'] ?? ''); ?>"></label>
                                <label class="full">Address<input name="address" value="<?php echo h($r['address'] ?? ''); ?>"></label>
                            </div>
                            <button class="button button-small" type="submit">Save account</button>
                        </form>
                    </details>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</section>
