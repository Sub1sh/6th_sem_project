<?php
session_start();
include_once(__DIR__ . "/connection.php");

// Initialize variables
$success = false;
$error = '';
$name = $email = $phone = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    
    // Validation
    if (empty($name)) {
        $errors[] = "Full name is required";
    } elseif (strlen($name) < 3) {
        $errors[] = "Full name must be at least 3 characters";
    }
    
    if (empty($email)) {
        $errors[] = "Email address is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        $errors[] = "Please enter a valid phone number (10-15 digits)";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // Check if email already exists
    if (empty($errors) && isset($conn) && $conn) {
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        
        if ($check_stmt) {
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_stmt->store_result();
            
            if ($check_stmt->num_rows > 0) {
                $errors[] = "Email already registered. Please use another email or login.";
            }
            $check_stmt->close();
        }
    }
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $created_at = date('Y-m-d H:i:s');
        
        // Using your actual database column names
        $sql = "INSERT INTO users (name, email, phone, password, created_at) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sssss", $name, $email, $phone, $hashed_password, $created_at);
            
            if ($stmt->execute()) {
                $success = true;
                $_SESSION['signup_success'] = "✓ Account created successfully! Please login with your credentials.";
                // Redirect to login page after 2 seconds
                echo '<!DOCTYPE html>
                <html>
                <head>
                    <meta http-equiv="refresh" content="2;url=loging.php">
                    <style>
                        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f4f4f4; }
                        .message { background: white; padding: 30px; border-radius: 10px; max-width: 500px; margin: auto; }
                        .success { color: green; font-size: 50px; }
                    </style>
                </head>
                <body>
                    <div class="message">
                        <div class="success">✓</div>
                        <h2>Registration Successful!</h2>
                        <p>Your account has been created successfully.</p>
                        <p>Redirecting to login page...</p>
                        <a href="loging.php">Click here if not redirected</a>
                    </div>
                </body>
                </html>';
                exit();
            } else {
                $error = "Registration failed: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Travel_X | Create Account</title>
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

        .signup-container {
            max-width: 550px;
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

        .signup-header {
            background: linear-gradient(135deg, #1f6e43, #2b9b5e);
            padding: 2rem;
            text-align: center;
            color: white;
        }

        .signup-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .signup-header p {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .signup-form {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
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

        .input-hint {
            font-size: 0.7rem;
            color: #888;
            margin-top: 0.25rem;
            display: block;
        }

        .password-strength {
            margin-top: 0.5rem;
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s, background 0.3s;
        }

        .strength-bar.weak { background: #e74c3c; width: 33%; }
        .strength-bar.medium { background: #f39c12; width: 66%; }
        .strength-bar.strong { background: #27ae60; width: 100%; }

        .btn-signup {
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

        .btn-signup:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(31, 110, 67, 0.3);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .login-link a {
            color: #2b9b5e;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error i {
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-header">
            <h1><i class="fas fa-user-plus"></i> Create Account</h1>
            <p>Fill in your details to get started</p>
        </div>

        <div class="signup-form">
            <?php if (!empty($error)): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <div><?php echo $error; ?></div>
            </div>
            <?php endif; ?>

            <form method="POST" action="" id="signupForm">
                <div class="form-group">
                    <label>Full Name <span class="required">*</span></label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" 
                           placeholder="Enter your full name" required>
                    <span class="input-hint"><i class="fas fa-user"></i> Enter your full name</span>
                </div>

                <div class="form-group">
                    <label>Email Address <span class="required">*</span></label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" 
                           placeholder="Enter your email" required>
                    <span class="input-hint"><i class="fas fa-envelope"></i> Enter your email address</span>
                </div>

                <div class="form-group">
                    <label>Phone Number <span class="required">*</span></label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($phone); ?>" 
                           placeholder="Enter your phone number" required>
                    <span class="input-hint"><i class="fas fa-phone"></i> Enter your phone number</span>
                </div>

                <div class="form-group">
                    <label>Password <span class="required">*</span></label>
                    <input type="password" name="password" id="password" 
                           placeholder="Create a password" required>
                    <div class="password-strength">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <span class="input-hint"><i class="fas fa-lock"></i> Minimum 6 characters</span>
                </div>

                <div class="form-group">
                    <label>Confirm Password <span class="required">*</span></label>
                    <input type="password" name="confirm_password" id="confirmPassword" 
                           placeholder="Confirm your password" required>
                    <span class="input-hint" id="passwordMatchHint"></span>
                </div>

                <button type="submit" class="btn-signup">
                    <i class="fas fa-user-plus"></i> Sign Up
                </button>

                <div class="loging-link">
                    Already have an account? <a href="loging.php">Login here</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirmPassword');
        const strengthBar = document.getElementById('strengthBar');
        const matchHint = document.getElementById('passwordMatchHint');

        function checkPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            strengthBar.className = 'strength-bar';
            if (strength <= 2) {
                strengthBar.classList.add('weak');
                return 'Weak';
            } else if (strength <= 4) {
                strengthBar.classList.add('medium');
                return 'Medium';
            } else {
                strengthBar.classList.add('strong');
                return 'Strong';
            }
        }

        passwordInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                checkPasswordStrength(this.value);
            } else {
                strengthBar.style.width = '0%';
            }
            checkPasswordMatch();
        });

        function checkPasswordMatch() {
            if (confirmInput.value.length > 0) {
                if (passwordInput.value === confirmInput.value) {
                    matchHint.innerHTML = '<i class="fas fa-check-circle" style="color:#27ae60;"></i> ✓ Passwords match!';
                    matchHint.style.color = '#27ae60';
                } else {
                    matchHint.innerHTML = '<i class="fas fa-times-circle" style="color:#e74c3c;"></i> ✗ Passwords do not match';
                    matchHint.style.color = '#e74c3c';
                }
            } else {
                matchHint.innerHTML = '';
            }
        }

        confirmInput.addEventListener('input', checkPasswordMatch);

        document.getElementById('signupForm').addEventListener('submit', function(e) {
            if (passwordInput.value !== confirmInput.value) {
                e.preventDefault();
                alert('❌ Passwords do not match. Please check and try again.');
            }
        });
    </script>
</body>
</html>