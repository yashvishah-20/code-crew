<?php
require 'config.php';
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


$stmt = $pdo->prepare("SELECT * FROM user_account WHERE id=?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch();


if ($currentUser['role'] !== 'Admin') {
    die("Access denied.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $userId = $_POST['user_id'];
    $role = $_POST['role'];
    $managerId = !empty($_POST['manager_id']) ? $_POST['manager_id'] : NULL;

    $stmt = $pdo->prepare("UPDATE user_account SET role=?, manager_id=? WHERE id=?");
    $stmt->execute([$role, $managerId, $userId]);
}


$users = $pdo->query("SELECT * FROM user_account")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Users</title>
<style>
body { font-family: Arial; background:#f4f6f9; }
.container { width: 80%; margin: auto; padding:20px; }
table { width:100%; border-collapse: collapse; margin-top:20px; }
th, td { border:1px solid #ccc; padding:10px; text-align:center; }
th { background:#007bff; color:white; }
button { padding:5px 10px; background:#28a745; color:white; border:none; cursor:pointer; }
button:hover { background:#218838; }
</style>
</head>
<body>
<div class="container">
    <h2>Manage Users</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Current Role</th>
            <th>Manager</th>
            <th>Actions</th>
        </tr>
        <?php foreach($users as $u): ?>
        <tr>
            <form method="post">
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td>
                    <select name="role">
                        <option value="Employee" <?= $u['role']=='Employee'?'selected':'' ?>>Employee</option>
                        <option value="Manager" <?= $u['role']=='Manager'?'selected':'' ?>>Manager</option>
                        <option value="Admin" <?= $u['role']=='Admin'?'selected':'' ?>>Admin</option>
                    </select>
                </td>
                <td>
                    <select name="manager_id">
                        <option value="">-- None --</option>
                        <?php foreach($users as $mgr): ?>
                            <?php if($mgr['role']=='Manager'): ?>
                                <option value="<?= $mgr['id'] ?>" <?= $u['manager_id']==$mgr['id']?'selected':'' ?>>
                                    <?= htmlspecialchars($mgr['email']) ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                    <button type="submit" name="update_role">Update</button>
                </td>
            </form>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>






