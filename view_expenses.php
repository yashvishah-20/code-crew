<?php
$conn = new mysqli("localhost", "root", "", "expense_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$employee_id = 1; // Replace with session employee_id if using login system

$sql = "SELECT * FROM expenses WHERE employee_id = ? ORDER BY date_submitted DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Expense History</h2>";
echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Amount</th>
            <th>Currency</th>
            <th>Category</th>
            <th>Description</th>
            <th>Date</th>
            <th>Status</th>
        </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['amount']}</td>
            <td>{$row['currency']}</td>
            <td>{$row['category']}</td>
            <td>{$row['description']}</td>
            <td>{$row['date_submitted']}</td>
            <td>{$row['status']}</td>
          </tr>";
}

echo "</table>";

$stmt->close();
$conn->close();
?>
