<?php
session_start();

// ADMIN LOGIN LOGIC
if (isset($_POST['Adminlogin'])) {

    $username = $_POST['Admin_username'];
    $password = $_POST['Admin_password'];

    // Hardcoded admin credentials (for project/demo)
    if ($username === "admin" && $password === "admin") {
        $_SESSION['username'] = "admin";
        header("Location: adminDash.php");
        exit();
    } else {
        echo "<script>alert('Incorrect Admin Username or Password');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | Travel_X</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- INLINE ATTRACTIVE CSS -->
    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        .admin-login-wrapper {
            height: 100vh;
            width: 100%;
            background: linear-gradient(
                rgba(0, 0, 0, 0.7),
                rgba(0, 0, 0, 0.7)
            ),
            url("image/background 2.jpg") center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .admin-login-box {
            width: 380px;
            background: #ffffff;
            padding: 35px 30px;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
            text-align: center;
            animation: fadeIn 0.6s ease;
        }

        .admin-login-box h2 {
            color: #222;
            margin-bottom: 5px;
            font-size: 26px;
        }

        .admin-login-box .subtitle {
            font-size: 14px;
            color: #777;
            margin-bottom: 25px;
        }

        .admin-login-box input {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            outline: none;
            transition: 0.3s;
        }

        .admin-login-box input:focus {
            border-color: #f9d806;
            box-shadow: 0 0 5px rgba(249, 216, 6, 0.6);
        }

        .admin-login-box button {
            width: 100%;
            padding: 12px;
            background: #f9d806;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .admin-login-box button:hover {
            background: #e6c600;
            transform: translateY(-2px);
        }

        .admin-login-box .back-home {
            display: inline-block;
            margin-top: 18px;
            font-size: 14px;
            text-decoration: none;
            color: #444;
        }

        .admin-login-box .back-home:hover {
            color: #f9d806;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<div class="admin-login-wrapper">
    <div class="admin-login-box">
        <h2>Admin Login</h2>
        <p class="subtitle">Authorized access only</p>

        <form method="POST">
            <input type="text" name="Admin_username" placeholder="Admin Username" required>
            <input type="password" name="Admin_password" placeholder="Admin Password" required>

            <button type="submit" name="Adminlogin">Login</button>
        </form>

        <a href="homepage.php" class="back-home">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
    </div>
</div>

</body>
</html>
