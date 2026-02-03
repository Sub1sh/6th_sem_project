<?php
session_start();

// Database connection (replace with your actual connection file or include connection.php)
$con = mysqli_connect("localhost", "root", "", "registration");

if (!$con) {
    die("Connection Failed: " . mysqli_connect_error());
}

// Function to generate random number
function random_num($length) {
    $text = "";
    if ($length < 5) {
        $length = 5;
    }
    $len = rand(4, $length);
    for ($i = 0; $i < $len; $i++) {
        $text .= rand(0, 9);
    }
    return $text;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user_name = $_POST['user_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $con_pass = $_POST['cpassword'] ?? '';

    // Validation
    if (empty($user_name) || empty($email) || empty($phone) || empty($password)) {
        echo "<script>alert('All fields are required');</script>";
        exit;
    }

    if ($password !== $con_pass) {
        echo "<script>alert('Passwords do not match');</script>";
        exit;
    }

    // Check if email already exists
    $check_email = mysqli_prepare($con, "SELECT email FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();
    
    if ($check_email->num_rows > 0) {
        echo "<script>alert('Email already exists');</script>";
        $check_email->close();
        exit;
    }
    $check_email->close();

    // Hash password
    $hashedPass = password_hash($password, PASSWORD_DEFAULT);
    $user_id = random_num(20);

    // Prepare and execute insert statement
    $stmt = $con->prepare("INSERT INTO users (user_id, user_name, email, phone, password) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("sssss", $user_id, $user_name, $email, $phone, $hashedPass);
        
        if ($stmt->execute()) {
            echo "<script>
                    alert('Signup successful!');
                    window.location.href='loging.php';
                  </script>";
        } else {
            echo "<script>alert('Signup failed: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Database error: " . $con->error . "');</script>";
    }
}

// Close connection at the end
$con->close();
?>