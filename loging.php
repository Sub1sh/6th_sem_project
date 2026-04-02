<?php
session_start();
include_once(__DIR__ . "/connection.php");

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: homepage.php");
    exit();
}

$error = '';
$success = '';

// Check for success message from signup
if (isset($_SESSION['signup_success'])) {
    $success = $_SESSION['signup_success'];
    unset($_SESSION['signup_success']);
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password";
    } else {
        // Query user from database
        $sql = "SELECT id, name, email, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                // Redirect to homepage
                header("Location: homepage.php");
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Travel_X | Vehicle Rental Service</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            max-width: 450px;
            width: 100%;
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: fadeInUp 0.5s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, #1f6e43, #2b9b5e);
            padding: 2rem;
            text-align: center;
            color: white;
        }

        .login-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .login-form {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .form-group label .required {
            color: #e74c3c;
        }

        .form-group input {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.3s;
            outline: none;
        }

        .form-group input:focus {
            border-color: #2b9b5e;
            box-shadow: 0 0 0 3px rgba(43, 155, 94, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #1f6e43, #2b9b5e);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 0.5rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(31, 110, 67, 0.3);
        }

        .signup-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .signup-link a {
            color: #2b9b5e;
            text-decoration: none;
            font-weight: 600;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        .admin-link {
            text-align: center;
            margin-top: 1rem;
            padding: 0.8rem;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .admin-link a {
            color: #1a1a2e;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .admin-link a:hover {
            color: #eab308;
        }

        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert i {
            font-size: 1.2rem;
        }

        .home-link {
            display: inline-block;
            margin-top: 1rem;
            text-align: center;
            width: 100%;
            color: #888;
            font-size: 0.85rem;
            text-decoration: none;
        }

        .home-link:hover {
            color: #2b9b5e;
        }

        .demo-credentials {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 12px;
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
            border: 1px dashed #dee2e6;
        }

        .demo-credentials p {
            margin: 5px 0;
            color: #666;
        }

        .demo-credentials strong {
            color: #1f6e43;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-sign-in-alt"></i> Welcome Back</h1>
            <p>Login to access your account</p>
        </div>

        <div class="login-form">
            <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <div><?php echo htmlspecialchars($success); ?></div>
            </div>
            <?php endif; ?>

            <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <div><?php echo htmlspecialchars($error); ?></div>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Email Address <span class="required">*</span></label>
                    <input type="email" name="email" placeholder="Enter your email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Password <span class="required">*</span></label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>

                <div class="signup-link">
                    Don't have an account? <a href="signUp.php">Create one here</a>
                </div>

                <!-- Admin Login Link - ADDED HERE -->
                <div class="admin-link">
                    <a href="adminLogin.php">
                        <i class="fas fa-user-shield"></i> Admin Login
                    </a>
                </div>
            </form>

            <div class="demo-credentials">
                <p><i class="fas fa-info-circle"></i> <strong>Demo Credentials</strong></p>
                <p>Email: <strong>user@example.com</strong> | Password: <strong>123456</strong></p>
                <p style="font-size: 11px; color: #999;">(Use after you create an account)</p>
            </div>
        </div>
    </div>
</body>
</html>