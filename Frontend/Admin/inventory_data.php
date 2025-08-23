<?php
$mysqli = new mysqli("localhost", "root", "1234", "canteen");
if($mysqli->connect_error) die("Connection failed: ".$mysqli->connect_error);

$threshold = 10; // Low stock threshold
$res = $mysqli->query("SELECT item_name, stock_quantity, unit FROM inventory WHERE stock_quantity <= $threshold");

$low_stock = [];
while($row = $res->fetch_assoc()){
    $low_stock[] = $row;
}

echo json_encode($low_stock);
?>
