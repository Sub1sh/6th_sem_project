<?php
session_start();
include("connection.php");

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    // Insert into database
    $query = "INSERT INTO contact_messages (name, email, phone, subject, message, created_at) 
              VALUES ('$name', '$email', '$phone', '$subject', '$message', NOW())";
    
    if (mysqli_query($conn, $query)) {
        $success_message = "Thank you for contacting us! We'll get back to you soon.";
    } else {
        $error_message = "Something went wrong. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Travel_X Vehicle Rental</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary: #667eea;
            --primary-dark: #764ba2;
            --secondary: #ffd166;
            --light: #f8f9fa;
            --dark: #343a40;
            --light-color: #666;
            --box-shadow: 0 .5rem 1rem rgba(0,0,0,.1);
            --border: .1rem solid rgba(0,0,0,.1);
        }

        body {
            background: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }

        /* Main Content */
        .main-content {
            margin-top: 80px;
            padding: 20px;
        }

        /* Contact Hero */
        .contact-hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            border-radius: 15px;
            margin-bottom: 40px;
        }

        .contact-hero h1 {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .contact-hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
            opacity: 0.9;
        }

        /* Contact Container */
        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 60px;
        }

        @media (max-width: 992px) {
            .contact-container {
                grid-template-columns: 1fr;
            }
        }

        /* Contact Info */
        .contact-info {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: var(--box-shadow);
        }

        .info-header {
            margin-bottom: 40px;
        }

        .info-header h2 {
            font-size: 2rem;
            color: var(--dark);
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }

        .info-header h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            border-radius: 2px;
        }

        .info-header p {
            color: var(--light-color);
            font-size: 1.1rem;
        }

        .info-items {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }

        .info-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .info-content h3 {
            font-size: 1.2rem;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .info-content p {
            color: var(--light-color);
            line-height: 1.8;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .social-link {
            width: 45px;
            height: 45px;
            background: var(--light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.2rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
        }

        /* Contact Form */
        .contact-form {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: var(--box-shadow);
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-header h2 {
            font-size: 2rem;
            color: var(--dark);
            margin-bottom: 10px;
            position: relative;
            display: inline-block;
        }

        .form-header h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            border-radius: 2px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 15px;
            border: var(--border);
            border-radius: 8px;
            font-size: 1rem;
            background: var(--light);
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 576px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .submit-btn {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(76, 217, 100, 0.1);
            color: #4cd964;
            border: 1px solid rgba(76, 217, 100, 0.2);
        }

        .alert-error {
            background: rgba(255, 59, 48, 0.1);
            color: #ff3b30;
            border: 1px solid rgba(255, 59, 48, 0.2);
        }

        /* Map Section */
        .map-section {
            margin-bottom: 60px;
        }

        .section-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-header h2 {
            font-size: 2.5rem;
            color: var(--dark);
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }

        .section-header h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            border-radius: 2px;
        }

        .section-header p {
            color: var(--light-color);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .map-container {
            width: 100%;
            height: 400px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        /* FAQ Section */
        .faq-section {
            margin-bottom: 50px;
        }

        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-item {
            background: white;
            margin-bottom: 15px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }

        .faq-question {
            width: 100%;
            padding: 20px;
            background: var(--light);
            border: none;
            text-align: left;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .faq-answer p {
            padding: 20px 0;
            color: var(--light-color);
        }

        .faq-item.active .faq-answer {
            max-height: 500px;
        }

        /* Business Hours */
        .hours-section {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: var(--box-shadow);
            margin-bottom: 60px;
        }

        .hours-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .hour-item {
            text-align: center;
            padding: 30px;
            background: var(--light);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .hour-item:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            transform: translateY(-5px);
        }

        .hour-item i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .hour-item h3 {
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: var(--dark);
        }

        .hour-item p {
            color: var(--light-color);
            font-size: 1.1rem;
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-content {
                margin-top: 70px;
                padding: 15px;
            }

            .contact-hero h1 {
                font-size: 2.2rem;
            }

            .contact-info,
            .contact-form {
                padding: 30px;
            }

            .info-item {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .section-header h2 {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .contact-hero h1 {
                font-size: 1.8rem;
            }

            .contact-hero p {
                font-size: 1rem;
            }

            .info-header h2,
            .form-header h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <?php include("nav.php"); ?>
    
    <div class="main-content">
        <!-- Hero Section -->
        <section class="contact-hero">
            <h1>Get in Touch</h1>
            <p>Have questions or need assistance? We're here to help! Reach out to us for any inquiries about vehicle rentals.</p>
        </section>

        <!-- Contact Container -->
        <div class="contact-container">
            <!-- Contact Info -->
            <div class="contact-info">
                <div class="info-header">
                    <h2>Contact Information</h2>
                    <p>Feel free to reach out to us through any of the following channels</p>
                </div>
                
                <div class="info-items">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <h3>Our Office</h3>
                            <p>Travel_X Vehicle Rental<br>
                               Kathmandu, Nepal<br>
                               Near Thamel Chowk</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="info-content">
                            <h3>Phone Numbers</h3>
                            <p>Office: +977-1-1234567<br>
                               Mobile: +977-9801234567<br>
                               Support: +977-9807654321</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <h3>Email Address</h3>
                            <p>info@travelx.com.np<br>
                               support@travelx.com.np<br>
                               booking@travelx.com.np</p>
                        </div>
                    </div>
                </div>
                
                <div class="social-links">
                    <a href="#" class="social-link">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="social-link">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="contact-form">
                <div class="form-header">
                    <h2>Send us a Message</h2>
                </div>
                
                <?php if(isset($success_message)): ?>
                    <div class="alert alert-success">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($error_message)): ?>
                    <div class="alert alert-error">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" required placeholder="Enter your full name">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required placeholder="Enter your email address">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" placeholder="Enter your phone number">
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject *</label>
                            <select id="subject" name="subject" required>
                                <option value="">Select a subject</option>
                                <option value="General Inquiry">General Inquiry</option>
                                <option value="Vehicle Booking">Vehicle Booking</option>
                                <option value="Pricing">Pricing Information</option>
                                <option value="Support">Technical Support</option>
                                <option value="Feedback">Feedback & Suggestions</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" rows="6" required placeholder="Type your message here..."></textarea>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>

        <!-- Map Section -->
        <section class="map-section">
            <div class="section-header">
                <h2>Our Location</h2>
                <p>Visit our office or find us on the map</p>
            </div>
            
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3532.462281347246!2d85.31146897538528!3d27.705237025218504!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39eb1900187e45d5%3A0x23d5f7b5d6d6c5c8!2sThamel%2C%20Kathmandu%2044600%2C%20Nepal!5e0!3m2!1sen!2s!4v1690572300000!5m2!1sen!2s" 
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </section>

        <!-- Business Hours -->
        <section class="hours-section">
            <div class="section-header">
                <h2>Business Hours</h2>
                <p>We're available to assist you during these hours</p>
            </div>
            
            <div class="hours-grid">
                <div class="hour-item">
                    <i class="fas fa-calendar-day"></i>
                    <h3>Weekdays</h3>
                    <p>Monday - Friday</p>
                    <p>8:00 AM - 8:00 PM</p>
                </div>
                
                <div class="hour-item">
                    <i class="fas fa-calendar-weekend"></i>
                    <h3>Weekends</h3>
                    <p>Saturday - Sunday</p>
                    <p>9:00 AM - 6:00 PM</p>
                </div>
                
                <div class="hour-item">
                    <i class="fas fa-headset"></i>
                    <h3>24/7 Support</h3>
                    <p>Emergency Support</p>
                    <p>Always Available</p>
                </div>
                
                <div class="hour-item">
                    <i class="fas fa-car"></i>
                    <h3>Vehicle Pickup</h3>
                    <p>Any Day</p>
                    <p>7:00 AM - 9:00 PM</p>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq-section">
            <div class="section-header">
                <h2>Contact FAQs</h2>
                <p>Quick answers to common contact questions</p>
            </div>
            
            <div class="faq-container">
                <div class="faq-item">
                    <button class="faq-question">
                        What's the best way to contact you for urgent inquiries?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>For urgent matters, we recommend calling our support number: +977-9801234567. This line is monitored 24/7 for emergencies related to vehicle breakdowns, accidents, or urgent booking changes.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        How long does it take to get a response to my email?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>We typically respond to emails within 2-4 hours during business hours. For emails received outside business hours, we'll respond by the next business day. Booking-related inquiries are prioritized and usually answered within 1 hour.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        Can I visit your office without an appointment?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Yes, our office is open for walk-in visits during business hours. However, we recommend calling ahead if you need specific documents or services to ensure we can assist you promptly.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        Do you provide support in other languages?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Yes, our staff can assist you in Nepali, English, and Hindi. We also have staff who can speak basic Chinese and Japanese to assist international tourists.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    <?php include("footer.php"); ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // FAQ accordion
            const faqQuestions = document.querySelectorAll('.faq-question');
            faqQuestions.forEach(question => {
                question.addEventListener('click', function() {
                    const item = this.parentElement;
                    const answer = this.nextElementSibling;
                    const icon = this.querySelector('i');
                    
                    // Close other items
                    document.querySelectorAll('.faq-item').forEach(otherItem => {
                        if (otherItem !== item) {
                            otherItem.classList.remove('active');
                            otherItem.querySelector('.faq-answer').style.maxHeight = null;
                            otherItem.querySelector('i').style.transform = 'rotate(0deg)';
                        }
                    });
                    
                    // Toggle current item
                    item.classList.toggle('active');
                    if (item.classList.contains('active')) {
                        answer.style.maxHeight = answer.scrollHeight + 'px';
                        icon.style.transform = 'rotate(180deg)';
                    } else {
                        answer.style.maxHeight = null;
                        icon.style.transform = 'rotate(0deg)';
                    }
                });
            });
            
            // Form validation
            const contactForm = document.querySelector('form');
            if (contactForm) {
                contactForm.addEventListener('submit', function(e) {
                    const name = document.getElementById('name').value.trim();
                    const email = document.getElementById('email').value.trim();
                    const subject = document.getElementById('subject').value;
                    const message = document.getElementById('message').value.trim();
                    
                    if (!name || !email || !subject || !message) {
                        e.preventDefault();
                        alert('Please fill in all required fields.');
                        return false;
                    }
                    
                    if (!isValidEmail(email)) {
                        e.preventDefault();
                        alert('Please enter a valid email address.');
                        return false;
                    }
                    
                    // Show loading state
                    const submitBtn = this.querySelector('.submit-btn');
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                    submitBtn.disabled = true;
                });
            }
            
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }
            
            // Phone number formatting
            const phoneInput = document.getElementById('phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 10) {
                        value = value.substring(0, 10);
                    }
                    
                    if (value.length > 6) {
                        value = value.substring(0, 6) + '-' + value.substring(6);
                    }
                    if (value.length > 3) {
                        value = value.substring(0, 3) + '-' + value.substring(3);
                    }
                    
                    e.target.value = value;
                });
            }
            
            // Smooth scroll for social links
            const socialLinks = document.querySelectorAll('.social-link');
            socialLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const platform = this.querySelector('i').className.split(' ')[1];
                    alert(`Redirecting to our ${getPlatformName(platform)} page`);
                    // In real implementation, you would redirect to actual social media URLs
                    // window.open(getSocialURL(platform), '_blank');
                });
            });
            
            function getPlatformName(className) {
                const platforms = {
                    'fa-facebook-f': 'Facebook',
                    'fa-twitter': 'Twitter',
                    'fa-instagram': 'Instagram',
                    'fa-linkedin-in': 'LinkedIn',
                    'fa-whatsapp': 'WhatsApp'
                };
                return platforms[className] || 'social media';
            }
            
            // Add animation to sections on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);
            
            // Animate sections
            const sections = document.querySelectorAll('.contact-info, .contact-form, .map-section, .hours-section, .faq-section');
            sections.forEach(section => {
                section.style.opacity = '0';
                section.style.transform = 'translateY(20px)';
                section.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(section);
            });
        });
    </script>
</body>
</html>