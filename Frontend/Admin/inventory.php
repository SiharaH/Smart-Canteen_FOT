<?php
$mysqli = new mysqli("localhost", "root", "1234", "canteen");
if ($mysqli->connect_error) die("Connection failed: " . $mysqli->connect_error);

$result = $mysqli->query("SELECT * FROM inventory");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Inventory Management</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
body { font-family:'Roboto',sans-serif; background:#f4f7f9; margin:0; }
.container { width:90%; max-width:1100px; margin:20px auto; }
h2 { text-align:center; }
table { width:100%; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
th,td { padding:12px; text-align:center; border-bottom:1px solid #ddd; }
th { background:#1abc9c; color:white; }
.low-stock { background:#f8d7da; color:#721c24; font-weight:bold; }
form { display:inline-block; }
input[type=number] { width:60px; padding:5px; }
button { padding:5px 10px; border:none; background:#1abc9c; color:white; border-radius:5px; cursor:pointer; }
</style>
</head>
<body>
<div class="container">
    <h2>📦 Inventory Management</h2>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Update Stock</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr class="<?= $row['stock_quantity'] < 10 ? 'low-stock' : '' ?>">
                <td><?= $row['item_name'] ?></td>
                <td><?= $row['stock_quantity'] ?></td>
                <td><?= $row['unit'] ?></td>
                <td>
                    <form action="update_inventory.php" method="POST">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="number" name="quantity" value="<?= $row['stock_quantity'] ?>" min="0">
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
