<?php
// recommendation.php - COMPLETE WORKING VERSION

// 1. Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Include database connection
include_once(__DIR__ . "/connection.php");

// 3. Check database connection
if (!$conn) {
    die("Database connection failed!");
}

// 4. Initialize vehicles array
$vehicles = [];

// 5. Check if user_id exists in session (for debugging)
if (!isset($_SESSION['user_id'])) {
    // echo "<!-- DEBUG: User ID not set in session -->";
}

// 6. Try to get user-based recommendations first (if user is logged in)
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $userId = (int)$_SESSION['user_id'];
    
    // Check if payments table exists
    $check_payments = mysqli_query($conn, "SHOW TABLES LIKE 'payments'");
    
    if (mysqli_num_rows($check_payments) > 0) {
        // User has payment history - get recommendations based on that
        $recommendation_sql = "
            SELECT DISTINCT v.* 
            FROM payments p
            JOIN vehicle v ON p.vehicle_id = v.id 
            WHERE p.user_id = $userId 
            AND v.available = 1
            ORDER BY p.payment_date DESC 
            LIMIT 6
        ";
        
        $recommendation_result = mysqli_query($conn, $recommendation_sql);
        
        if ($recommendation_result && mysqli_num_rows($recommendation_result) > 0) {
            // echo "<!-- DEBUG: Found user-specific recommendations -->";
            while ($row = mysqli_fetch_assoc($recommendation_result)) {
                $vehicles[] = $row;
            }
        } else {
            // echo "<!-- DEBUG: No user-specific recommendations found -->";
        }
    } else {
        // echo "<!-- DEBUG: Payments table doesn't exist -->";
    }
}

// 7. Fallback: Get all available vehicles if no recommendations found
if (empty($vehicles)) {
    // Check if table name is 'vehicle' or 'vehicles'
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'vehicle'");
    
    if (mysqli_num_rows($check_table) > 0) {
        $table_name = 'vehicle';
    } else {
        $table_name = 'vehicles'; // fallback to vehicles table
    }
    
    $fallback_sql = "SELECT * FROM $table_name WHERE available = 1 LIMIT 6";
    $fallback_result = mysqli_query($conn, $fallback_sql);
    
    if ($fallback_result && mysqli_num_rows($fallback_result) > 0) {
        // echo "<!-- DEBUG: Using fallback query - found " . mysqli_num_rows($fallback_result) . " vehicles -->";
        while ($row = mysqli_fetch_assoc($fallback_result)) {
            $vehicles[] = $row;
        }
    } else {
        // echo "<!-- DEBUG: No vehicles found in fallback query -->";
        // Try without available filter
        $alternative_sql = "SELECT * FROM $table_name LIMIT 6";
        $alternative_result = mysqli_query($conn, $alternative_sql);
        
        if ($alternative_result && mysqli_num_rows($alternative_result) > 0) {
            while ($row = mysqli_fetch_assoc($alternative_result)) {
                $vehicles[] = $row;
            }
        }
    }
}

