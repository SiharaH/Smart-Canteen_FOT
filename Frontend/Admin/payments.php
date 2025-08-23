<?php
$mysqli = new mysqli("localhost", "root", "1234", "canteen");
if ($mysqli->connect_error) die("Connection failed: " . $mysqli->connect_error);

// Total Revenue
$totalRevenue = $mysqli->query("SELECT SUM(amount) AS total FROM payments")->fetch_assoc()['total'];

// Today's Revenue
$todayRevenue = $mysqli->query("
    SELECT SUM(amount) AS today 
    FROM payments 
    WHERE DATE(payment_time) = CURDATE()
")->fetch_assoc()['today'];

// Fetch Payments
$result = $mysqli->query("SELECT * FROM payments ORDER BY payment_time DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payments & Sales Reports</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    body { font-family:'Roboto',sans-serif; background:#f4f7f9; margin:0; }
    .container { width:90%; max-width:1100px; margin:20px auto; }
    h2 { text-align:center; margin-bottom:20px; }
    .cards { display:flex; justify-content:space-around; margin-bottom:20px; }
    .card { background:#fff; padding:20px; border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.1); width:45%; text-align:center; }
    .card h3 { margin:0; color:#1abc9c; }
    table { width:100%; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
    th,td { padding:12px; text-align:center; border-bottom:1px solid #ddd; }
    th { background:#1abc9c; color:white; }
</style>
</head>
<body>
<div class="container">
    <h2>💳 Payments & Sales Reports</h2>

    <div class="cards">
        <div class="card">
            <h3>Total Revenue</h3>
            <p><strong>Rs. <?= number_format($totalRevenue, 2) ?></strong></p>
        </div>
        <div class="card">
            <h3>Today's Revenue</h3>
            <p><strong>Rs. <?= number_format($todayRevenue, 2) ?></strong></p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Order ID</th>
                <th>Amount (Rs)</th>
                <th>Method</th>
                <th>Payment Time</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['order_id'] ?></td>
                <td><?= number_format($row['amount'], 2) ?></td>
                <td><?= ucfirst($row['payment_method']) ?></td>
                <td><?= $row['payment_time'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
