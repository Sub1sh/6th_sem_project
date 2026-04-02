<?php
session_start();
include_once(__DIR__ . "/connection.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loging.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Fetch user data from database
$sql = "SELECT id, name, email, phone, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_destroy();
    header("Location: loging.php");
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    $errors = [];
    
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
    
    // Check if email is already taken by another user
    if (empty($errors)) {
        $check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $email, $user_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $errors[] = "Email already in use by another account";
        }
        $check_stmt->close();
    }
    
    if (empty($errors)) {
        $update_sql = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $name, $email, $phone, $user_id);
        
        if ($update_stmt->execute()) {
            $success = "Profile updated successfully!";
            // Update session variables
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            // Refresh user data
            $user['name'] = $name;
            $user['email'] = $email;
            $user['phone'] = $phone;
        } else {
            $error = "Failed to update profile: " . $update_stmt->error;
        }
        $update_stmt->close();
    } else {
        $error = implode("<br>", $errors);
    }
}

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    
    if (empty($current_password)) {
        $errors[] = "Current password is required";
    }
    
    if (empty($new_password)) {
        $errors[] = "New password is required";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "New password must be at least 6 characters";
    }
    
    if ($new_password !== $confirm_password) {
        $errors[] = "New passwords do not match";
    }
    
    if (empty($errors)) {
        // Verify current password
        $pass_sql = "SELECT password FROM users WHERE id = ?";
        $pass_stmt = $conn->prepare($pass_sql);
        $pass_stmt->bind_param("i", $user_id);
        $pass_stmt->execute();
        $pass_result = $pass_stmt->get_result();
        $user_data = $pass_result->fetch_assoc();
        
        if (password_verify($current_password, $user_data['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_pass_sql = "UPDATE users SET password = ? WHERE id = ?";
            $update_pass_stmt = $conn->prepare($update_pass_sql);
            $update_pass_stmt->bind_param("si", $hashed_password, $user_id);
            
            if ($update_pass_stmt->execute()) {
                $success = "Password changed successfully!";
            } else {
                $error = "Failed to change password";
            }
            $update_pass_stmt->close();
        } else {
            $error = "Current password is incorrect";
        }
        $pass_stmt->close();
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
    <title>My Profile - Travel_X</title>
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
            padding: 80px 20px 40px;
        }

        /* Navigation Bar */
        .navbar {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 1rem 2rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .logo h2 {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1f6e43, #2b9b5e);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            font-weight: 500;
            color: #2c3e44;
            transition: 0.2s;
        }

        .nav-links a:hover {
            color: #1f6e43;
        }

        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .logout-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        /* Profile Container */
        .profile-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Profile Header */
        .profile-header {
            background: white;
            border-radius: 24px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #1f6e43, #2b9b5e);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .avatar i {
            font-size: 50px;
            color: white;
        }

        .profile-header h2 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .profile-header .member-since {
            color: #888;
            font-size: 0.85rem;
        }

        /* Profile Cards */
        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            color: #1f6e43;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
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
            background: #f8fafc;
        }

        .form-group input:focus {
            border-color: #2b9b5e;
            background: white;
            box-shadow: 0 0 0 3px rgba(43, 155, 94, 0.1);
        }

        .form-group input:disabled {
            background: #f1f5f9;
            cursor: not-allowed;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .btn {
            padding: 0.9rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1f6e43, #2b9b5e);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(31, 110, 67, 0.3);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #cbd5e1;
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        /* Alert Messages */
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

        /* Info Display */
        .info-row {
            display: flex;
            padding: 1rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-label {
            width: 120px;
            font-weight: 600;
            color: #555;
        }

        .info-value {
            flex: 1;
            color: #333;
        }

        /* Edit Mode Toggle */
        .edit-toggle {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 1rem;
        }

        .hidden {
            display: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 70px 15px 30px;
            }
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            .navbar {
                flex-direction: column;
            }
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<!-- Navigation -->
<div class="navbar">
    <div class="logo">
        <h2>Travel <span style="color:#eab308;">X</span></h2>
    </div>
    <div class="nav-links">
        <a href="homepage.php"><i class="fas fa-home"></i> Home</a>
        <a href="homepage.php#vehicles"><i class="fas fa-car"></i> Vehicles</a>
        <a href="profile.php"><i class="fas fa-user-circle"></i> My Profile</a>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="profile-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <h2><?php echo htmlspecialchars($user['name']); ?></h2>
        <p class="member-since">
            <i class="fas fa-calendar-alt"></i> Member since: 
            <?php echo date('F j, Y', strtotime($user['created_at'])); ?>
        </p>
    </div>

    <!-- Success/Error Messages -->
    <?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <div><?php echo $success; ?></div>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <div><?php echo $error; ?></div>
    </div>
    <?php endif; ?>

    <!-- Profile Information Card -->
    <div class="profile-card">
        <div class="card-title">
            <i class="fas fa-user-edit"></i>
            <span>Profile Information</span>
        </div>
        
        <!-- Display Mode -->
        <div id="displayMode">
            <div class="info-row">
                <div class="info-label">Full Name:</div>
                <div class="info-value"><?php echo htmlspecialchars($user['name']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Email Address:</div>
                <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone Number:</div>
                <div class="info-value"><?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Member Since:</div>
                <div class="info-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></div>
            </div>
            <div class="edit-toggle" style="margin-top: 1.5rem;">
                <button class="btn btn-primary" onclick="toggleEditMode(true)">
                    <i class="fas fa-edit"></i> Edit Profile
                </button>
            </div>
        </div>

        <!-- Edit Mode -->
        <div id="editMode" class="hidden">
            <form method="POST" action="">
                <div class="form-group">
                    <label>Full Name <span style="color:#e74c3c;">*</span></label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address <span style="color:#e74c3c;">*</span></label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number <span style="color:#e74c3c;">*</span></label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="toggleEditMode(false)">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password Card -->
    <div class="profile-card">
        <div class="card-title">
            <i class="fas fa-lock"></i>
            <span>Change Password</span>
        </div>
        
        <form method="POST" action="" id="passwordForm">
            <div class="form-group">
                <label>Current Password <span style="color:#e74c3c;">*</span></label>
                <input type="password" name="current_password" id="current_password" placeholder="Enter your current password" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>New Password <span style="color:#e74c3c;">*</span></label>
                    <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required>
                    <div class="password-strength" style="margin-top: 0.5rem; height: 4px; background: #e2e8f0; border-radius: 2px; overflow: hidden;">
                        <div id="strengthBar" style="height: 100%; width: 0%; transition: width 0.3s;"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Confirm New Password <span style="color:#e74c3c;">*</span></label>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required>
                    <span id="matchHint" style="font-size: 0.7rem; margin-top: 0.25rem; display: block;"></span>
                </div>
            </div>
            <button type="submit" name="change_password" class="btn btn-primary">
                <i class="fas fa-key"></i> Change Password
            </button>
        </form>
    </div>

    <!-- Account Statistics Card -->
    <div class="profile-card">
        <div class="card-title">
            <i class="fas fa-chart-line"></i>
            <span>Account Statistics</span>
        </div>
        <div class="info-row">
            <div class="info-label">Account ID:</div>
            <div class="info-value">#<?php echo $user['id']; ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Account Status:</div>
            <div class="info-value"><span style="color: #27ae60;"><i class="fas fa-check-circle"></i> Active</span></div>
        </div>
        <div class="info-row">
            <div class="info-label">Last Login:</div>
            <div class="info-value"><?php echo date('F j, Y g:i A'); ?></div>
        </div>
    </div>
</div>

<script>
    // Toggle between display and edit mode
    function toggleEditMode(showEdit) {
        const displayMode = document.getElementById('displayMode');
        const editMode = document.getElementById('editMode');
        
        if (showEdit) {
            displayMode.classList.add('hidden');
            editMode.classList.remove('hidden');
        } else {
            displayMode.classList.remove('hidden');
            editMode.classList.add('hidden');
        }
    }

    // Password strength checker
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const strengthBar = document.getElementById('strengthBar');
    const matchHint = document.getElementById('matchHint');

    function checkPasswordStrength(password) {
        let strength = 0;
        let width = 0;
        let color = '#e74c3c';
        
        if (password.length >= 6) strength++;
        if (password.length >= 10) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        if (strength <= 2) {
            width = 33;
            color = '#e74c3c';
        } else if (strength <= 4) {
            width = 66;
            color = '#f39c12';
        } else {
            width = 100;
            color = '#27ae60';
        }
        
        strengthBar.style.width = width + '%';
        strengthBar.style.backgroundColor = color;
    }

    function checkPasswordMatch() {
        if (confirmPassword.value.length > 0) {
            if (newPassword.value === confirmPassword.value) {
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

    if (newPassword) {
        newPassword.addEventListener('input', function() {
            if (this.value.length > 0) {
                checkPasswordStrength(this.value);
            } else {
                strengthBar.style.width = '0%';
            }
            checkPasswordMatch();
        });
    }

    if (confirmPassword) {
        confirmPassword.addEventListener('input', checkPasswordMatch);
    }

    // Form validation for password change
    document.getElementById('passwordForm')?.addEventListener('submit', function(e) {
        if (newPassword.value !== confirmPassword.value) {
            e.preventDefault();
            alert('❌ New passwords do not match!');
        } else if (newPassword.value.length > 0 && newPassword.value.length < 6) {
            e.preventDefault();
            alert('❌ Password must be at least 6 characters long!');
        }
    });
</script>

</body>
</html>