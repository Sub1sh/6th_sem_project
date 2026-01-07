<?php
// Get data from form
$user  = $_POST["username"] ?? '';
$mail  = $_POST["email"] ?? '';
$phone = $_POST["phone"] ?? '';
$pass  = $_POST["password"] ?? '';

// Basic input validation
if (empty($user) || empty($mail) || empty($phone) || empty($pass)) {
    die("All fields are required.");
}

// Hash password for security
$hashedPass = password_hash($pass, PASSWORD_DEFAULT);

// Create database connection
$con = mysqli_connect("localhost", "root", "", "registration");

if (!$con) {
    die("Connection Failed: " . mysqli_connect_error());
}

// Prepare statement to prevent SQL injection
$stmt = $con->prepare("INSERT INTO register (username, email, phone, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $user, $mail, $phone, $hashedPass);

// Execute query
if ($stmt->execute()) {
    echo "Successfully Saved!";
} else {
    echo "Save failed: " . $stmt->error;
}

// Close connections
$stmt->close();
$con->close();
?>
