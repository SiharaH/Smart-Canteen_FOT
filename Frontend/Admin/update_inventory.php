<?php
$mysqli = new mysqli("localhost", "root", "1234", "canteen");
if ($mysqli->connect_error) die("Connection failed: " . $mysqli->connect_error);

if (isset($_POST['id'], $_POST['quantity'])) {
    $id = intval($_POST['id']);
    $quantity = intval($_POST['quantity']);
    $stmt = $mysqli->prepare("UPDATE inventory SET stock_quantity=? WHERE id=?");
    $stmt->bind_param("ii", $quantity, $id);
    $stmt->execute();
}
header("Location: inventory.php");
exit;
?>
