<?php
// recommendation.php - FINAL FIXED VERSION

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

// 5. Try to get user-based recommendations first
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $userId = (int)$_SESSION['user_id'];
    
    // Check if payments table exists
    $check_payments = mysqli_query($conn, "SHOW TABLES LIKE 'payments'");
    
    if (mysqli_num_rows($check_payments) > 0) {
        $recommendation_sql = "
            SELECT DISTINCT v.* 
            FROM payments p
            JOIN vehicles v ON p.vehicle_id = v.id 
            WHERE p.user_id = $userId 
            AND v.status = 'available'
            ORDER BY p.payment_date DESC 
            LIMIT 6
        ";
        
        $recommendation_result = mysqli_query($conn, $recommendation_sql);
        
        if ($recommendation_result && mysqli_num_rows($recommendation_result) > 0) {
            while ($row = mysqli_fetch_assoc($recommendation_result)) {
                $vehicles[] = $row;
            }
        }
    }
}

// 6. Fallback: Get all available vehicles
if (empty($vehicles)) {
    $fallback_sql = "SELECT * FROM vehicles WHERE status = 'available' ORDER BY id LIMIT 6";
    $fallback_result = mysqli_query($conn, $fallback_sql);
    
    if ($fallback_result && mysqli_num_rows($fallback_result) > 0) {
        while ($row = mysqli_fetch_assoc($fallback_result)) {
            $vehicles[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommended Vehicles - TravelX</title>
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .vehicles {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .heading {
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.5rem;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            padding-bottom: 15px;
        }
        
        .heading::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(to right, #f9d806, #ffee80);
            border-radius: 2px;
        }
        
        .heading span {
            color: #f9d806;
        }
        
        .swiper {
            width: 100%;
            padding: 50px 10px;
        }
        
        .swiper-slide {
            background: #fff;
            width: 320px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .swiper-slide:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            border-color: #f9d806;
        }
        
        .swiper-slide .vehicle-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
            border-bottom: 3px solid #f0f0f0;
        }
        
        .content {
            padding: 25px;
        }
        
        .content h3 {
            font-size: 1.4rem;
            color: #333;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .price {
            font-size: 1.3rem;
            color: #f9d806;
            margin-bottom: 15px;
            font-weight: bold;
        }
        
        .price span {
            color: #666;
            font-weight: normal;
            font-size: 1rem;
        }
        
        .vehicle-details {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }
        
        .detail-badge {
            background: #f8f9fa;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #666;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .detail-badge i {
            color: #f9d806;
        }
        
        .btn {
            display: inline-block;
            background: linear-gradient(to right, #f9d806, #ffee80);
            color: #130f40;
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            cursor: pointer;
            width: 100%;
            text-align: center;
        }
        
        .btn:hover {
            background: #130f40;
            color: #f9d806;
            border-color: #f9d806;
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
            background: #f9d806;
        }
        
        .no-vehicles {
            text-align: center;
            padding: 60px 20px;
            background: #f8f9fa;
            border-radius: 15px;
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
    <h1 class="heading">Recommended for you <span>🚗</span></h1>

    <?php if (count($vehicles) > 0) { ?>

        <div class="swiper vehicles-slider">
            <div class="swiper-wrapper">

                <?php foreach ($vehicles as $row) { 
                    
                    // ===== USE THE IMAGE URL DIRECTLY FROM DATABASE =====
                    // Your database already has the correct image paths
                    $image_path = $row['image_url'] ?? '';
                    
                    // If image_url is empty, use fallback
                    if (empty($image_path)) {
                        // Fallback based on vehicle type
                        $type = strtolower($row['type'] ?? '');
                        if (strpos($type, 'truck') !== false) {
                            $image_path = 'images/tipper-truck.png';
                        } elseif (strpos($type, 'bus') !== false) {
                            $image_path = 'images/White Bus on Rural Road.png';
                        } elseif (strpos($type, 'van') !== false) {
                            $image_path = 'images/vehicles 2.jpg';
                        } elseif (strpos($type, 'motorcycle') !== false) {
                            $image_path = 'images/vehicles 6.png';
                        } else {
                            $image_path = 'images/vehicle-' . ($row['id'] % 6 + 1) . '.png';
                        }
                    }
                    
                    // Get vehicle details
                    $display_name = $row['brand'] . ' ' . $row['model'];
                    $display_price = $row['daily_rate'] ?? 0;
                ?>
                    <div class="swiper-slide box">
                        <img src="<?php echo $image_path; ?>" 
                             alt="<?php echo htmlspecialchars($display_name); ?>"
                             class="vehicle-img"
                             onerror="this.src='https://images.unsplash.com/photo-1553440569-bcc63803a83d?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'">
                        
                        <div class="content">
                            <h3><?php echo htmlspecialchars($row['model']); ?></h3>
                            
                            <div class="price">
                                <span>Price:</span> Rs. <?php echo number_format($display_price, 2); ?>/day
                            </div>
                            
                            <div class="vehicle-details">
                                <span class="detail-badge">
                                    <i class="fas fa-car"></i> <?php echo htmlspecialchars($row['brand']); ?>
                                </span>
                                <span class="detail-badge">
                                    <i class="fas fa-calendar"></i> <?php echo $row['year']; ?>
                                </span>
                                <span class="detail-badge">
                                    <i class="fas fa-cog"></i> <?php echo htmlspecialchars($row['transmission'] ?? 'Auto'); ?>
                                </span>
                                <span class="detail-badge">
                                    <i class="fas fa-gas-pump"></i> <?php echo htmlspecialchars($row['fuel_type'] ?? 'Petrol'); ?>
                                </span>
                                <span class="detail-badge">
                                    <i class="fas fa-tachometer-alt"></i> <?php echo $row['top_speed'] ?? '120'; ?> km/h
                                </span>
                                <span class="detail-badge">
                                    <i class="fas fa-palette"></i> <?php echo htmlspecialchars($row['color'] ?? ''); ?>
                                </span>
                            </div>
                            
                            <a href="checkout.php?id=<?php echo $row['id']; ?>" class="btn">
                                <i class="fas fa-shopping-cart"></i> Book Now
                            </a>
                        </div>
                    </div>
                <?php } ?>

            </div>
            <div class="swiper-pagination"></div>
        </div>

    <?php } else { ?>

        <div class="no-vehicles">
            <i class="fas fa-car-crash" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
            <p style="font-size: 1.2rem; color: #666;">No vehicles available at the moment</p>
            <a href="vehicles.php" class="btn" style="width: auto; display: inline-block; padding: 10px 30px; margin-top: 20px;">
                Browse All Vehicles
            </a>
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
            0: { slidesPerView: 1 },
            640: { slidesPerView: 2 },
            1024: { slidesPerView: 3 }
        },
    });
</script>

</body>
</html>