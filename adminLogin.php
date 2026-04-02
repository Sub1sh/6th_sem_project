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

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', Arial, sans-serif;
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
            width: 400px;
            background: #ffffff;
            padding: 40px 35px;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            text-align: center;
            animation: fadeIn 0.6s ease;
        }

        .admin-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .admin-icon i {
            font-size: 2rem;
            color: #eab308;
        }

        .admin-login-box h2 {
            color: #222;
            margin-bottom: 5px;
            font-size: 28px;
            font-weight: 700;
        }

        .admin-login-box .subtitle {
            font-size: 14px;
            color: #777;
            margin-bottom: 30px;
        }

        .admin-login-box input {
            width: 100%;
            padding: 14px 18px;
            margin-bottom: 18px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            outline: none;
            transition: 0.3s;
            font-family: inherit;
        }

        .admin-login-box input:focus {
            border-color: #eab308;
            box-shadow: 0 0 0 3px rgba(234, 179, 8, 0.1);
        }

        .admin-login-box button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            color: white;
            cursor: pointer;
            transition: 0.3s;
        }

        .admin-login-box button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .admin-login-box .back-home {
            display: inline-block;
            margin-top: 20px;
            font-size: 14px;
            text-decoration: none;
            color: #666;
            transition: 0.3s;
        }

        .admin-login-box .back-home:hover {
            color: #eab308;
        }

        .user-login-link {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .user-login-link a {
            color: #1f6e43;
            text-decoration: none;
            font-weight: 600;
        }

        .user-login-link a:hover {
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
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
        <div class="admin-icon">
            <i class="fas fa-user-shield"></i>
        </div>
        <h2>Admin Login</h2>
        <p class="subtitle">Enter your credentials to access dashboard</p>

        <form method="POST">
            <input type="text" name="Admin_username" placeholder="Admin Username" required>
            <input type="password" name="Admin_password" placeholder="Admin Password" required>

            <button type="submit" name="Adminlogin">
                <i class="fas fa-sign-in-alt"></i> Login as Admin
            </button>
        </form>


        <div class="user-login-link">
            <a href="loging.php">
                <i class="fas fa-user"></i> User Login
            </a>
        </div>
    </div>
</div>

</body>
</html>