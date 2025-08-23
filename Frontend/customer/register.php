<?php
// Database config
$host = "localhost"; 
$user = "root";   // change if needed
$pass = "801@Vihanga";       // add your password if MySQL has one
$dbname = "smart_canteen";

// Connect to MySQL
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$cpassword = $_POST['cpassword'];

// Validate password match
if ($password !== $cpassword) {
    die("Passwords do not match!");
}

// Hash password before saving
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$sql = "INSERT INTO `users` (`name`, `email`, `password`) VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $email, $hashedPassword);

if ($stmt->execute()) {
    echo "✅ Registration successful!";
} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
