    <?php
    require 'config.php';

    if (!is_logged_in()) {
        redirect('login.php');
    }

    $user = current_user($pdo);
    ?>
    <!DOCTYPE html>
    <html>
    <head><title>Dashboard</title></head>
    <body>
    <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
    <p>Your role: <?php echo $user['role']; ?></p>

    <?php if($user['role']=='ADMIN'): ?>
        <p><a href="manage_users.php">Manage Users</a></p>
    <?php elseif($user['role']=='MANAGER'): ?>
        <p><a href="approvals.php">View Approvals</a></p>
    <?php else: ?>
        <p><a href="submit_expense.php">Submit Expense</a></p>
    <?php endif; ?>

    <p><a href="logout.php">Logout</a></p>
    </body>
    </html>
