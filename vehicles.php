<?php
session_start();
include_once(__DIR__ . "/connection.php");

// Get current page number
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Get filter parameters
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';
$transmission_filter = isset($_GET['transmission']) ? $_GET['transmission'] : '';
$fuel_filter = isset($_GET['fuel']) ? $_GET['fuel'] : '';
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 100000;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Number of vehicles per page
$vehicles_per_page = 9;

// Calculate offset
$offset = ($page - 1) * $vehicles_per_page;

// Build WHERE clause
$where_conditions = ["status = 'available'"];
$params = [];
$types = "";

if (!empty($type_filter)) {
    $where_conditions[] = "type = ?";
    $params[] = $type_filter;
    $types .= "s";
}

if (!empty($transmission_filter)) {
    $where_conditions[] = "transmission = ?";
    $params[] = $transmission_filter;
    $types .= "s";
}

if (!empty($fuel_filter)) {
    $where_conditions[] = "fuel_type = ?";
    $params[] = $fuel_filter;
    $types .= "s";
}

if ($min_price > 0) {
    $where_conditions[] = "daily_rate >= ?";
    $params[] = $min_price;
    $types .= "i";
}

if ($max_price < 100000) {
    $where_conditions[] = "daily_rate <= ?";
    $params[] = $max_price;
    $types .= "i";
}

