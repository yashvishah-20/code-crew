<?php
require 'config.php';

$user = current_user($pdo);
if (!$user || $user['role'] != 'ADMIN') {
    die("Access denied.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'] ?? '';

    if ($action === 'add') {
        if (!$name || !$email || !$role || !$password) {
            $msg = "All fields are required for adding a user.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO user_account (company_id,name,email,password_hash,role) VALUES (?,?,?,?,?)");
            $stmt->execute([$user['company_id'],$name,$email,$password_hash,$role]);
            $msg = "User added successfully.";
        }
    }

    if ($action === 'edit') {
        $edit_id = intval($_POST['user_id']);
        if (!$name || !$email || !$role) {
            $msg = "All fields are required for editing.";
        } else {
            if ($password) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE user_account SET name=?, email=?, role=?, password_hash=? WHERE id=? AND company_id=?");
                $stmt->execute([$name,$email,$role,$password_hash,$edit_id,$user['company_id']]);
            } else {
                $stmt = $pdo->prepare("UPDATE user_account SET name=?, email=?, role=? WHERE id=? AND company_id=?");
                $stmt->execute([$name,$email,$role,$edit_id,$user['company_id']]);
            }
            $msg = "User updated successfully.";
        }
    }
}


if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    if ($delete_id != $user['id']) { 
        $stmt = $pdo->prepare("DELETE FROM user_account WHERE id=? AND company_id=?");
        $stmt->execute([$delete_id, $user['company_id']]);
        $msg = "User deleted successfully.";
    } else {
        $msg = "Cannot delete your own account.";
    }
}


$stmt = $pdo->prepare("SELECT * FROM user_account WHERE company_id=?");
$stmt->execute([$user['company_id']]);
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
</head>
<body>
<h2>Manage Users</h2>
<?php if(!empty($msg)) echo "<p style='color:green'>$msg</p>"; ?>

<h3>Add New User</h3>
<form method="post">
    <input type="hidden" name="action" value="add">
    Name:<br><input type="text" name="name"><br>
    Email:<br><input type="email" name="email"><br>
    Role:<br>
    <select name="role">
        <option value="EMPLOYEE">Employee</option>
        <option value="MANAGER">Manager</option>
    </select><br>
    Password:<br><input type="password" name="password"><br><br>
    <button type="submit">Add User</button>
</form>

<h3>Existing Users</h3>
<table border="1" cellpadding="5">
<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr>
<?php foreach($users as $u): ?>
<tr>
    <td><?php echo $u['id']; ?></td>
    <td><?php echo htmlspecialchars($u['name']); ?></td>
    <td><?php echo htmlspecialchars($u['email']); ?></td>
    <td><?php echo $u['role']; ?></td>
    <td>
        <?php if($u['id'] != $user['id']): ?>
        <form method="post" style="display:inline">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
            Name: <input type="text" name="name" value="<?php echo htmlspecialchars($u['name']); ?>">
            Email: <input type="email" name="email" value="<?php echo htmlspecialchars($u['email']); ?>">
            Role: 
            <select name="role">
                <option value="EMPLOYEE" <?php if($u['role']=='EMPLOYEE') echo 'selected'; ?>>Employee</option>
                <option value="MANAGER" <?php if($u['role']=='MANAGER') echo 'selected'; ?>>Manager</option>
            </select>
            Password: <input type="password" name="password" placeholder="Leave blank to keep">
            <button type="submit">Update</button>
        </form>
        <a href="manage_users.php?delete=<?php echo $u['id']; ?>" onclick="return confirm('Delete user?')">Delete</a>
        <?php else: ?>
        N/A
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

<p><a href="index.php">Back to Dashboard</a></p>
</body>
</html>
