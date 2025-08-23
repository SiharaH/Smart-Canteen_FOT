<?php
$mysqli = new mysqli("localhost", "root", "1234", "canteen");
if ($mysqli->connect_error) die("Connection failed: " . $mysqli->connect_error);

$last = $_GET['last'] ?? '1970-01-01 00:00:00';

$res = $mysqli->query("SELECT o.id, o.customer_name, o.quantity, m.item_name, o.order_time
                       FROM orders o
                       JOIN menu m ON o.item_id = m.id
                       WHERE o.order_time > '$last'
                       ORDER BY o.order_time ASC");

$new_orders = [];
while($row = $res->fetch_assoc()){
    $new_orders[] = $row;
}

echo json_encode($new_orders);
?>