if (!empty($search)) {
    $where_conditions[] = "(brand LIKE ? OR model LIKE ? OR type LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

$where_clause = implode(" AND ", $where_conditions);

// Get total number of vehicles
$total_sql = "SELECT COUNT(*) as total FROM vehicles WHERE $where_clause";
$total_stmt = $conn->prepare($total_sql);

if (!empty($params)) {
    $total_stmt->bind_param($types, ...$params);
}
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_vehicles = $total_row['total'];
$total_stmt->close();

// Calculate total pages
$total_pages = ceil($total_vehicles / $vehicles_per_page);

// Fetch vehicles for current page
$sql = "SELECT * FROM vehicles WHERE $where_clause ORDER BY id LIMIT $offset, $vehicles_per_page";
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$vehicles = [];
while ($row = $result->fetch_assoc()) {
    $vehicles[] = $row;
}
$stmt->close();

// Get unique vehicle types for filter
$type_sql = "SELECT DISTINCT type FROM vehicles WHERE status = 'available' ORDER BY type";
$type_result = $conn->query($type_sql);
$vehicle_types = [];
while ($row = $type_result->fetch_assoc()) {
    $vehicle_types[] = $row['type'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicles - Travel_X Vehicle Rental</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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

        .logout-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        /* Main Container */
        .vehicles-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Hero Section */
        .vehicles-hero {
            background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.9));
            border-radius: 24px;
            padding: 50px 40px;
            text-align: center;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .vehicles-hero h1 {
            font-size: 2.8rem;
            color: #1f6e43;
            margin-bottom: 15px;
        }

        .vehicles-hero p {
            font-size: 1.1rem;
            color: #666;
            max-width: 700px;
            margin: 0 auto;
        }

        /* Search Bar */
        .search-section {
            margin-bottom: 30px;
        }

        .search-box {
            background: white;
            border-radius: 60px;
            padding: 5px 5px 5px 25px;
            display: flex;
            gap: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .search-box input {
            flex: 1;
            border: none;
            padding: 15px 0;
            font-size: 1rem;
            outline: none;
            background: transparent;
        }

        .search-box button {
            background: #1f6e43;
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .search-box button:hover {
            background: #155a38;
            transform: translateY(-2px);
        }

        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .filter-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .filter-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }

        .filter-group select, 
        .filter-group input {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.9rem;
            outline: none;
            transition: 0.3s;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            border-color: #1f6e43;
        }

        .price-range {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .price-range input {
            flex: 1;
        }

        .filter-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
        }

        .btn-filter {
            background: #1f6e43;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-reset {
            background: #e2e8f0;
            color: #333;
            border: none;
            padding: 10px 25px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-filter:hover, .btn-reset:hover {
            transform: translateY(-2px);
        }

        /* Results Info */
        .results-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            color: white;
        }

        .results-count {
            background: rgba(0,0,0,0.2);
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 0.9rem;
        }

        /* Vehicles Grid */
        .vehicles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        /* Vehicle Card */
        .vehicle-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
        }

        .vehicle-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .vehicle-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            z-index: 2;
        }

        .badge-new {
            background: #4cd964;
            color: white;
        }

        .badge-popular {
            background: #ff9500;
            color: white;
        }

        .badge-premium {
            background: #af52de;
            color: white;
        }

        .vehicle-image {
            height: 220px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .vehicle-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .vehicle-card:hover .vehicle-image img {
            transform: scale(1.05);
        }

        .vehicle-image i {
            font-size: 4rem;
            color: #1f6e43;
        }

        .vehicle-content {
            padding: 20px;
        }

        .vehicle-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .vehicle-type {
            font-size: 0.8rem;
            color: #1f6e43;
            margin-bottom: 12px;
        }

        .vehicle-specs {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin: 12px 0;
            padding: 12px 0;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }

        .spec {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            color: #666;
        }

        .spec i {
            color: #1f6e43;
            font-size: 0.8rem;
        }

        .vehicle-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f6e43;
            margin: 12px 0;
        }

        .vehicle-price span {
            font-size: 0.8rem;
            color: #666;
            font-weight: normal;
        }

        .vehicle-actions {
            display: flex;
            gap: 12px;
            margin-top: 15px;
        }

        .btn-details {
            flex: 1;
            background: #f1f5f9;
            color: #333;
            text-decoration: none;
            text-align: center;
            padding: 10px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-details:hover {
            background: #e2e8f0;
        }

        .btn-rent {
            flex: 1.5;
            background: linear-gradient(135deg, #1f6e43, #2b9b5e);
            color: white;
            text-decoration: none;
            text-align: center;
            padding: 10px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: 0.3s;
            display: inline-block;
        }

        .btn-rent:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(31, 110, 67, 0.3);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin: 40px 0;
        }

        .page-link {
            min-width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 10px;
            color: #333;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .page-link:hover,
        .page-link.active {
            background: #1f6e43;
            color: white;
        }

        .page-dots {
            color: white;
            padding: 0 5px;
        }

        /* No Results */
        .no-results {
            background: white;
            border-radius: 20px;
            padding: 60px;
            text-align: center;
            grid-column: 1 / -1;
        }

        .no-results i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .no-results h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .no-results p {
            color: #666;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 70px 15px 30px;
            }
            .navbar {
                flex-direction: column;
            }
            .vehicles-hero h1 {
                font-size: 2rem;
            }
            .vehicles-grid {
                grid-template-columns: 1fr;
            }
            .filter-grid {
                grid-template-columns: 1fr;
            }
            .search-box {
                flex-direction: column;
                border-radius: 20px;
                background: white;
                padding: 15px;
            }
            .search-box button {
                width: 100%;
            }
            .vehicle-actions {
                flex-direction: column;
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
        <a href="vehicles.php" class="active"><i class="fas fa-car"></i> Vehicles</a>
        <a href="bookings.php"><i class="fas fa-bookmark"></i> My Bookings</a>
        <a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="loging.php" class="logout-btn"><i class="fas fa-sign-in-alt"></i> Login</a>
        <?php endif; ?>
    </div>
</div>

<div class="vehicles-container">
    <!-- Hero Section -->
    <div class="vehicles-hero">
        <h1><i class="fas fa-car"></i> Our Vehicle Fleet</h1>
        <p>Choose from a wide range of well-maintained vehicles - from luxury cars to trucks and lorries. All vehicles are regularly serviced for your safety and comfort.</p>
    </div>

    <!-- Search Bar -->
    <div class="search-section">
        <form method="GET" action="">
            <div class="search-box">
                <input type="text" name="search" placeholder="Search by brand, model or type..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fas fa-search"></i> Search</button>
            </div>
        </form>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-title">
            <i class="fas fa-filter"></i> Filter Vehicles
        </div>
        <form method="GET" action="" id="filterForm">
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
            <div class="filter-grid">
                <div class="filter-group">
                    <label><i class="fas fa-car"></i> Vehicle Type</label>
                    <select name="type">
                        <option value="">All Types</option>
                        <?php foreach ($vehicle_types as $type): ?>
                            <option value="<?php echo htmlspecialchars($type); ?>" <?php echo $type_filter == $type ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($type); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label><i class="fas fa-cogs"></i> Transmission</label>
                    <select name="transmission">
                        <option value="">All</option>
                        <option value="Automatic" <?php echo $transmission_filter == 'Automatic' ? 'selected' : ''; ?>>Automatic</option>
                        <option value="Manual" <?php echo $transmission_filter == 'Manual' ? 'selected' : ''; ?>>Manual</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label><i class="fas fa-gas-pump"></i> Fuel Type</label>
                    <select name="fuel">
                        <option value="">All</option>
                        <option value="Petrol" <?php echo $fuel_filter == 'Petrol' ? 'selected' : ''; ?>>Petrol</option>
                        <option value="Diesel" <?php echo $fuel_filter == 'Diesel' ? 'selected' : ''; ?>>Diesel</option>
                        <option value="Electric" <?php echo $fuel_filter == 'Electric' ? 'selected' : ''; ?>>Electric</option>
                        <option value="Hybrid" <?php echo $fuel_filter == 'Hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label><i class="fas fa-tag"></i> Price Range (per day)</label>
                    <div class="price-range">
                        <input type="number" name="min_price" placeholder="Min" value="<?php echo $min_price > 0 ? $min_price : ''; ?>">
                        <span>-</span>
                        <input type="number" name="max_price" placeholder="Max" value="<?php echo $max_price < 100000 ? $max_price : ''; ?>">
                    </div>
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-filter"><i class="fas fa-check"></i> Apply Filters</button>
                <a href="vehicles.php" class="btn-reset"><i class="fas fa-undo"></i> Reset All</a>
            </div>
        </form>
    </div>

    <!-- Results Info -->
    <div class="results-info">
        <div class="results-count">
            <i class="fas fa-car"></i> <?php echo $total_vehicles; ?> vehicles found
        </div>
    </div>

    <!-- Vehicles Grid -->
    <div class="vehicles-grid">
        <?php if (count($vehicles) > 0): ?>
            <?php foreach ($vehicles as $vehicle): 
                // Determine badge
                $badge = '';
                if ($vehicle['year'] >= 2023) {
                    $badge = '<span class="vehicle-badge badge-new"><i class="fas fa-star"></i> New</span>';
                } elseif ($vehicle['daily_rate'] > 15000) {
                    $badge = '<span class="vehicle-badge badge-premium"><i class="fas fa-gem"></i> Premium</span>';
                } elseif (strpos($vehicle['type'], 'SUV') !== false || strpos($vehicle['type'], 'Sports') !== false) {
                    $badge = '<span class="vehicle-badge badge-popular"><i class="fas fa-fire"></i> Popular</span>';
                }
                
                // Image path
                $image = $vehicle['image_url'] ?? '';
                if (empty($image) || !file_exists($image)) {
                    $image = '';
                }
            ?>
            <div class="vehicle-card">
                <?php echo $badge; ?>
                <div class="vehicle-image">
                    <?php if ($image): ?>
                        <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?>">
                    <?php else: ?>
                        <i class="fas fa-car"></i>
                    <?php endif; ?>
                </div>
                <div class="vehicle-content">
                    <h3 class="vehicle-title"><?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?></h3>
                    <div class="vehicle-type"><?php echo htmlspecialchars($vehicle['type']); ?></div>
                    <div class="vehicle-specs">
                        <span class="spec"><i class="fas fa-calendar"></i> <?php echo $vehicle['year']; ?></span>
                        <span class="spec"><i class="fas fa-cogs"></i> <?php echo $vehicle['transmission']; ?></span>
                        <span class="spec"><i class="fas fa-gas-pump"></i> <?php echo $vehicle['fuel_type']; ?></span>
                        <span class="spec"><i class="fas fa-tachometer-alt"></i> <?php echo $vehicle['top_speed']; ?> mph</span>
                        <span class="spec"><i class="fas fa-palette"></i> <?php echo $vehicle['color']; ?></span>
                    </div>
                    <div class="vehicle-price">
                        NPR <?php echo number_format($vehicle['daily_rate'], 0); ?> <span>/ day</span>
                    </div>
                    <div class="vehicle-actions">
                        <a href="vehicle-details.php?id=<?php echo $vehicle['id']; ?>" class="btn-details">
                            <i class="fas fa-info-circle"></i> Details
                        </a>
                        <a href="checkout.php?id=<?php echo $vehicle['id']; ?>" class="btn-rent">
                            <i class="fas fa-shopping-cart"></i> Rent Now
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-car-side"></i>
                <h3>No Vehicles Found</h3>
                <p>We couldn't find any vehicles matching your criteria. Please try different filters.</p>
                <a href="vehicles.php" class="btn-rent" style="display: inline-block; margin-top: 20px; padding: 12px 30px;">
                    <i class="fas fa-undo"></i> Reset Filters
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page-1; ?>&type=<?php echo urlencode($type_filter); ?>&transmission=<?php echo urlencode($transmission_filter); ?>&fuel=<?php echo urlencode($fuel_filter); ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>&search=<?php echo urlencode($search); ?>" class="page-link">
                <i class="fas fa-chevron-left"></i>
            </a>
        <?php endif; ?>
        
        <?php
        $start_page = max(1, $page - 2);
        $end_page = min($total_pages, $page + 2);
        
        if ($start_page > 1) {
            echo '<a href="?page=1&type=' . urlencode($type_filter) . '&transmission=' . urlencode($transmission_filter) . '&fuel=' . urlencode($fuel_filter) . '&min_price=' . $min_price . '&max_price=' . $max_price . '&search=' . urlencode($search) . '" class="page-link">1</a>';
            if ($start_page > 2) {
                echo '<span class="page-dots">...</span>';
            }
        }
        
        for ($i = $start_page; $i <= $end_page; $i++) {
            $active = ($i == $page) ? 'active' : '';
            echo '<a href="?page=' . $i . '&type=' . urlencode($type_filter) . '&transmission=' . urlencode($transmission_filter) . '&fuel=' . urlencode($fuel_filter) . '&min_price=' . $min_price . '&max_price=' . $max_price . '&search=' . urlencode($search) . '" class="page-link ' . $active . '">' . $i . '</a>';
        }
        
        if ($end_page < $total_pages) {
            if ($end_page < $total_pages - 1) {
                echo '<span class="page-dots">...</span>';
            }
            echo '<a href="?page=' . $total_pages . '&type=' . urlencode($type_filter) . '&transmission=' . urlencode($transmission_filter) . '&fuel=' . urlencode($fuel_filter) . '&min_price=' . $min_price . '&max_price=' . $max_price . '&search=' . urlencode($search) . '" class="page-link">' . $total_pages . '</a>';
        }
        ?>
        
        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page+1; ?>&type=<?php echo urlencode($type_filter); ?>&transmission=<?php echo urlencode($transmission_filter); ?>&fuel=<?php echo urlencode($fuel_filter); ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>&search=<?php echo urlencode($search); ?>" class="page-link">
                <i class="fas fa-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
    // Auto-submit filter form when select changes
    const filterSelects = document.querySelectorAll('#filterForm select');
    filterSelects.forEach(select => {
        select.addEventListener('change', () => {
            document.getElementById('filterForm').submit();
        });
    });
</script>

</body>
</html>