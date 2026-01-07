<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Feedback - Travel X</title>
    <!-- CDN icon library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/signUp.css">
    <style type="text/css">
        body {
            background-image: url("image/background 2.jpg");
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .adminTopic {
            text-align: center;
            color: #fff;
            font-size: 2.5rem;
            margin-bottom: 2rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .form_wrap .submit_btn:hover {
            color: #fff;
            font-weight: 600;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        #description {
            width: 100%;
            border-radius: 3px;
            border: 1px solid #9597a6;
            padding: 10px;
            outline: none;
            color: black;
            font-family: inherit;
            resize: vertical;
            min-height: 100px;
        }

        .idclass {
            width: 100%;
            border-radius: 3px;
            border: 1px solid #9597a6;
            padding: 10px;
            outline: none;
            color: black;
            font-family: inherit;
        }

        .sidebar2 {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem;
        }

        .wrapper {
            width: 100%;
            max-width: 500px;
        }

        .registration_form {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            backdrop-filter: blur(10px);
        }

        .title {
            font-size: 2rem;
            color: #130f40;
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 600;
        }

        .input_wrap {
            margin-bottom: 1.5rem;
        }

        .input_wrap label {
            display: block;
            margin-bottom: 0.5rem;
            color: #130f40;
            font-weight: 500;
            font-size: 1.4rem;
        }

        .input_wrap input[type="text"],
        .input_wrap input[type="email"],
        .input_wrap textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 1.4rem;
            transition: all 0.3s ease;
            background: #fff;
        }

        .input_wrap input[type="text"]:focus,
        .input_wrap input[type="email"]:focus,
        .input_wrap textarea:focus {
            border-color: #f9d806;
            box-shadow: 0 0 5px rgba(249, 216, 6, 0.3);
            outline: none;
        }

        .submit_btn {
            width: 100%;
            padding: 12px;
            background: #f9d806;
            color: #130f40;
            border: none;
            border-radius: 5px;
            font-size: 1.6rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .submit_btn:hover {
            background: #8cacea;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 5px;
            text-align: center;
            font-size: 1.4rem;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar2 {
                padding: 1rem;
            }
            
            .registration_form {
                padding: 1.5rem;
            }
            
            .title {
                font-size: 1.8rem;
            }
            
            .adminTopic {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .registration_form {
                padding: 1rem;
            }
            
            .title {
                font-size: 1.6rem;
            }
            
            .input_wrap input[type="text"],
            .input_wrap input[type="email"],
            .input_wrap textarea {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar2">
        <div class="wrapper">
            <div class="registration_form">
                <div class="title">
                    Add Feedback
                </div>

                <?php
                include("connection.php");

                if(isset($_POST['Addfeed'])) {
                    $name = $_POST['name'];
                    $email = $_POST['email'];
                    $message = $_POST['message'];

                    // Input validation
                    if(empty($name) || empty($email) || empty($message)) {
                        echo "<div class='alert alert-error'>All fields are required!</div>";
                    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        echo "<div class='alert alert-error'>Please enter a valid email address!</div>";
                    } else {
                        if($conn->connect_error) {
                            die('Connection Failed: '.$conn->connect_error);
                        } else {
                            // Use prepared statements to prevent SQL injection
                            $stmt = $conn->prepare("INSERT INTO feedback (name, email, message) VALUES (?, ?, ?)");
                            $stmt->bind_param("sss", $name, $email, $message);

                            if($stmt->execute()) {
                                echo "<div class='alert alert-success'>Feedback added successfully!</div>";
                                echo "<script>
                                    setTimeout(function() {
                                        window.location.href = 'feedbackManage.php';
                                    }, 2000);
                                </script>";
                            } else {
                                echo "<div class='alert alert-error'>Error adding feedback: " . $conn->error . "</div>";
                            }

                            $stmt->close();
                            $conn->close();
                        }
                    }
                }
                ?>

                <form action="#" method="POST" id="feedbackForm">
                    <div class="form_wrap">
                        <div class="input_wrap">
                            <label for="name">Name:</label>
                            <input type="text" id="name" name="name" placeholder="Enter your name" required 
                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        </div>

                        <div class="input_wrap">
                            <label for="email">E-mail:</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email" required 
                                   pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" 
                                   title="Please enter a valid email address"
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>

                        <div class="input_wrap">
                            <label for="message">Message:</label>
                            <textarea id="message" name="message" placeholder="Enter your feedback message" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        </div>

                        <div class="input_wrap">
                            <input type="submit" value="Add Feedback" class="submit_btn" name="Addfeed">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Form validation
        document.getElementById('feedbackForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const message = document.getElementById('message').value.trim();

            if(name === '' || email === '' || message === '') {
                e.preventDefault();
                alert('Please fill in all fields.');
                return false;
            }

            if(message.length < 10) {
                e.preventDefault();
                alert('Please provide a more detailed message (at least 10 characters).');
                return false;
            }
        });

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>