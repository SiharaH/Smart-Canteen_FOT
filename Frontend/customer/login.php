<?php
session_start();

// DB connection
$host = "localhost";
$user = "root";
$pass = "801@Vihanga";
$dbname = "smart_canteen";

$conn = new mysqli($host,$user,$pass,$dbname);
if ($conn->connect_error) die("Connection failed: ".$conn->connect_error);

// Collect data
$email = trim($_POST['L_email'] ?? '');
$password = trim($_POST['L_password'] ?? '');

if(empty($email) || empty($password)) {
    echo "<script>alert('Please fill in all fields'); window.history.back();</script>";
    exit();
}

// Lookup user
$sql = "SELECT * FROM users WHERE email=? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 1){
    $user = $result->fetch_assoc();

    // Plain text password check (replace with password_verify if hashed)
    if($password === $user['password']){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        echo "<script>alert('Login successful!'); window.location='https://www.youtube.com';</script>";
        exit();
    } else {
        echo "<script>alert('Incorrect password'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('No user found with this email'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
