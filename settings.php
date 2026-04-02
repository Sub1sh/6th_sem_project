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

// Fetch current user data
$sql = "SELECT id, name, email, phone, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle Profile Update
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
        
        if ($check_stmt) {
            $check_stmt->bind_param("si", $email, $user_id);
            $check_stmt->execute();
            $check_stmt->store_result();
            
            if ($check_stmt->num_rows > 0) {
                $errors[] = "Email already in use by another account";
            }
            $check_stmt->close();
        }
    }
    
    if (empty($errors)) {
        $update_sql = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        
        if ($update_stmt) {
            $update_stmt->bind_param("sssi", $name, $email, $phone, $user_id);
            
            if ($update_stmt->execute()) {
                $success = "Profile updated successfully!";
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $user['name'] = $name;
                $user['email'] = $email;
                $user['phone'] = $phone;
            } else {
                $error = "Failed to update profile: " . $update_stmt->error;
            }
            $update_stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    } else {
        $error = implode("<br>", $errors);
    }
}

// Handle Password Change
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
        $pass_sql = "SELECT password FROM users WHERE id = ?";
        $pass_stmt = $conn->prepare($pass_sql);
        
        if ($pass_stmt) {
            $pass_stmt->bind_param("i", $user_id);
            $pass_stmt->execute();
            $pass_result = $pass_stmt->get_result();
            $user_data = $pass_result->fetch_assoc();
            
            if (password_verify($current_password, $user_data['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_pass_sql = "UPDATE users SET password = ? WHERE id = ?";
                $update_pass_stmt = $conn->prepare($update_pass_sql);
                
                if ($update_pass_stmt) {
                    $update_pass_stmt->bind_param("si", $hashed_password, $user_id);
                    
                    if ($update_pass_stmt->execute()) {
                        $success = "Password changed successfully!";
                    } else {
                        $error = "Failed to change password";
                    }
                    $update_pass_stmt->close();
                } else {
                    $error = "Database error: " . $conn->error;
                }
            } else {
                $error = "Current password is incorrect";
            }
            $pass_stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    } else {
        $error = implode("<br>", $errors);
    }
}

// Handle Notification Preferences
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_notifications'])) {
    $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
    $sms_notifications = isset($_POST['sms_notifications']) ? 1 : 0;
    $promotional_emails = isset($_POST['promotional_emails']) ? 1 : 0;
    
    // Create notification_settings table if not exists
    $create_table = "CREATE TABLE IF NOT EXISTS notification_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL UNIQUE,
        email_notifications TINYINT DEFAULT 1,
        sms_notifications TINYINT DEFAULT 0,
        promotional_emails TINYINT DEFAULT 1,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $conn->query($create_table);
    
    $insert_sql = "INSERT INTO notification_settings (user_id, email_notifications, sms_notifications, promotional_emails) 
                   VALUES (?, ?, ?, ?) 
                   ON DUPLICATE KEY UPDATE 
                   email_notifications = VALUES(email_notifications),
                   sms_notifications = VALUES(sms_notifications),
                   promotional_emails = VALUES(promotional_emails)";
    
    $insert_stmt = $conn->prepare($insert_sql);
    
    if ($insert_stmt) {
        $insert_stmt->bind_param("iiii", $user_id, $email_notifications, $sms_notifications, $promotional_emails);
        
        if ($insert_stmt->execute()) {
            $success = "Notification preferences saved!";
        } else {
            $error = "Failed to save preferences: " . $insert_stmt->error;
        }
        $insert_stmt->close();
    } else {
        $error = "Database error: " . $conn->error;
    }
}

// Fetch current notification settings
$email_notifications = 1;
$sms_notifications = 0;
$promotional_emails = 1;

$notif_sql = "SELECT * FROM notification_settings WHERE user_id = ?";
$notif_stmt = $conn->prepare($notif_sql);

if ($notif_stmt) {
    $notif_stmt->bind_param("i", $user_id);
    $notif_stmt->execute();
    $notif_result = $notif_stmt->get_result();
    
    if ($notif_result->num_rows > 0) {
        $settings = $notif_result->fetch_assoc();
        $email_notifications = $settings['email_notifications'];
        $sms_notifications = $settings['sms_notifications'];
        $promotional_emails = $settings['promotional_emails'];
    }
    $notif_stmt->close();
}

