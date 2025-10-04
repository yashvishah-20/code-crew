<?php

$host = "localhost";
$user = "root"; 
$password = "";
$dbname = "expense_db";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_id = 1; 
    $amount = $_POST['amount'];
    $currency = $_POST['currency'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("INSERT INTO expenses (employee_id, amount, currency, category, description, date_submitted) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idssss", $employee_id, $amount, $currency, $category, $description, $date);

    if ($stmt->execute()) {
        echo "Expense submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<form method="POST" action="">
    Amount: <input type="number" step="0.01" name="amount" required><br>
    Currency: <input type="text" name="currency" value="USD" required><br>
    Category: <input type="text" name="category" required><br>
    Description: <textarea name="description"></textarea><br>
    Date: <input type="date" name="date" required><br>
    <input type="submit" value="Submit Expense">
</form>
