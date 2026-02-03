<?php
session_start();
include("connection.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Travel_X Vehicle Rental</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css">
    
    <!-- Custom CSS -->
    <style>
        /* CSS Styles - All included here, no external image files needed */
        :root {
            --primary: #667eea;
            --primary-dark: #764ba2;
            --secondary: #ffd166;
            --success: #4cd964;
            --danger: #ff4757;
            --warning: #ffa502;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray: #6c757d;
            --border-radius: 10px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Loading Animation */
        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }
        
        .loader.fade-out {
            opacity: 0;
            pointer-events: none;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        /* Hero Section - No image needed */
        .hero-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                linear-gradient(45deg, transparent 49%, rgba(255,255,255,0.1) 50%, transparent 51%),
                linear-gradient(-45deg, transparent 49%, rgba(255,255,255,0.1) 50%, transparent 51%);
            background-size: 50px 50px;
            opacity: 0.3;
        }

        .hero-content {
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            animation: fadeInDown 1s ease;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 50px;
            animation: fadeInUp 1s ease 0.3s both;
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-top: 50px;
            animation: fadeInUp 1s ease 0.6s both;
        }

        .stat {
            text-align: center;
            padding: 30px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius);
            backdrop-filter: blur(10px);
            transition: var(--transition);
        }

        .stat:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.2);
        }

        .stat i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--secondary);
        }

        .stat h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .hero-visual {
            margin-top: 50px;
            animation: fadeIn 1s ease 0.9s both;
        }

        .hero-visual-content {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .vehicle-icon {
            font-size: 3rem;
            color: var(--secondary);
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .vehicle-icon:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.2);
        }

        /* Section Header */
        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--dark);
        }

        .underline {
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            margin: 0 auto 20px;
            border-radius: 2px;
        }

        .section-header p {
            color: var(--gray);
            font-size: 1.1rem;
        }

        /* Story Section */
        .story-section {
            padding: 100px 0;
            background: var(--light);
        }

        .timeline {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 100%;
            background: var(--primary);
        }

        .timeline-item {
            display: flex;
            justify-content: center;
            margin-bottom: 50px;
            opacity: 0;
            transform: translateY(30px);
            transition: var(--transition);
        }

        .timeline-item.animate {
            opacity: 1;
            transform: translateY(0);
        }

        .timeline-item:nth-child(odd) {
            flex-direction: row;
        }

        .timeline-item:nth-child(even) {
            flex-direction: row-reverse;
        }

        .timeline-year {
            flex: 0 0 100px;
            background: var(--primary);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-align: center;
            font-weight: 600;
            position: relative;
            z-index: 1;
            margin: 0 30px;
        }

        .timeline-content {
            flex: 1;
            background: white;
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .timeline-content h3 {
            color: var(--primary);
            margin-bottom: 15px;
        }

        /* Mission Section */
        .mission-section {
            padding: 100px 0;
            background: white;
        }

        .mission-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .mission-card {
            text-align: center;
            padding: 40px 30px;
            background: var(--light);
            border-radius: var(--border-radius);
            transition: var(--transition);
            opacity: 0;
            transform: translateY(30px);
        }

        .mission-card.animate {
            opacity: 1;
            transform: translateY(0);
        }

        .mission-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--box-shadow);
        }

        .mission-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }

        .mission-icon i {
            font-size: 2rem;
            color: white;
        }

        .mission-card h3 {
            margin-bottom: 15px;
            color: var(--dark);
        }

        /* Team Section - No images needed */
        .team-section {
            padding: 100px 0;
            background: var(--light);
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .team-card {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            text-align: center;
            transition: var(--transition);
            opacity: 0;
            transform: translateY(30px);
        }

        .team-card.animate {
            opacity: 1;
            transform: translateY(0);
        }

        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--box-shadow);
        }

        .team-avatar {
            width: 150px;
            height: 150px;
            margin: 40px auto 20px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3.5rem;
            transition: var(--transition);
        }

        .team-card:hover .team-avatar {
            transform: scale(1.1);
        }

        .team-card h3 {
            margin: 20px 0 5px;
            color: var(--dark);
        }

        .team-role {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .team-desc {
            color: var(--gray);
            padding: 0 20px;
            margin-bottom: 20px;
        }

        .team-social {
            padding: 20px;
            border-top: 1px solid #eee;
        }

        .team-social a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: var(--light);
            border-radius: 50%;
            line-height: 40px;
            color: var(--dark);
            margin: 0 5px;
            transition: var(--transition);
        }

        .team-social a:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
        }

        /* Awards Section */
        .awards-section {
            padding: 100px 0;
            background: white;
        }

        .award-card {
            background: var(--light);
            padding: 40px 30px;
            border-radius: var(--border-radius);
            text-align: center;
            transition: var(--transition);
            opacity: 0;
            transform: translateY(30px);
        }

        .award-card.animate {
            opacity: 1;
            transform: translateY(0);
        }

        .award-card:hover {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            transform: translateY(-10px);
        }

        .award-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 20px;
            transition: var(--transition);
        }

        .award-card:hover .award-icon {
            color: white;
        }

        /* Testimonials Section - No customer images needed */
        .testimonials-section {
            padding: 100px 0;
            background: var(--light);
        }

        .testimonial-card {
            background: white;
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: var(--transition);
        }

        .testimonial-card:hover {
            transform: translateY(-10px);
        }

        .testimonial-content {
            position: relative;
            margin-bottom: 30px;
        }

        .testimonial-content i.fa-quote-left {
            position: absolute;
            top: -10px;
            left: -10px;
            font-size: 2rem;
            color: var(--primary);
            opacity: 0.3;
        }

        .testimonial-content i.fa-quote-right {
            position: absolute;
            bottom: -10px;
            right: -10px;
            font-size: 2rem;
            color: var(--primary);
            opacity: 0.3;
        }

        .testimonial-content p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--dark);
        }

        .testimonial-author {
            display: flex;
            align-items: center;
        }

        .testimonial-avatar {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-right: 15px;
        }

        .testimonial-author h4 {
            color: var(--dark);
            margin-bottom: 5px;
        }

        .testimonial-author p {
            color: var(--gray);
            font-size: 0.9rem;
        }

        /* FAQ Section */
        .faq-section {
            padding: 100px 0;
            background: white;
        }

        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-item {
            margin-bottom: 15px;
            border-radius: var(--border-radius);
            overflow: hidden;
            border: 1px solid #eee;
            transition: var(--transition);
        }

        .faq-item:hover {
            border-color: var(--primary);
        }

        .faq-question {
            width: 100%;
            padding: 20px 30px;
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
            transition: var(--transition);
        }

        .faq-question:hover {
            background: #e9ecef;
        }

        .faq-question i {
            transition: var(--transition);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .faq-answer p {
            padding: 20px 30px;
            color: var(--gray);
            line-height: 1.7;
        }

        .faq-item.active .faq-question {
            background: var(--primary);
            color: white;
        }

        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            text-align: center;
        }

        .cta-content h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .cta-content p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 15px 35px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: white;
            color: var(--primary);
        }

        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        /* Swiper Styles */
        .swiper-pagination-bullet {
            width: 12px;
            height: 12px;
            background: var(--gray);
            opacity: 0.5;
        }

        .swiper-pagination-bullet-active {
            background: var(--primary);
            opacity: 1;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        /* Responsive Design */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.8rem;
            }
            
            .section-header h2 {
                font-size: 2.2rem;
            }
            
            .timeline::before {
                left: 30px;
            }
            
            .timeline-item {
                flex-direction: row !important;
                justify-content: flex-start;
            }
            
            .timeline-year {
                margin-right: 30px;
                margin-left: 0;
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.2rem;
            }
            
            .hero-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .mission-grid,
            .team-grid {
                grid-template-columns: 1fr;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .hero-stats {
                grid-template-columns: 1fr;
            }
            
            .stat {
                padding: 20px;
            }
            
            .faq-question {
                padding: 15px 20px;
                font-size: 1rem;
            }
            
            .faq-answer p {
                padding: 15px 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div class="loader" id="loader">
        <div class="spinner"></div>
    </div>
    
    <?php include("nav.php"); ?>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">About Travel_X</h1>
            <p class="hero-subtitle">Your Trusted Vehicle Rental Partner Since 2015</p>
            <div class="hero-stats">
                <div class="stat">
                    <i class="fas fa-car"></i>
                    <h3>500+</h3>
                    <p>Vehicles</p>
                </div>
                <div class="stat">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>15+</h3>
                    <p>Locations</p>
                </div>
                <div class="stat">
                    <i class="fas fa-users"></i>
                    <h3>50,000+</h3>
                    <p>Happy Customers</p>
                </div>
                <div class="stat">
                    <i class="fas fa-star"></i>
                    <h3>4.8/5</h3>
                    <p>Rating</p>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-visual-content">
                    <div class="vehicle-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <div class="vehicle-icon">
                        <i class="fas fa-shuttle-van"></i>
                    </div>
                    <div class="vehicle-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="vehicle-icon">
                        <i class="fas fa-motorcycle"></i>
                    </div>
                    <div class="vehicle-icon">
                        <i class="fas fa-bus"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Our Story -->
    <section class="story-section">
        <div class="container">
            <div class="section-header">
                <h2>Our Journey</h2>
                <div class="underline"></div>
                <p>From humble beginnings to Nepal's leading rental service</p>
            </div>
            
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-year">2015</div>
                    <div class="timeline-content">
                        <h3>Humble Beginnings</h3>
                        <p>Started with just 5 vehicles in Kathmandu, focusing on tourist transportation.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">2017</div>
                    <div class="timeline-content">
                        <h3>First Expansion</h3>
                        <p>Expanded to Pokhara and Chitwan with 25 vehicles, introducing online booking.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">2019</div>
                    <div class="timeline-content">
                        <h3>Technology Integration</h3>
                        <p>Launched mobile app and GPS tracking across all vehicles.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">2021</div>
                    <div class="timeline-content">
                        <h3>National Presence</h3>
                        <p>Reached 15 cities across Nepal with 300+ vehicles.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">2023</div>
                    <div class="timeline-content">
                        <h3>Premium Services</h3>
                        <p>Introduced luxury fleet and corporate rental solutions.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Mission & Vision -->
    <section class="mission-section">
        <div class="container">
            <div class="mission-grid">
                <div class="mission-card">
                    <div class="mission-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3>Our Mission</h3>
                    <p>To provide safe, reliable, and accessible transportation solutions that empower mobility across Nepal, enhancing travel experiences through exceptional service and innovation.</p>
                </div>
                
                <div class="mission-card">
                    <div class="mission-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3>Our Vision</h3>
                    <p>To become Nepal's most trusted and innovative vehicle rental service, setting new standards in customer satisfaction and sustainable transportation.</p>
                </div>
                
                <div class="mission-card">
                    <div class="mission-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Our Values</h3>
                    <p>Integrity, Reliability, Innovation, Customer Focus, and Community Responsibility guide every decision we make.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <div class="section-header">
                <h2>Meet Our Leadership</h2>
                <div class="underline"></div>
                <p>The dedicated team behind Travel_X's success</p>
            </div>
            
            <div class="team-grid">
                <div class="team-card">
                    <div class="team-avatar">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3>Rajesh Thapa</h3>
                    <p class="team-role">Founder & CEO</p>
                    <p class="team-desc">20+ years in transportation industry</p>
                    <div class="team-social">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                
                <div class="team-card">
                    <div class="team-avatar">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <h3>Sunita Gurung</h3>
                    <p class="team-role">Operations Head</p>
                    <p class="team-desc">15 years in fleet management</p>
                    <div class="team-social">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                
                <div class="team-card">
                    <div class="team-avatar">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>Amit Shrestha</h3>
                    <p class="team-role">Customer Relations</p>
                    <p class="team-desc">12 years in hospitality</p>
                    <div class="team-social">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Awards & Recognition -->
    <section class="awards-section">
        <div class="container">
            <div class="section-header">
                <h2>Awards & Recognition</h2>
                <div class="underline"></div>
                <p>Our commitment to excellence recognized nationally</p>
            </div>
            
            <div class="awards-slider swiper">
                <div class="swiper-wrapper">
                    <div class="award-card swiper-slide">
                        <div class="award-icon">
                            <i class="fas fa-award"></i>
                        </div>
                        <h3>Best Vehicle Rental Service 2022</h3>
                        <p>Nepal Tourism Board</p>
                    </div>
                    
                    <div class="award-card swiper-slide">
                        <div class="award-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <h3>Customer Excellence Award</h3>
                        <p>Nepal Business Chamber</p>
                    </div>
                    
                    <div class="award-card swiper-slide">
                        <div class="award-icon">
                            <i class="fas fa-medal"></i>
                        </div>
                        <h3>Innovation in Tourism 2021</h3>
                        <p>Ministry of Tourism</p>
                    </div>
                    
                    <div class="award-card swiper-slide">
                        <div class="award-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>5-Star Safety Rating</h3>
                        <p>Department of Transport</p>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>
    
    <!-- Testimonials -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-header">
                <h2>What Our Customers Say</h2>
                <div class="underline"></div>
                <p>Trusted by travelers and businesses across Nepal</p>
            </div>
            
            <div class="testimonials-slider swiper">
                <div class="swiper-wrapper">
                    <div class="testimonial-card swiper-slide">
                        <div class="testimonial-content">
                            <i class="fas fa-quote-left"></i>
                            <p>"Travel_X made our family trip to Pokhara unforgettable. The vehicle was clean, comfortable, and the driver was very professional. Highly recommended!"</p>
                            <i class="fas fa-quote-right"></i>
                        </div>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h4>Sarah Johnson</h4>
                                <p>International Tourist</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-card swiper-slide">
                        <div class="testimonial-content">
                            <i class="fas fa-quote-left"></i>
                            <p>"As a local business, we rely on Travel_X for our logistics. Their reliability and professional service have been instrumental to our operations."</p>
                            <i class="fas fa-quote-right"></i>
                        </div>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div>
                                <h4>Ramesh Bhandari</h4>
                                <p>Business Owner</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-card swiper-slide">
                        <div class="testimonial-content">
                            <i class="fas fa-quote-left"></i>
                            <p>"The online booking system is so convenient! I booked a car for my wedding within minutes. The luxury vehicle arrived on time and was immaculate."</p>
                            <i class="fas fa-quote-right"></i>
                        </div>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div>
                                <h4>Priya Sharma</h4>
                                <p>Event Planner</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>
    
    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="section-header">
                <h2>Frequently Asked Questions</h2>
                <div class="underline"></div>
                <p>Get answers to common questions about our services</p>
            </div>
            
            <div class="faq-container">
                <div class="faq-item">
                    <button class="faq-question">
                        What documents do I need to rent a vehicle?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>You need a valid driving license, citizenship card or passport, and a security deposit. International tourists need a valid international driving permit along with their passport.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        Can I rent a vehicle without a driver?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Yes, we offer both self-drive and chauffeur-driven options. For self-drive, you must be at least 21 years old and have held a valid license for at least 2 years.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        What is your cancellation policy?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>You can cancel free of charge up to 24 hours before pickup. Cancellations within 24 hours incur a 20% fee. No refund for no-shows.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        Do you offer insurance coverage?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>All our vehicles come with comprehensive insurance. Additional CDW (Collision Damage Waiver) is available for extra protection at a nominal fee.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        Can I extend my rental period?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Yes, you can extend your rental by contacting us at least 6 hours before the scheduled return time, subject to vehicle availability.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready for Your Next Adventure?</h2>
                <p>Join thousands of satisfied customers who trust Travel_X for their transportation needs</p>
                <div class="cta-buttons">
                    <a href="vehicles.php" class="btn btn-primary">
                        <i class="fas fa-car"></i> Browse Vehicles
                    </a>
                    <a href="contactus.php" class="btn btn-secondary">
                        <i class="fas fa-phone"></i> Contact Us
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <?php include("footer.php"); ?>
    
    <!-- Swiper JS -->
    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
    
    <script>
        // All JavaScript is included here - no external files needed
        document.addEventListener('DOMContentLoaded', function() {
            // Page Loader
            window.addEventListener('load', function() {
                const loader = document.getElementById('loader');
                setTimeout(() => {
                    loader.classList.add('fade-out');
                    setTimeout(() => {
                        loader.style.display = 'none';
                    }, 500);
                }, 1000);
            });
            
            // Initialize Swiper Sliders
            // Awards Slider
            const awardsSwiper = new Swiper('.awards-slider', {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 3,
                    }
                }
            });
            
            // Testimonials Slider
            const testimonialsSwiper = new Swiper('.testimonials-slider', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                    }
                }
            });
            
            // FAQ Accordion
            const faqQuestions = document.querySelectorAll('.faq-question');
            faqQuestions.forEach(question => {
                question.addEventListener('click', () => {
                    const item = question.parentElement;
                    const answer = question.nextElementSibling;
                    const icon = question.querySelector('i');
                    
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
            
            // Scroll Animation
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate');
                    }
                });
            }, observerOptions);
            
            // Observe elements for animation
            document.querySelectorAll('.timeline-item, .mission-card, .team-card, .award-card').forEach(el => {
                observer.observe(el);
            });
            
            // Counter Animation for Stats
            const stats = document.querySelectorAll('.stat h3');
            stats.forEach(stat => {
                const text = stat.textContent;
                if (text.includes('+') || text.includes('/')) {
                    const target = parseInt(text.replace(/[^0-9]/g, ''));
                    let current = 0;
                    const increment = target / 50;
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            current = target;
                            clearInterval(timer);
                        }
                        stat.textContent = Math.floor(current) + (text.includes('/') ? '/5' : '+');
                    }, 30);
                }
            });
            
            // Back to top button
            const backToTop = document.createElement('button');
            backToTop.innerHTML = '<i class="fas fa-arrow-up"></i>';
            backToTop.style.cssText = `
                position: fixed;
                bottom: 30px;
                right: 30px;
                width: 50px;
                height: 50px;
                background: var(--primary);
                color: white;
                border: none;
                border-radius: 50%;
                cursor: pointer;
                display: none;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
                transition: all 0.3s ease;
                z-index: 1000;
            `;
            
            document.body.appendChild(backToTop);
            
            backToTop.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            
            window.addEventListener('scroll', () => {
                if (window.scrollY > 500) {
                    backToTop.style.display = 'flex';
                } else {
                    backToTop.style.display = 'none';
                }
            });
            
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;
                    
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        const headerHeight = document.querySelector('header')?.offsetHeight || 80;
                        const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                        
                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>