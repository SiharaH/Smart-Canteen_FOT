<?php
$mysqli = new mysqli("localhost", "root", "1234", "canteen");
if ($mysqli->connect_error) die("Connection failed: " . $mysqli->connect_error);

if(isset($_POST['order_id'], $_POST['status'])){
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    $stmt = $mysqli->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si",$status,$order_id);
    $stmt->execute();
}
header("Location: orders.php");
exit;
?>
