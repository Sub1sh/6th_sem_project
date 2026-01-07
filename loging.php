<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/loging.css">
    <link rel="stylesheet" href="css/homepage.css">

    <title>Login | Travel_X</title>
</head>
<body>

<?php include("nav.php"); ?>

<?php
session_start();
include("connection.php");
include("function.php");

// ================= USER LOGIN =================
if ($_SERVER['REQUEST_METHOD'] == "POST" && !isset($_POST['Adminlogin'])) {
    $user_name = $_POST['user_name'];
    $password = $_POST['password'];
    if (!empty($user_name) && !empty($password)) {
        $query = "SELECT * FROM users WHERE user_name = '$user_name' LIMIT 1";
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            if ($user_data['password'] === $password) {
                $_SESSION['user_id'] = $user_data['user_id'];
                header("Location: forProfile.php");
                exit();
            }
        }
        echo "<script>alert('Wrong username or password');</script>";
    }
}

// ================= ADMIN LOGIN =================
if (isset($_POST['Adminlogin'])) {
    $username = $_POST['Admin_username'];
    $password = $_POST['Admin_password'];
    if ($username === "admin" && $password === "admin") {
        $_SESSION['username'] = "admin";
        header("Location: adminDash.php");
        exit();
    } else {
        echo "<script>alert('Incorrect Admin Username or Password');</script>";
    }
}
?>

<!-- Login Modal -->
<div class="login-form-container">
    <i class="fas fa-times" id="close-login-form"></i>

    <div class="form-box">
        <div class="button-box">
            <button type="button" class="toggle-btn" onclick="showUser()">Login as User</button>
            <button type="button" class="toggle-btn" onclick="showAdmin()">Login as Admin</button>
        </div>

        <!-- User Login Form -->
        <form id="userLogin" class="input-group" method="POST">
            <input type="text" class="input-field" placeholder="Username" name="user_name" required>
            <input type="password" class="input-field" placeholder="Password" name="password" required>
            <button type="submit" class="submit-btn">Login as User</button>
        </form>

        <!-- Admin Login Form -->
        <form id="adminLogin" class="input-group" method="POST" style="display:none;">
            <input type="text" class="input-field" placeholder="Admin Username" name="Admin_username" required>
            <input type="password" class="input-field" placeholder="Admin Password" name="Admin_password" required>
            <button type="submit" name="Adminlogin" class="submit-btn">Login as Admin</button>
        </form>
    </div>
</div>

<?php include("footer.php"); ?>

<!-- Swiper JS -->
<script src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>
<!-- Custom JS -->
<script src="js/loging.js"></script>

<script>
// Show User/Admin forms
function showUser() {
    document.getElementById("userLogin").style.display = "block";
    document.getElementById("adminLogin").style.display = "none";
}
function showAdmin() {
    document.getElementById("userLogin").style.display = "none";
    document.getElementById("adminLogin").style.display = "block";
}

// Open login modal from header
document.querySelectorAll('#login-btn, #login-btn button').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        document.querySelector('.login-form-container').classList.add('active');
        showUser(); // default to user login
    });
});

// Close login modal
document.getElementById('close-login-form').addEventListener('click', () => {
    document.querySelector('.login-form-container').classList.remove('active');
});
</script>

</body>
</html>
