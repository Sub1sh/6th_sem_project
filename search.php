<?php
// search.php - Updated to work with your database structure
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("connection.php");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Vehicles - TravelX</title>
    
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
        
        .search-header {
            margin-bottom: 30px;
            text-align: center;
        }
        
        .search-header h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 10px;
        }
        
        .search-header h1 span {
            color: #8E2DE2;
        }
        
        .filter-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
        }
        
        .filter-tab {
            padding: 10px 20px;
            background: white;
            border-radius: 30px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .filter-tab:hover, .filter-tab.active {
            background: #8E2DE2;
            color: white;
            transform: translateY(-2px);
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
            margin-bottom: 5px;
        }
        
        .vehicle-brand {
            color: #8E2DE2;
            font-size: 1rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .vehicle-price {
            color: #e74c3c;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 15px 0;
        }
        
        .vehicle-details {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
        }
        
        .detail-item {
            flex: 1 1 calc(50% - 10px);
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
        }
        
        .detail-item i {
            color: #8E2DE2;
            width: 20px;
        }
        
        .vehicle-description {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
            padding: 10px;
            background: #fff3e0;
            border-radius: 8px;
            font-style: italic;
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
            transform: scale(1.02);
            box-shadow: 0 10px 20px rgba(142, 45, 226, 0.3);
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
    </style>
</head>
<body>

<!-- Include Navbar -->
<?php include_once('nav.php'); ?>

<div class="search-container">
    <div class="search-header">
        <h1>🔍 <span>Search</span> Vehicles</h1>
        <p>Find your perfect ride from 71 vehicles across 17 categories</p>
    </div>
    
    <!-- Type Filters -->
    <div class="filter-tabs">
        <a href="?q=<?php echo urlencode($search_query); ?>&filter=all" 
           class="filter-tab <?php echo ($filter == 'all') ? 'active' : ''; ?>">All Types</a>
        <?php
        $types = mysqli_query($conn, "SELECT DISTINCT type FROM vehicles ORDER BY type");
        while ($t = mysqli_fetch_assoc($types)) {
            $active = ($filter == $t['type']) ? 'active' : '';
            echo "<a href='?q=" . urlencode($search_query) . "&filter=" . urlencode($t['type']) . "' class='filter-tab $active'>" . $t['type'] . "</a>";
        }
        ?>
    </div>
    
    <div class="results-container">
        <?php
        // Build search query
        $sql = "SELECT * FROM vehicles WHERE 1=1";
        
        // Apply type filter
        if ($filter != 'all') {
            $escaped_filter = mysqli_real_escape_string($conn, $filter);
            $sql .= " AND type = '$escaped_filter'";
        }
        
        // Apply search query
        if (!empty($search_query)) {
            $search_term = mysqli_real_escape_string($conn, $search_query);
            $sql .= " AND (
                brand LIKE '%$search_term%' OR 
                model LIKE '%$search_term%' OR 
                type LIKE '%$search_term%' OR
                description LIKE '%$search_term%' OR
                tags LIKE '%$search_term%'
            )";
        }
        
        $sql .= " ORDER BY 
            CASE 
                WHEN type = 'SUV' THEN 1
                WHEN type = 'Sedan' THEN 2
                WHEN type = 'Truck' THEN 3
                WHEN type = 'Sports Car' THEN 4
                WHEN type = 'Bus' THEN 5
                WHEN type = 'Van' THEN 6
                WHEN type = 'Motorcycle' THEN 7
                ELSE 8
            END, brand";
        
        $result = mysqli_query($conn, $sql);
        $total_results = mysqli_num_rows($result);
        
        // Search info
        echo '<div class="search-info">';
        if (!empty($search_query)) {
            echo 'Found <strong>' . $total_results . '</strong> vehicles for "<strong>' . htmlspecialchars($search_query) . '</strong>"';
            if ($filter != 'all') {
                echo ' in <strong>' . $filter . '</strong> category';
            }
        } else {
            if ($filter != 'all') {
                echo 'Showing all <strong>' . $total_results . '</strong> vehicles in <strong>' . $filter . '</strong> category';
            } else {
                echo 'Showing all <strong>' . $total_results . '</strong> available vehicles';
            }
        }
        echo '</div>';
        
        if ($result && $total_results > 0) {
            echo '<div class="results-grid">';
            while ($row = mysqli_fetch_assoc($result)) {
                $image = $row['image_url'] ?? 'images/vehicle-1.png';
                if (!file_exists($image)) {
                    $image = 'https://via.placeholder.com/400x300/8E2DE2/ffffff?text=' . urlencode($row['brand']);
                }
                
                // Get country flag based on brand
                $flag = '🌐';
                $brand_lower = strtolower($row['brand']);
                if (strpos($brand_lower, 'toyota') !== false || strpos($brand_lower, 'honda') !== false || 
                    strpos($brand_lower, 'mahindra') !== false || strpos($brand_lower, 'tata') !== false) {
                    $flag = '🇮🇳';
                } elseif (strpos($brand_lower, 'ford') !== false || strpos($brand_lower, 'jeep') !== false || 
                          strpos($brand_lower, 'tesla') !== false) {
                    $flag = '🇺🇸';
                } elseif (strpos($brand_lower, 'bmw') !== false || strpos($brand_lower, 'mercedes') !== false || 
                          strpos($brand_lower, 'audi') !== false || strpos($brand_lower, 'porsche') !== false) {
                    $flag = '🇩🇪';
                } elseif (strpos($brand_lower, 'volvo') !== false) {
                    $flag = '🇸🇪';
                } elseif (strpos($brand_lower, 'lamborghini') !== false || strpos($brand_lower, 'ferrari') !== false || 
                          strpos($brand_lower, 'maserati') !== false) {
                    $flag = '🇮🇹';
                } elseif (strpos($brand_lower, 'aston') !== false || strpos($brand_lower, 'bentley') !== false || 
                          strpos($brand_lower, 'rolls') !== false || strpos($brand_lower, 'land rover') !== false) {
                    $flag = '🇬🇧';
                }
                ?>
                <div class="vehicle-card">
                    <div class="vehicle-img-container">
                        <img src="<?php echo $image; ?>" 
                             alt="<?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?>"
                             class="vehicle-img"
                             onerror="this.src='https://via.placeholder.com/400x300/8E2DE2/ffffff?text=<?php echo urlencode($row['brand']); ?>'">
                        <div class="vehicle-type-badge"><?php echo htmlspecialchars($row['type']); ?></div>
                    </div>
                    
                    <div class="vehicle-content">
                        <h3 class="vehicle-title"><?php echo htmlspecialchars($row['model']); ?></h3>
                        <div class="vehicle-brand">
                            <?php echo $flag; ?> <?php echo htmlspecialchars($row['brand']); ?> • <?php echo $row['year']; ?>
                        </div>
                        
                        <div class="vehicle-price">
                            Rs. <?php echo number_format($row['daily_rate'], 2); ?>/day
                        </div>
                        
                        <div class="vehicle-details">
                            <div class="detail-item"><i class="fas fa-cog"></i> <?php echo htmlspecialchars($row['transmission'] ?? 'Auto'); ?></div>
                            <div class="detail-item"><i class="fas fa-gas-pump"></i> <?php echo htmlspecialchars($row['fuel_type'] ?? 'Petrol'); ?></div>
                            <div class="detail-item"><i class="fas fa-tachometer-alt"></i> <?php echo $row['top_speed'] ?? '120'; ?> km/h</div>
                            <div class="detail-item"><i class="fas fa-palette"></i> <?php echo htmlspecialchars($row['color'] ?? ''); ?></div>
                        </div>
                        
                        <?php if (!empty($row['description'])): ?>
                        <div class="vehicle-description">
                            <?php echo htmlspecialchars(substr($row['description'], 0, 100)) . '...'; ?>
                        </div>
                        <?php endif; ?>
                        
                        <a href="book.php?id=<?php echo $row['id']; ?>" class="book-btn">
                            <i class="fas fa-calendar-check"></i> Book Now
                        </a>
                    </div>
                </div>
                <?php
            }
            echo '</div>';
        } else {
            ?>
            <div class="no-results">
                <i class="fas fa-car-crash"></i>
                <h3>No vehicles found</h3>
                <p>Try different keywords or browse all vehicles</p>
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