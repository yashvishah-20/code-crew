<?php
require 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!$email || !$password) {
        $error = "Enter both email and password.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM user_account WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    header("Location: dashboard.php");  // ✅ redirect to your dashboard
    exit;
}

        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Expense Man</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f6f9; }
        .login-box {
            width: 350px; margin: 100px auto; padding: 20px;
            background: #fff; border: 1px solid #ddd; border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color:#333; }
        input, button { width: 100%; padding: 10px; margin: 8px 0; }
        button {
            background: #007bff; color: white; border: none; border-radius: 5px;
            cursor: pointer;
        }
        button:hover { background: #0056b3; }
        .error { color: red; text-align:center; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login</h2>
        <?php if(!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <label>Email:</label>
            <input type="email" name="email" required>
            
            <label>Password:</label>
            <input type="password" name="password" required>
            
            <button type="submit">Login</button>
        </form>
        <p style="text-align:center;">Don't have an account? 
           <a href="signup.php">Signup here</a></p>
    </div>
</body>
</html>
