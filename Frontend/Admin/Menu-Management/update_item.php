<?php
header('Content-Type: application/json');

// Database connection
$conn = mysqli_connect("localhost", "root", "1234", "canteen");
if (!$conn) {
    echo json_encode(["success" => false, "error" => mysqli_connect_error()]);
    exit;
}

// Read JSON request body
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'], $data['name'], $data['price'], $data['category'])) {
    $id = intval($data['id']);
    $name = mysqli_real_escape_string($conn, $data['name']);
    $price = floatval($data['price']);
    $category = mysqli_real_escape_string($conn, $data['category']);
    $description = mysqli_real_escape_string($conn, $data['description'] ?? "");

    $sql = "UPDATE menu_items 
            SET name='$name', price=$price, category='$category', description='$description' 
            WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Invalid input"]);
}
?>
