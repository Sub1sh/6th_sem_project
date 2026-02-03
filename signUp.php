<?php
session_start();
require_once 'connection.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: homepage.php");
    exit();
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $license_number = trim($_POST['license_number'] ?? '');
    $terms = isset($_POST['terms']);
    
    // Validation
    if (empty($full_name)) {
        $errors['full_name'] = "Full name is required";
    } elseif (strlen($full_name) < 3) {
        $errors['full_name'] = "Full name must be at least 3 characters";
    }
    
    if (empty($username)) {
        $errors['username'] = "Username is required";
    } elseif (strlen($username) < 4) {
        $errors['username'] = "Username must be at least 4 characters";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = "Username can only contain letters, numbers and underscores";
    }
    
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }
    
    if (empty($phone)) {
        $errors['phone'] = "Phone number is required";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        $errors['phone'] = "Invalid phone number";
    }
    
    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors['password'] = "Password must contain at least one uppercase letter";
    } elseif (!preg_match('/[a-z]/', $password)) {
        $errors['password'] = "Password must contain at least one lowercase letter";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors['password'] = "Password must contain at least one number";
    }
    
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }
    
    if (!$terms) {
        $errors['terms'] = "You must agree to the terms and conditions";
    }
    
    // Check if username or email already exists
    if (empty($errors)) {
        $check_query = "SELECT user_id FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            // Check which one exists
            $stmt->bind_result($id);
            $stmt->fetch();
            $stmt->close();
            
            // Need to check separately which one exists
            $check_user = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
            $check_user->bind_param("s", $username);
            $check_user->execute();
            $check_user->store_result();
            
            if ($check_user->num_rows > 0) {
                $errors['username'] = "Username already taken";
            }
            $check_user->close();
            
            $check_email = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
            $check_email->bind_param("s", $email);
            $check_email->execute();
            $check_email->store_result();
            
            if ($check_email->num_rows > 0) {
                $errors['email'] = "Email already registered";
            }
            $check_email->close();
        }
        $stmt->close();
    }
    
    // If no errors, insert user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $insert_query = "INSERT INTO users (username, email, phone, password, full_name, address, license_number) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sssssss", $username, $email, $phone, $hashed_password, $full_name, $address, $license_number);
        
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            
            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['full_name'] = $full_name;
            $_SESSION['email'] = $email;
            $_SESSION['logged_in'] = true;
            
            $success = true;
            
            // Redirect after 3 seconds
            header("refresh:3;url=homepage.php");
        } else {
            $errors['database'] = "Registration failed. Please try again.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Travel_X Vehicle Rental</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/signup.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .signup-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            width: 100%;
            max-width: 1000px;
            min-height: 700px;
            display: flex;
        }

        .signup-left {
            flex: 1;
            background: linear-gradient(135deg, #4a6ee0 0%, #6a45a0 100%);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .signup-left::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.3;
            animation: float 20s linear infinite;
        }

        @keyframes float {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .signup-right {
            flex: 1.2;
            padding: 60px 40px;
            overflow-y: auto;
            max-height: 700px;
        }

        .logo {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 40px;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i {
            color: #ffd166;
        }

        .welcome-text h1 {
            font-size: 36px;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .welcome-text p {
            font-size: 16px;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .features {
            list-style: none;
            margin-top: 40px;
        }

        .features li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .features i {
            color: #4cd964;
            font-size: 20px;
        }

        .signup-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .signup-header h2 {
            font-size: 32px;
            color: #333;
            margin-bottom: 10px;
        }

        .signup-header p {
            color: #666;
            font-size: 16px;
        }

        .signup-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
            font-size: 14px;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
            font-size: 18px;
        }

        input, textarea, select {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
        }

        .password-strength {
            margin-top: 5px;
            font-size: 12px;
            color: #666;
        }

        .strength-meter {
            height: 4px;
            background: #eee;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            background: #ff4757;
            transition: all 0.3s ease;
        }

        .terms-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
        }

        .terms-container input {
            width: auto;
        }

        .terms-container label {
            margin: 0;
            font-weight: normal;
        }

        .terms-container a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .terms-container a:hover {
            text-decoration: underline;
        }

        .btn-signup {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 18px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-signup:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-signup:active {
            transform: translateY(-1px);
        }

        .login-link {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 15px;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #ff4757;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }

        .success-message {
            background: #4cd964;
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .social-signup {
            margin-top: 30px;
            text-align: center;
        }

        .social-signup p {
            color: #666;
            margin-bottom: 15px;
            position: relative;
        }

        .social-signup p::before,
        .social-signup p::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background: #eee;
        }

        .social-signup p::before {
            left: 0;
        }

        .social-signup p::after {
            right: 0;
        }

        .social-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .social-btn {
            flex: 1;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            color: #444;
        }

        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .social-btn.google {
            color: #db4437;
        }

        .social-btn.facebook {
            color: #4267B2;
        }

        .social-btn.twitter {
            color: #1DA1F2;
        }

        @media (max-width: 900px) {
            .signup-container {
                flex-direction: column;
                max-width: 500px;
            }
            
            .signup-left {
                padding: 40px 30px;
            }
            
            .signup-form {
                grid-template-columns: 1fr;
            }
            
            .form-group.full-width {
                grid-column: span 1;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .signup-container {
                border-radius: 15px;
            }
            
            .signup-left, .signup-right {
                padding: 30px 20px;
            }
            
            .social-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <!-- Left Side: Welcome Section -->
        <div class="signup-left">
            <div class="logo">
                <i class="fas fa-car"></i>
                <span>Travel_X</span>
            </div>
            
            <div class="welcome-text">
                <h1>Join Our Vehicle Rental Community</h1>
                <p>Create your account and get access to premium vehicles, exclusive deals, and seamless booking experience.</p>
                
                <ul class="features">
                    <li><i class="fas fa-check-circle"></i> Wide range of vehicles</li>
                    <li><i class="fas fa-check-circle"></i> Best price guarantee</li>
                    <li><i class="fas fa-check-circle"></i> 24/7 customer support</li>
                    <li><i class="fas fa-check-circle"></i> Flexible booking options</li>
                    <li><i class="fas fa-check-circle"></i> Secure payment gateway</li>
                    <li><i class="fas fa-check-circle"></i> Free cancellation</li>
                </ul>
            </div>
        </div>
        
        <!-- Right Side: Signup Form -->
        <div class="signup-right">
            <div class="signup-header">
                <h2>Create Account</h2>
                <p>Fill in your details to get started</p>
            </div>
            
            <?php if($success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> Registration successful! Redirecting to homepage...
                </div>
            <?php endif; ?>
            
            <?php if(isset($errors['database'])): ?>
                <div class="error-message" style="background: #ff4757; color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    <?php echo $errors['database']; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="signupForm" novalidate>
                <div class="signup-form">
                    <!-- Full Name -->
                    <div class="form-group full-width">
                        <label for="full_name">Full Name *</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="full_name" name="full_name" 
                                   value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                                   placeholder="Enter your full name" required>
                        </div>
                        <?php if(isset($errors['full_name'])): ?>
                            <span class="error-message"><?php echo $errors['full_name']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Username -->
                    <div class="form-group">
                        <label for="username">Username *</label>
                        <div class="input-with-icon">
                            <i class="fas fa-at"></i>
                            <input type="text" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                   placeholder="Choose a username" required>
                        </div>
                        <?php if(isset($errors['username'])): ?>
                            <span class="error-message"><?php echo $errors['username']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                   placeholder="Enter your email" required>
                        </div>
                        <?php if(isset($errors['email'])): ?>
                            <span class="error-message"><?php echo $errors['email']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Phone -->
                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <div class="input-with-icon">
                            <i class="fas fa-phone"></i>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                   placeholder="Enter your phone number" required>
                        </div>
                        <?php if(isset($errors['phone'])): ?>
                            <span class="error-message"><?php echo $errors['phone']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Address -->
                    <div class="form-group full-width">
                        <label for="address">Address</label>
                        <div class="input-with-icon">
                            <i class="fas fa-map-marker-alt"></i>
                            <textarea id="address" name="address" 
                                      placeholder="Enter your address"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Driver's License -->
                    <div class="form-group full-width">
                        <label for="license_number">Driver's License Number</label>
                        <div class="input-with-icon">
                            <i class="fas fa-id-card"></i>
                            <input type="text" id="license_number" name="license_number" 
                                   value="<?php echo htmlspecialchars($_POST['license_number'] ?? ''); ?>"
                                   placeholder="Enter your driver's license number">
                        </div>
                    </div>
                    
                    <!-- Password -->
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" 
                                   placeholder="Create a password" required>
                        </div>
                        <div class="password-strength">
                            Password strength: <span id="strength-text">Weak</span>
                        </div>
                        <div class="strength-meter">
                            <div class="strength-fill" id="strength-fill"></div>
                        </div>
                        <?php if(isset($errors['password'])): ?>
                            <span class="error-message"><?php echo $errors['password']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirm_password" name="confirm_password" 
                                   placeholder="Confirm your password" required>
                        </div>
                        <?php if(isset($errors['confirm_password'])): ?>
                            <span class="error-message"><?php echo $errors['confirm_password']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Terms and Conditions -->
                    <div class="form-group full-width">
                        <div class="terms-container">
                            <input type="checkbox" id="terms" name="terms" <?php echo isset($_POST['terms']) ? 'checked' : ''; ?> required>
                            <label for="terms">
                                I agree to the <a href="terms.php" target="_blank">Terms & Conditions</a> and <a href="privacy.php" target="_blank">Privacy Policy</a>
                            </label>
                        </div>
                        <?php if(isset($errors['terms'])): ?>
                            <span class="error-message"><?php echo $errors['terms']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="form-group full-width">
                        <button type="submit" class="btn-signup">
                            <i class="fas fa-user-plus"></i> Create Account
                        </button>
                    </div>
                </div>
            </form>
            
            <!-- Social Signup -->
            <div class="social-signup">
                <p>Or sign up with</p>
                <div class="social-buttons">
                    <button type="button" class="social-btn google">
                        <i class="fab fa-google"></i> Google
                    </button>
                    <button type="button" class="social-btn facebook">
                        <i class="fab fa-facebook"></i> Facebook
                    </button>
                    <button type="button" class="social-btn twitter">
                        <i class="fab fa-twitter"></i> Twitter
                    </button>
                </div>
            </div>
            
            <!-- Login Link -->
            <div class="login-link">
                Already have an account? <a href="login.php">Sign in here</a>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const strengthText = document.getElementById('strength-text');
            const strengthFill = document.getElementById('strength-fill');
            const form = document.getElementById('signupForm');
            
            // Password strength checker
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                
                // Check length
                if (password.length >= 8) strength++;
                
                // Check for uppercase
                if (/[A-Z]/.test(password)) strength++;
                
                // Check for lowercase
                if (/[a-z]/.test(password)) strength++;
                
                // Check for numbers
                if (/[0-9]/.test(password)) strength++;
                
                // Check for special characters
                if (/[^A-Za-z0-9]/.test(password)) strength++;
                
                // Update strength indicator
                const strengthPercent = (strength / 5) * 100;
                strengthFill.style.width = strengthPercent + '%';
                
                // Update text and color
                let text = 'Weak';
                let color = '#ff4757';
                
                if (strength >= 4) {
                    text = 'Strong';
                    color = '#4cd964';
                } else if (strength >= 3) {
                    text = 'Medium';
                    color = '#ffa502';
                }
                
                strengthText.textContent = text;
                strengthFill.style.background = color;
            });
            
            // Password confirmation check
            confirmPasswordInput.addEventListener('input', function() {
                const password = passwordInput.value;
                const confirmPassword = this.value;
                
                if (confirmPassword !== password && confirmPassword !== '') {
                    this.style.borderColor = '#ff4757';
                } else {
                    this.style.borderColor = confirmPassword === '' ? '#e1e5e9' : '#4cd964';
                }
            });
            
            // Real-time username availability check
            const usernameInput = document.getElementById('username');
            let usernameTimeout;
            
            usernameInput.addEventListener('input', function() {
                clearTimeout(usernameTimeout);
                const username = this.value.trim();
                
                if (username.length >= 4) {
                    usernameTimeout = setTimeout(() => {
                        checkUsernameAvailability(username);
                    }, 500);
                }
            });
            
            // Form validation before submit
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Clear previous errors
                document.querySelectorAll('.error-message').forEach(el => {
                    if (!el.classList.contains('server-error')) {
                        el.remove();
                    }
                });
                
                // Validate required fields
                const requiredFields = form.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        showError(field, 'This field is required');
                    }
                });
                
                // Validate email format
                const emailField = document.getElementById('email');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailField.value && !emailRegex.test(emailField.value)) {
                    isValid = false;
                    showError(emailField, 'Invalid email format');
                }
                
                // Validate phone format
                const phoneField = document.getElementById('phone');
                const phoneRegex = /^[0-9]{10,15}$/;
                if (phoneField.value && !phoneRegex.test(phoneField.value)) {
                    isValid = false;
                    showError(phoneField, 'Invalid phone number');
                }
                
                // Validate password match
                if (passwordInput.value !== confirmPasswordInput.value) {
                    isValid = false;
                    showError(confirmPasswordInput, 'Passwords do not match');
                }
                
                // Validate terms
                const termsField = document.getElementById('terms');
                if (!termsField.checked) {
                    isValid = false;
                    showError(termsField, 'You must agree to the terms and conditions');
                }
                
                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first error
                    const firstError = form.querySelector('.error-message');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
            
            function showError(field, message) {
                // Remove existing error
                const existingError = field.parentNode.parentNode.querySelector('.error-message');
                if (existingError) existingError.remove();
                
                // Add error message
                const error = document.createElement('span');
                error.className = 'error-message';
                error.textContent = message;
                field.parentNode.parentNode.appendChild(error);
                
                // Highlight field
                field.style.borderColor = '#ff4757';
                field.addEventListener('input', function clearError() {
                    field.style.borderColor = '';
                    error.remove();
                    field.removeEventListener('input', clearError);
                });
            }
            
            function checkUsernameAvailability(username) {
                // This would typically make an AJAX request to check username availability
                console.log('Checking username:', username);
                // Simulate API call
                /*
                fetch('check_username.php?username=' + encodeURIComponent(username))
                    .then(response => response.json())
                    .then(data => {
                        if (data.available) {
                            usernameInput.style.borderColor = '#4cd964';
                        } else {
                            usernameInput.style.borderColor = '#ff4757';
                            showError(usernameInput, 'Username already taken');
                        }
                    });
                */
            }
            
            // Social login buttons (placeholder)
            document.querySelectorAll('.social-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    alert('Social login integration would go here');
                });
            });
        });
    </script>
</body>
</html>