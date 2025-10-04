<?php
require 'config.php';
$user = current_user($pdo);

$company_id = $user['company_id'];

$stmt = $pdo->prepare("SELECT 
    COALESCE(SUM(amount),0) as total_expenses,
    SUM(CASE WHEN status='PENDING' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status='APPROVED' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status='REJECTED' THEN 1 ELSE 0 END) as rejected
    FROM expenses WHERE company_id=?");
$stmt->execute([$company_id]);
$stats = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM expenses WHERE user_id=? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user['id']]);
$recent_expenses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7faff;
            margin: 20px;
        }
        h2 {
            color: #333;
        }
        .cards {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .card {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            font-size: 18px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #f1f1f1;
        }
    </style>
</head>
<body>
<h2>Dashboard</h2>

<div class="cards">
    <div class="card">💰 Total Expenses<br>USD <?php echo number_format($stats['total_expenses'],2); ?></div>
    <div class="card">⏳ Pending Approval<br><?php echo $stats['pending']; ?></div>
    <div class="card">✅ Approved<br><?php echo $stats['approved']; ?></div>
    <div class="card">❌ Rejected<br><?php echo $stats['rejected']; ?></div>
</div>

<h3>Recent Expenses</h3>
<?php if(!$recent_expenses): ?>
    <p>No recent expenses found.</p>
<?php else: ?>
    <table>
        <tr><th>Date</th><th>Category</th><th>Amount</th><th>Status</th></tr>
        <?php foreach($recent_expenses as $exp): ?>
        <tr>
            <td><?php echo $exp['expense_date']; ?></td>
            <td><?php echo htmlspecialchars($exp['category']); ?></td>
            <td><?php echo $exp['amount']." ".$exp['currency']; ?></td>
            <td><?php echo $exp['status']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
</body>
</html>
