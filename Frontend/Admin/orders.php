<?php
$mysqli = new mysqli("localhost", "root", "1234", "canteen");
if ($mysqli->connect_error) die("Connection failed: " . $mysqli->connect_error);

// Fetch orders with menu details
$sql = "SELECT o.id, o.customer_name, m.item_name, o.quantity, o.status, o.order_time 
        FROM orders o 
        JOIN menu m ON o.item_id = m.id 
        ORDER BY o.order_time DESC";
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Orders Management</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
body { font-family:'Roboto',sans-serif; margin:0; background:#f4f7f9; }
.container { width:95%; 
             max-width:1200px; 
             margin:20px auto; }
table { width:100%; 
        border-collapse:collapse; 
        background:#fff; 
        border-radius:10px; 
        overflow:hidden; 
        box-shadow:0 5px 15px rgba(0,0,0,0.1);}

th,td { padding:12px; 
        text-align:center; 
        border-bottom:1px solid #ddd; }

th { background:#1abc9c; 
     color:#fff; }
     
.status-btn { padding:5px 10px; border:none; border-radius:5px; cursor:pointer; color:#fff;}
.pending { background:#f1c40f; }
.preparing { background:#3498db; }
.ready { background:#e67e22; }
.completed { background:#2ecc71; }
</style>
</head>
<body>

<div class="container">
    <h2>🍽️ Orders Management</h2>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Change Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['customer_name'] ?></td>
                <td><?= $row['item_name'] ?></td>
                <td><?= $row['quantity'] ?></td>
                <td class="<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></td>
                <td>
                    <form method="POST" action="update_order_status.php">
                        <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                        <select name="status">
                            <option value="pending" <?= $row['status']=='pending'?'selected':'' ?>>Pending</option>
                            <option value="preparing" <?= $row['status']=='preparing'?'selected':'' ?>>Preparing</option>
                            <option value="ready" <?= $row['status']=='ready'?'selected':'' ?>>Ready</option>
                            <option value="completed" <?= $row['status']=='completed'?'selected':'' ?>>Completed</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
