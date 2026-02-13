<?php

// 1. Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Include database connection
include_once("connection.php");

// 3. Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// 4. Handle search requests
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Vehicles - TravelX</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
            padding-top: 80px;
        }
        
        .search-container {
            max-width: 1400px;
            margin: 30px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            padding: 30px;
        }
        
        .results-container {
            min-height: 400px;
        }
        
        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }
        
        .vehicle-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .vehicle-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            border-color: #8E2DE2;
        }
        
        .vehicle-img-container {
            width: 100%;
            height: 220px;
            overflow: hidden;
            background: #f0f0f0;
            position: relative;
        }
        
        .vehicle-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .vehicle-card:hover .vehicle-img {
            transform: scale(1.05);
        }
        
        .vehicle-type-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(142, 45, 226, 0.9);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .vehicle-content {
            padding: 25px;
        }
        
        .vehicle-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        
        .vehicle-brand {
            color: #666;
            font-size: 1rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .vehicle-price {
            color: #8E2DE2;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .vehicle-details {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .detail-badge {
            background: #f8f9fa;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #666;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .detail-badge i {
            color: #8E2DE2;
        }
        
        .vehicle-description {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .book-btn {
            display: block;
            width: 100%;
            background: linear-gradient(to right, #4A00E0, #8E2DE2);
            color: white;
            text-align: center;
            padding: 12px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .book-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(142, 45, 226, 0.3);
            color: white;
        }
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
            background: #f8f9fa;
            border-radius: 15px;
        }
        
        .no-results i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .no-results h3 {
            color: #666;
            margin-bottom: 10px;
            font-size: 1.8rem;
        }
        
        .no-results p {
            color: #888;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        
        .search-info {
            margin-bottom: 20px;
            color: #666;
            font-size: 1.1rem;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .search-info strong {
            color: #8E2DE2;
        }
        
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }
            
            .results-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .vehicle-status {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(76, 175, 80, 0.9);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

<!-- Include Navbar (has the search bar) -->
<?php include_once('nav.php'); ?>

<div class="search-container">
    <!-- Results Only - No Header, No Filters -->
    <div class="results-container">
        <?php
        // ===== SEARCH LOGIC =====
        
        // Base query - only show available vehicles
        $sql = "SELECT * FROM vehicles WHERE status = 'available'";
        
        // Handle search query
        if (!empty($search_query)) {
            $search_term = strtolower(trim($search_query));
            $escaped_term = mysqli_real_escape_string($conn, $search_term);
            
            // SPECIFIC HANDLING FOR DIFFERENT VEHICLE TYPES
            if ($search_term == 'bus' || strpos($search_term, 'bus') !== false) {
                // BUS SEARCH - ONLY show vehicles with type exactly 'Bus'
                $sql .= " AND type = 'Bus'";
                
            } elseif ($search_term == 'van' || strpos($search_term, 'van') !== false) {
                // VAN SEARCH - ONLY show vehicles with type exactly 'Van'
                $sql .= " AND type = 'Van'";
                
            } elseif ($search_term == 'truck' || strpos($search_term, 'truck') !== false || 
                      $search_term == 'truk' || $search_term == 'pickup') {
                // TRUCK SEARCH - Show trucks and pickups
                $sql .= " AND (type = 'Truck' OR type = 'Pickup Truck' OR type LIKE '%Truck%')";
                
            } elseif ($search_term == 'suv' || strpos($search_term, 'suv') !== false) {
                // SUV SEARCH
                $sql .= " AND type = 'SUV'";
                
            } elseif ($search_term == 'sedan' || strpos($search_term, 'sedan') !== false || 
                      $search_term == 'seden') {
                // SEDAN SEARCH
                $sql .= " AND type = 'Sedan'";
                
            } elseif ($search_term == 'bike' || $search_term == 'motorcycle' || 
                      strpos($search_term, 'bike') !== false || strpos($search_term, 'motor') !== false) {
                // BIKE SEARCH
                $sql .= " AND (type = 'Motorcycle' OR type = 'Scooter')";
                
            } else {
                // GENERAL SEARCH - search in multiple fields
                $sql .= " AND (
                    LOWER(brand) LIKE '%$escaped_term%' OR 
                    LOWER(model) LIKE '%$escaped_term%' OR 
                    LOWER(type) LIKE '%$escaped_term%' OR
                    LOWER(description) LIKE '%$escaped_term%'
                )";
            }
        }
        
        // Order by price
        $sql .= " ORDER BY daily_rate ASC";
        
        $result = mysqli_query($conn, $sql);
        
        if ($result === false) {
            echo '<div class="alert alert-danger">Database error: ' . mysqli_error($conn) . '</div>';
            $total_results = 0;
        } else {
            $total_results = mysqli_num_rows($result);
        }
        
        // Display search info
        echo '<div class="search-info">';
        if (!empty($search_query)) {
            echo 'Found <strong>' . $total_results . '</strong> vehicles for "<strong>' . htmlspecialchars($search_query) . '</strong>"';
        } else {
            echo 'Showing all <strong>' . $total_results . '</strong> available vehicles';
        }
        echo '</div>';
        
        if ($result && $total_results > 0) {
            echo '<div class="results-grid">';
            while ($row = mysqli_fetch_assoc($result)) {
                // Get image
                $image_url = $row['image_url'] ?? 'images/vehicle-1.png';
                
                if (empty($image_url) || !file_exists($image_url)) {
                    // Use different placeholders based on vehicle type
                    $type = strtolower($row['type'] ?? '');
                    if (strpos($type, 'bus') !== false) {
                        $image_url = 'images/White Bus on Rural Road.png';
                    } elseif (strpos($type, 'van') !== false) {
                        $image_url = 'images/vehicles 2.jpg';
                    } elseif (strpos($type, 'truck') !== false) {
                        $image_url = 'images/tipper-truck.png';
                    } else {
                        $image_url = 'https://images.unsplash.com/photo-1553440569-bcc63803a83d?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
                    }
                }
                
                $status_class = strtolower($row['status']);
                $status_text = ucfirst($row['status']);
                ?>
                <div class="vehicle-card">
                    <div class="vehicle-img-container">
                        <img src="<?php echo $image_url; ?>" 
                             alt="<?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?>"
                             class="vehicle-img"
                             onerror="this.src='https://images.unsplash.com/photo-1553440569-bcc63803a83d?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'">
                        <div class="vehicle-type-badge"><?php echo htmlspecialchars($row['type']); ?></div>
                        <div class="vehicle-status <?php echo $status_class; ?>"><?php echo $status_text; ?></div>
                    </div>
                    
                    <div class="vehicle-content">
                        <h3 class="vehicle-title"><?php echo htmlspecialchars($row['model']); ?></h3>
                        
                        <div class="vehicle-brand">
                            <i class="fas fa-car"></i>
                            <?php echo htmlspecialchars($row['brand']); ?> • <?php echo $row['year']; ?>
                        </div>
                        
                        <div class="vehicle-price">
                            Rs. <?php echo number_format($row['daily_rate'], 2); ?>/day
                        </div>
                        
                        <div class="vehicle-details">
                            <span class="detail-badge"><i class="fas fa-cog"></i> <?php echo htmlspecialchars($row['transmission'] ?? 'Auto'); ?></span>
                            <span class="detail-badge"><i class="fas fa-gas-pump"></i> <?php echo htmlspecialchars($row['fuel_type'] ?? 'Petrol'); ?></span>
                            <span class="detail-badge"><i class="fas fa-tachometer-alt"></i> <?php echo $row['top_speed'] ?? '120'; ?> km/h</span>
                            <span class="detail-badge"><i class="fas fa-palette"></i> <?php echo htmlspecialchars($row['color'] ?? ''); ?></span>
                        </div>
                        
                        <p class="vehicle-description"><?php echo htmlspecialchars($row['description'] ?? ''); ?></p>
                        
                        <a href="book.php?id=<?php echo $row['id']; ?>" class="book-btn">
                            <i class="fas fa-calendar-check"></i> Book Now
                        </a>
                    </div>
                </div>
                <?php
            }
            echo '</div>';
            mysqli_free_result($result);
        } else {
            ?>
            <div class="no-results">
                <i class="fas fa-car-crash"></i>
                <h3>No vehicles found</h3>
                <p>Try different keywords in the search bar above</p>
                <a href="search.php" class="book-btn" style="width: auto; display: inline-block; padding: 10px 30px;">
                    <i class="fas fa-undo"></i> Show All Vehicles
                </a>
            </div>
            <?php
        }
        ?>
    </div>
</div>

</body>
</html>