// 8. DEBUG: Count vehicles (remove after testing)
// echo "<!-- DEBUG: Total vehicles to display: " . count($vehicles) . " -->";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Recommendations</title>
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            padding: 20px;
        }
        
        .vehicles {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .heading {
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.5rem;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .heading span {
            color: #ff6b6b;
        }
        
        .swiper {
            width: 100%;
            padding-top: 50px;
            padding-bottom: 50px;
        }
        
        .swiper-slide {
            background: #fff;
            width: 300px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .swiper-slide:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        
        .swiper-slide img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }
        
        .content {
            padding: 20px;
        }
        
        .content h3 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 10px;
        }
        
        .price {
            font-size: 1.2rem;
            color: #ff6b6b;
            margin-bottom: 15px;
            font-weight: bold;
        }
        
        .price span {
            color: #666;
            font-weight: normal;
        }
        
        .content p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        
        .fas.fa-circle {
            font-size: 6px;
            color: #ddd;
            margin: 0 8px;
            vertical-align: middle;
        }
        
        .btn {
            display: inline-block;
            background: #ff6b6b;
            color: white;
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: center;
        }
        
        .btn:hover {
            background: #ff5252;
            transform: scale(1.05);
        }
        
        .swiper-pagination {
            position: relative;
            margin-top: 30px;
        }
        
        .swiper-pagination-bullet {
            width: 12px;
            height: 12px;
            background: #ddd;
            opacity: 1;
        }
        
        .swiper-pagination-bullet-active {
            background: #ff6b6b;
        }
        
        .no-vehicles {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .no-vehicles p {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 20px;
        }
        
        .debug-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            border-left: 4px solid #007bff;
            font-family: monospace;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .heading {
                font-size: 2rem;
            }
            
            .swiper-slide {
                width: 280px;
            }
        }
    </style>
</head>
<body>

<section class="vehicles" id="recommended">
    <h1 class="heading">Recommended for you<span>🚗</span></h1>

    <?php if (count($vehicles) > 0) { ?>

        <div class="swiper vehicles-slider">
            <div class="swiper-wrapper">

                <?php foreach ($vehicles as $row) { 
                    // Handle image path
                    $imagePath = $row['image'];
                    if (!file_exists($imagePath)) {
                        // Try with different path
                        $imagePath = "images/" . basename($imagePath);
                        if (!file_exists($imagePath)) {
                            // Use placeholder if image doesn't exist
                            $imagePath = "https://images.unsplash.com/photo-1503376780353-7e6692767b70?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80";
                        }
                    }
                ?>
                    <div class="swiper-slide box">
                        <img src="<?php echo $imagePath; ?>" 
                             alt="<?php echo htmlspecialchars($row['vehicle_name']); ?>"
                             onerror="this.src='https://images.unsplash.com/photo-1503376780353-7e6692767b70?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80'">
                        
                        <div class="content">
                            <h3><?php echo htmlspecialchars($row['vehicle_name']); ?></h3>
                            
                            <div class="price">
                                <span>Price:</span> Rs. <?php echo number_format($row['amount'], 2); ?>
                            </div>
                            
                            <p>
                                <span><?php echo htmlspecialchars($row['type']); ?></span>
                                <i class="fas fa-circle"></i>
                                <span><?php echo htmlspecialchars($row['model']); ?></span>
                                <i class="fas fa-circle"></i>
                                <span><?php echo htmlspecialchars($row['transmission']); ?></span>
                                <i class="fas fa-circle"></i>
                                <span><?php echo htmlspecialchars($row['fuel_type']); ?></span>
                                <i class="fas fa-circle"></i>
                                <span><?php echo htmlspecialchars($row['top_speed']); ?> mph</span>
                            </p>
                            
                            <a href="checkout.php?id=<?php echo $row['id']; ?>" class="btn">
                                <i class="fas fa-shopping-cart"></i> Check Out
                            </a>
                        </div>
                    </div>
                <?php } ?>

            </div>
            <!-- Add pagination -->
            <div class="swiper-pagination"></div>
        </div>

    <?php } else { ?>

        <div class="no-vehicles">
            <p style="font-size: 2rem; margin-bottom: 20px;">🚫</p>
            <p style="font-size: 1.5rem; color: #ff6b6b; margin-bottom: 10px;">
                No vehicles available at the moment
            </p>
            <p style="color: #666; margin-bottom: 30px;">
                Please check back later or contact support.
            </p>
            <a href="index.php" class="btn" style="width: auto; display: inline-block; padding: 10px 30px;">
                Go to Homepage
            </a>
            
            <!-- DEBUG SECTION - Remove in production -->
            <div class="debug-info">
                <h4>Debug Information:</h4>
                <p>Database Connection: <?php echo $conn ? "✅ Connected" : "❌ Failed"; ?></p>
                <p>Session User ID: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "Not set"; ?></p>
                <p>Vehicles in database: 
                    <?php 
                    $count_sql = "SELECT COUNT(*) as total FROM vehicle WHERE available = 1";
                    $count_result = mysqli_query($conn, $count_sql);
                    if ($count_result) {
                        $count_row = mysqli_fetch_assoc($count_result);
                        echo $count_row['total'];
                    } else {
                        echo "Query failed";
                    }
                    ?>
                </p>
            </div>
        </div>

    <?php } ?>
</section>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<!-- Initialize Swiper -->
<script>
    var swiper = new Swiper(".vehicles-slider", {
        loop: true,
        grabCursor: true,
        spaceBetween: 20,
        centeredSlides: false,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        breakpoints: {
            0: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
        },
    });
</script>

</body>
</html>