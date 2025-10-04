<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = trim($_POST['company_name']);
    $country = trim($_POST['country']);
    $default_currency = strtoupper(trim($_POST['default_currency']));
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!$company_name || !$country || !$default_currency || !$name || !$email || !$password) {
        $error = "All fields are required.";
    } else {
        try {
            $pdo->beginTransaction();

            
            $stmt = $pdo->prepare("INSERT INTO company (name,country,default_currency) VALUES (?,?,?)");
            $stmt->execute([$company_name, $country, $default_currency]);
            $company_id = $pdo->lastInsertId();

            
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO user_account (company_id,name,email,password_hash,role) VALUES (?,?,?,?, 'ADMIN')");
            $stmt->execute([$company_id, $name, $email, $password_hash]);
            $user_id = $pdo->lastInsertId();

            $pdo->commit();

            
            $_SESSION['user_id'] = $user_id;
            redirect('index.php');
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Signup failed: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Signup - Expense Man</title></head>
<body>
<h2>Signup - Create your Company + Admin account</h2>
<?php if(!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
<form method="post">
    Company Name:<br><input type="text" name="company_name" value="My Company"><br>
    Country:<br><input type="text" name="country" value="India"><br>
    Default Currency (INR, USD...):<br><input type="text" name="default_currency" value="INR"><br>
    Your Name:<br><input type="text" name="name"><br>
    Email:<br><input type="email" name="email"><br>
    Password:<br><input type="password" name="password"><br><br>
    <button type="submit">Signup</button>
</form>
<p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