// Handle Account Deactivation (without status column)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deactivate_account'])) {
    $confirm_text = $_POST['confirm_deactivate'] ?? '';
    
    if ($confirm_text === 'DEACTIVATE') {
        // Instead of updating status, we'll just delete or archive the user
        // Option 1: Delete user account
        $delete_sql = "DELETE FROM users WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        
        if ($delete_stmt) {
            $delete_stmt->bind_param("i", $user_id);
            
            if ($delete_stmt->execute()) {
                session_destroy();
                header("Location: homepage.php?message=account_deleted");
                exit();
            } else {
                $error = "Failed to deactivate account: " . $delete_stmt->error;
            }
            $delete_stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    } else {
        $error = "Please type 'DEACTIVATE' to confirm account deactivation";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Travel_X</title>
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
            flex-wrap: wrap;
        }

        .nav-links a {
            text-decoration: none;
            font-weight: 500;
            color: #2c3e44;
            transition: 0.2s;
        }

        .nav-links a:hover, .nav-links a.active {
            color: #1f6e43;
        }

        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
        }

        .settings-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 2.5rem;
            color: white;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: rgba(255,255,255,0.8);
        }

        .settings-card {
            background: white;
            border-radius: 20px;
            margin-bottom: 1.5rem;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: #f8fafc;
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header i {
            font-size: 1.3rem;
            color: #1f6e43;
        }

        .card-header h2 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }

        .card-body {
            padding: 1.5rem;
        }

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
            padding: 0.85rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.9rem;
            font-family: inherit;
            transition: all 0.3s;
            outline: none;
        }

        .form-group input:focus {
            border-color: #1f6e43;
            box-shadow: 0 0 0 3px rgba(31, 110, 67, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .toggle-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .toggle-item:last-child {
            border-bottom: none;
        }

        .toggle-info h4 {
            font-size: 0.95rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.2rem;
        }

        .toggle-info p {
            font-size: 0.75rem;
            color: #888;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.3s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #1f6e43;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .btn {
            padding: 0.85rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1f6e43, #2b9b5e);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(31, 110, 67, 0.3);
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #e74c3c;
            color: #e74c3c;
        }

        .btn-outline:hover {
            background: #e74c3c;
            color: white;
        }

        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
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

        .danger-zone {
            border: 2px solid #fee2e2;
            background: #fff5f5;
        }

        .danger-zone .card-header {
            background: #fee2e2;
            border-bottom-color: #fecaca;
        }

        .danger-zone .card-header i {
            color: #e74c3c;
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
            transition: width 0.3s;
        }

        .strength-bar.weak { background: #e74c3c; width: 33%; }
        .strength-bar.medium { background: #f39c12; width: 66%; }
        .strength-bar.strong { background: #27ae60; width: 100%; }

        @media (max-width: 768px) {
            body {
                padding: 70px 15px 30px;
            }
            .navbar {
                flex-direction: column;
            }
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">
        <h2>Travel <span style="color:#eab308;">X</span></h2>
    </div>
    <div class="nav-links">
        <a href="homepage.php"><i class="fas fa-home"></i> Home</a>
        <a href="vehicles.php"><i class="fas fa-car"></i> Vehicles</a>
        <a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a>
        <a href="bookings.php"><i class="fas fa-bookmark"></i> Bookings</a>
        <a href="settings.php" class="active"><i class="fas fa-cog"></i> Settings</a>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="settings-container">
    <div class="page-header">
        <h1><i class="fas fa-cog"></i> Settings</h1>
        <p>Manage your account preferences and security</p>
    </div>

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

    <!-- Profile Settings -->
    <div class="settings-card">
        <div class="card-header">
            <i class="fas fa-user-edit"></i>
            <h2>Profile Information</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Profile
                </button>
            </form>
        </div>
    </div>

    <!-- Change Password -->
    <div class="settings-card">
        <div class="card-header">
            <i class="fas fa-lock"></i>
            <h2>Change Password</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="" id="passwordForm">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" id="current_password" placeholder="Enter current password" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required>
                        <div class="password-strength">
                            <div class="strength-bar" id="strengthBar"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required>
                        <span id="matchHint" style="font-size: 0.7rem;"></span>
                    </div>
                </div>
                <button type="submit" name="change_password" class="btn btn-primary">
                    <i class="fas fa-key"></i> Change Password
                </button>
            </form>
        </div>
    </div>

    <!-- Notification Preferences -->
    <div class="settings-card">
        <div class="card-header">
            <i class="fas fa-bell"></i>
            <h2>Notification Preferences</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="toggle-item">
                    <div class="toggle-info">
                        <h4>Email Notifications</h4>
                        <p>Receive booking confirmations and updates via email</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="email_notifications" <?php echo $email_notifications ? 'checked' : ''; ?>>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="toggle-item">
                    <div class="toggle-info">
                        <h4>SMS Notifications</h4>
                        <p>Get text message alerts for booking status</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="sms_notifications" <?php echo $sms_notifications ? 'checked' : ''; ?>>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="toggle-item">
                    <div class="toggle-info">
                        <h4>Promotional Emails</h4>
                        <p>Receive special offers and discounts</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="promotional_emails" <?php echo $promotional_emails ? 'checked' : ''; ?>>
                        <span class="slider"></span>
                    </label>
                </div>
                <button type="submit" name="save_notifications" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-save"></i> Save Preferences
                </button>
            </form>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="settings-card danger-zone">
        <div class="card-header">
            <i class="fas fa-exclamation-triangle"></i>
            <h2>Danger Zone</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="" onsubmit="return confirmDeactivation()">
                <p style="color: #e74c3c; margin-bottom: 1rem; font-size: 0.85rem;">
                    <i class="fas fa-warning"></i> 
                    Once you delete your account, you will lose access to all your bookings and data. This action cannot be undone.
                </p>
                <div class="form-group">
                    <label>Type <strong style="color:#e74c3c;">DEACTIVATE</strong> to confirm:</label>
                    <input type="text" name="confirm_deactivate" class="deactivate-input" 
                           placeholder="Type DEACTIVATE" required>
                </div>
                <button type="submit" name="deactivate_account" class="btn btn-outline">
                    <i class="fas fa-trash-alt"></i> Delete Account
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const strengthBar = document.getElementById('strengthBar');
    const matchHint = document.getElementById('matchHint');

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
        } else if (strength <= 4) {
            strengthBar.classList.add('medium');
        } else {
            strengthBar.classList.add('strong');
        }
    }

    function checkPasswordMatch() {
        if (confirmPassword.value.length > 0) {
            if (newPassword.value === confirmPassword.value) {
                matchHint.innerHTML = '<i class="fas fa-check-circle" style="color:#27ae60;"></i> Passwords match!';
                matchHint.style.color = '#27ae60';
            } else {
                matchHint.innerHTML = '<i class="fas fa-times-circle" style="color:#e74c3c;"></i> Passwords do not match';
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

    function confirmDeactivation() {
        return confirm('⚠️ WARNING: This will permanently delete your account and all associated data. Are you absolutely sure?');
    }

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