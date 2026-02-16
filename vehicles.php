<?php
session_start();
include("connection.php");

// Get current page number
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Number of vehicles per page
$vehicles_per_page = 8;

// Calculate offset
$offset = ($page - 1) * $vehicles_per_page;

// Get total number of vehicles
$total_sql = "SELECT COUNT(*) as total FROM vehicles WHERE status = 'available'";
$total_result = mysqli_query($conn, $total_sql);
$total_row = mysqli_fetch_assoc($total_result);
$total_vehicles = $total_row['total'];

// Calculate total pages
$total_pages = ceil($total_vehicles / $vehicles_per_page);

// Fetch vehicles for current page
$sql = "SELECT * FROM vehicles WHERE status = 'available' ORDER BY id LIMIT $offset, $vehicles_per_page";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicles - Travel_X Vehicle Rental</title>
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />

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

        /* Vehicles Hero */
        .vehicles-hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            border-radius: 15px;
            margin-bottom: 40px;
        }

        .vehicles-hero h1 {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .vehicles-hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
            opacity: 0.9;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: var(--box-shadow);
            margin-bottom: 40px;
        }

        .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .filter-header h2 {
            color: var(--dark);
            font-size: 1.8rem;
        }

        .filter-actions {
            display: flex;
            gap: 15px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .filter-group {
            margin-bottom: 15px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 12px 15px;
            border: var(--border);
            border-radius: 8px;
            font-size: 1rem;
            background: var(--light);
        }

        .price-range {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .price-range input {
            flex: 1;
        }

        .apply-filters {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .apply-filters:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .reset-filters {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
            padding: 10px 25px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .reset-filters:hover {
            background: var(--primary);
            color: white;
        }

        /* Vehicles Grid */
        .vehicles-container {
            margin-bottom: 50px;
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

        .vehicles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }

        /* Vehicle Card */
        .vehicle-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: all 0.3s ease;
            position: relative;
        }

        .vehicle-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .vehicle-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--primary);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 2;
        }

        .vehicle-badge.popular {
            background: var(--secondary);
            color: var(--dark);
        }

        .vehicle-badge.new {
            background: #4cd964;
        }

        .vehicle-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .vehicle-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .vehicle-card:hover .vehicle-image img {
            transform: scale(1.1);
        }

        .vehicle-content {
            padding: 25px;
        }

        .vehicle-title {
            font-size: 1.5rem;
            color: var(--dark);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .vehicle-price {
            font-size: 1.8rem;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 15px;
        }

        .vehicle-price span {
            font-size: 1rem;
            color: var(--light-color);
            font-weight: normal;
        }

        .vehicle-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--light-color);
            font-size: 0.9rem;
        }

        .detail-item i {
            color: var(--primary);
            font-size: 1rem;
        }

        .vehicle-features {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }

        .feature-tag {
            background: rgba(102, 126, 234, 0.1);
            color: var(--primary);
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .vehicle-actions {
            display: flex;
            gap: 10px;
        }

        .btn-details {
            flex: 1;
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
        }

        .btn-details:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-rent {
            flex: 2;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-rent:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        /* Categories Section */
        .categories-section {
            margin-bottom: 50px;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        .category-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: var(--box-shadow);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .category-card:hover {
            transform: translateY(-5px);
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .category-card:hover .category-icon {
            background: white;
            color: var(--primary);
        }

        .category-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 2rem;
            transition: all 0.3s ease;
        }

        .category-card h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .category-card p {
            color: var(--light-color);
            font-size: 0.9rem;
        }

        .category-card:hover p {
            color: rgba(255, 255, 255, 0.9);
        }

        /* Why Choose Us */
        .features-section {
            background: white;
            padding: 60px 20px;
            border-radius: 15px;
            margin-bottom: 50px;
            box-shadow: var(--box-shadow);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .feature-item {
            text-align: center;
            padding: 30px;
            border-radius: 10px;
            background: var(--light);
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            transform: translateY(-5px);
        }

        .feature-item i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .feature-item h3 {
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: var(--dark);
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

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .page-link {
            min-width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 8px;
            color: var(--dark);
            text-decoration: none;
            font-weight: 600;
            box-shadow: var(--box-shadow);
            transition: all 0.3s ease;
            padding: 0 12px;
        }

        .page-link:hover,
        .page-link.active {
            background: var(--primary);
            color: white;
        }

        .page-dots {
            color: var(--light-color);
            font-size: 1.2rem;
            padding: 0 5px;
        }

        .page-info {
            text-align: center;
            margin-top: 20px;
            color: var(--light-color);
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-content {
                margin-top: 70px;
                padding: 15px;
            }

            .vehicles-hero h1 {
                font-size: 2.2rem;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .filter-header {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }

            .filter-actions {
                flex-direction: column;
            }

            .vehicles-grid {
                grid-template-columns: 1fr;
            }

            .vehicle-actions {
                flex-direction: column;
            }

            .categories-grid {
                grid-template-columns: 1fr;
            }

            .pagination {
                gap: 5px;
            }

            .page-link {
                min-width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .vehicle-details {
                grid-template-columns: 1fr;
            }

            .section-header h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <?php include("nav.php"); ?>
    
    <div class="main-content">
        <!-- Hero Section -->
        <section class="vehicles-hero">
            <h1>Explore Our Vehicle Fleet</h1>
            <p>Choose from a wide range of vehicles - from luxury cars to trucks and lorries. All maintained to the highest standards for your comfort and safety.</p>
        </section>

        <!-- Filter Section -->
        <section class="filter-section">
            <div class="filter-header">
                <h2>Filter Vehicles</h2>
                <div class="filter-actions">
                    <button class="apply-filters">Apply Filters</button>
                    <button class="reset-filters">Reset All</button>
                </div>
            </div>
            
            <div class="filter-grid">
                <div class="filter-group">
                    <label for="vehicle-type">Vehicle Type</label>
                    <select id="vehicle-type">
                        <option value="">All Types</option>
                        <option value="car">Cars</option>
                        <option value="truck">Trucks</option>
                        <option value="lorry">Lorries</option>
                        <option value="luxury">Luxury Vehicles</option>
                        <option value="premium">Premium Vehicles</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="transmission">Transmission</label>
                    <select id="transmission">
                        <option value="">All</option>
                        <option value="automatic">Automatic</option>
                        <option value="manual">Manual</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="price-range">Price Range (per day)</label>
                    <div class="price-range">
                        <input type="number" placeholder="Min" id="min-price">
                        <span>to</span>
                        <input type="number" placeholder="Max" id="max-price">
                    </div>
                </div>
                
                <div class="filter-group">
                    <label for="fuel-type">Fuel Type</label>
                    <select id="fuel-type">
                        <option value="">All</option>
                        <option value="petrol">Petrol</option>
                        <option value="diesel">Diesel</option>
                        <option value="electric">Electric</option>
                        <option value="hybrid">Hybrid</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="passengers">Passengers</label>
                    <select id="passengers">
                        <option value="">Any</option>
                        <option value="1-2">1-2</option>
                        <option value="3-4">3-4</option>
                        <option value="5-7">5-7</option>
                        <option value="8+">8+</option>
                    </select>
                </div>
            </div>
        </section>

        <!-- Vehicles Categories -->
        <section class="categories-section">
            <div class="section-header">
                <h2>Vehicle Categories</h2>
                <p>Browse vehicles by category</p>
            </div>
            
            <div class="categories-grid">
                <div class="category-card" data-category="luxury">
                    <div class="category-icon">
                        <i class="fas fa-gem"></i>
                    </div>
                    <h3>Luxury Cars</h3>
                    <p>Premium vehicles for special occasions</p>
                </div>
                
                <div class="category-card" data-category="car">
                    <div class="category-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <h3>General Cars</h3>
                    <p>Daily use vehicles for families</p>
                </div>
                
                <div class="category-card" data-category="truck">
                    <div class="category-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Trucks</h3>
                    <p>Heavy duty vehicles for transport</p>
                </div>
                
                <div class="category-card" data-category="lorry">
                    <div class="category-icon">
                        <i class="fas fa-truck-loading"></i>
                    </div>
                    <h3>Lorries</h3>
                    <p>Commercial vehicles for cargo</p>
                </div>
            </div>
        </section>

        <!-- All Vehicles -->
        <section class="vehicles-container">
            <div class="section-header">
                <h2>Available Vehicles</h2>
                <p>Showing <?php echo min($vehicles_per_page, $total_vehicles - $offset); ?> of <?php echo $total_vehicles; ?> vehicles (Page <?php echo $page; ?> of <?php echo $total_pages; ?>)</p>
            </div>
            
            <div class="vehicles-grid" id="vehiclesGrid">
                <?php 
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($vehicle = mysqli_fetch_assoc($result)) {
                        $image = $vehicle['image_url'] ?? 'image/vehicle-1.png';
                        if (!file_exists($image)) {
                            $image = 'image/vehicle-1.png';
                        }
                        
                        // Determine badge based on vehicle data
                        $badge = '<span class="vehicle-badge">Available</span>';
                        if ($vehicle['year'] >= 2023) {
                            $badge = '<span class="vehicle-badge new">New</span>';
                        } elseif ($vehicle['daily_rate'] > 20000) {
                            $badge = '<span class="vehicle-badge popular">Premium</span>';
                        }
                        
                        // Get category based on vehicle type
                        $category = 'car';
                        if (strpos($vehicle['type'], 'Truck') !== false) $category = 'truck';
                        elseif (strpos($vehicle['type'], 'Lorry') !== false) $category = 'lorry';
                        elseif (strpos($vehicle['type'], 'Luxury') !== false) $category = 'luxury';
                ?>
                <div class="vehicle-card" data-category="<?php echo $category; ?>" data-price="<?php echo $vehicle['daily_rate']; ?>">
                    <?php echo $badge; ?>
                    <div class="vehicle-image">
                        <img src="<?php echo $image; ?>" alt="<?php echo $vehicle['brand'] . ' ' . $vehicle['model']; ?>">
                    </div>
                    <div class="vehicle-content">
                        <h3 class="vehicle-title"><?php echo $vehicle['brand'] . ' ' . $vehicle['model']; ?></h3>
                        <div class="vehicle-price">Rs <?php echo number_format($vehicle['daily_rate'], 0); ?> <span>/day</span></div>
                        <div class="vehicle-details">
                            <div class="detail-item">
                                <i class="fas fa-calendar"></i>
                                <span><?php echo $vehicle['year']; ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-cog"></i>
                                <span><?php echo $vehicle['transmission'] ?? 'Auto'; ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-gas-pump"></i>
                                <span><?php echo $vehicle['fuel_type'] ?? 'Petrol'; ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-tachometer-alt"></i>
                                <span><?php echo $vehicle['top_speed'] ?? '120'; ?> mph</span>
                            </div>
                        </div>
                        <div class="vehicle-features">
                            <span class="feature-tag"><?php echo $vehicle['type']; ?></span>
                            <span class="feature-tag"><?php echo $vehicle['color'] ?? 'N/A'; ?></span>
                        </div>
                        <div class="vehicle-actions">
                            <a href="vehicle-details.php?id=<?php echo $vehicle['id']; ?>" class="btn-details">View Details</a>
                            <button class="btn-rent">Rent Now</button>
                        </div>
                    </div>
                </div>
                <?php 
                    }
                } else {
                    echo "<p style='text-align:center; grid-column:1/-1; padding:50px;'>No vehicles found.</p>";
                }
                ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <!-- Previous button -->
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page-1; ?>" class="page-link"><i class="fas fa-chevron-left"></i></a>
                <?php else: ?>
                    <span class="page-link" style="opacity:0.5; cursor:not-allowed;"><i class="fas fa-chevron-left"></i></span>
                <?php endif; ?>
                
                <!-- Page numbers -->
                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                if ($start_page > 1) {
                    echo '<a href="?page=1" class="page-link">1</a>';
                    if ($start_page > 2) {
                        echo '<span class="page-dots">...</span>';
                    }
                }
                
                for ($i = $start_page; $i <= $end_page; $i++) {
                    $active = ($i == $page) ? 'active' : '';
                    echo '<a href="?page=' . $i . '" class="page-link ' . $active . '">' . $i . '</a>';
                }
                
                if ($end_page < $total_pages) {
                    if ($end_page < $total_pages - 1) {
                        echo '<span class="page-dots">...</span>';
                    }
                    echo '<a href="?page=' . $total_pages . '" class="page-link">' . $total_pages . '</a>';
                }
                ?>
                
                <!-- Next button -->
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page+1; ?>" class="page-link"><i class="fas fa-chevron-right"></i></a>
                <?php else: ?>
                    <span class="page-link" style="opacity:0.5; cursor:not-allowed;"><i class="fas fa-chevron-right"></i></span>
                <?php endif; ?>
            </div>
            
            <div class="page-info">
                Showing page <?php echo $page; ?> of <?php echo $total_pages; ?> 
            </div>
            <?php endif; ?>
        </section>

        <!-- Why Choose Us -->
        <section class="features-section">
            <div class="section-header">
                <h2>Why Choose Travel_X?</h2>
                <p>We provide the best vehicle rental experience in Nepal</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Fully Insured</h3>
                    <p>All vehicles come with comprehensive insurance coverage</p>
                </div>
                
                <div class="feature-item">
                    <i class="fas fa-headset"></i>
                    <h3>24/7 Support</h3>
                    <p>Round-the-clock customer service and roadside assistance</p>
                </div>
                
                <div class="feature-item">
                    <i class="fas fa-car"></i>
                    <h3>Well Maintained</h3>
                    <p>Regular servicing and maintenance of all vehicles</p>
                </div>
                
                <div class="feature-item">
                    <i class="fas fa-tags"></i>
                    <h3>Best Prices</h3>
                    <p>Competitive rates with no hidden charges</p>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq-section">
            <div class="section-header">
                <h2>Frequently Asked Questions</h2>
                <p>Find answers to common questions about vehicle rental</p>
            </div>
            
            <div class="faq-container">
                <div class="faq-item">
                    <button class="faq-question">
                        What documents do I need to rent a vehicle?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>You need a valid driving license, citizenship card or passport, and a security deposit. For international tourists, an international driving permit along with passport is required.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        Is there a mileage limit?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>We offer flexible packages. Standard packages include 200km per day, while premium packages offer unlimited mileage. Additional kilometers are charged at Rs 50/km.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        Can I get a driver with the vehicle?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Yes, we provide experienced drivers at an additional cost of Rs 1,500 per day. All our drivers are licensed and familiar with Nepal's roads.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        What is your fuel policy?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Vehicles are provided with a full tank and should be returned with a full tank. If returned without a full tank, refueling charges will apply at market rates plus a service fee.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    <?php include("footer.php"); ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter functionality
            const applyFiltersBtn = document.querySelector('.apply-filters');
            const resetFiltersBtn = document.querySelector('.reset-filters');
            const categoryCards = document.querySelectorAll('.category-card');
            const vehicleCards = document.querySelectorAll('.vehicle-card');
            
            // Category filtering
            categoryCards.forEach(card => {
                card.addEventListener('click', function() {
                    const category = this.getAttribute('data-category');
                    filterVehicles(category);
                    
                    // Update active category
                    categoryCards.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            // Apply filters
            applyFiltersBtn.addEventListener('click', function() {
                const vehicleType = document.getElementById('vehicle-type').value;
                const transmission = document.getElementById('transmission').value;
                const minPrice = document.getElementById('min-price').value;
                const maxPrice = document.getElementById('max-price').value;
                const fuelType = document.getElementById('fuel-type').value;
                const passengers = document.getElementById('passengers').value;
                
                filterVehicles(vehicleType, {transmission, minPrice, maxPrice, fuelType, passengers});
            });
            
            // Reset filters
            resetFiltersBtn.addEventListener('click', function() {
                // Reset all filter inputs
                document.getElementById('vehicle-type').value = '';
                document.getElementById('transmission').value = '';
                document.getElementById('min-price').value = '';
                document.getElementById('max-price').value = '';
                document.getElementById('fuel-type').value = '';
                document.getElementById('passengers').value = '';
                
                // Reset category cards
                categoryCards.forEach(card => card.classList.remove('active'));
                
                // Show all vehicles
                vehicleCards.forEach(card => {
                    card.style.display = 'block';
                });
            });
            
            // Filter vehicles function
            function filterVehicles(category, additionalFilters = {}) {
                vehicleCards.forEach(card => {
                    const cardCategory = card.getAttribute('data-category');
                    const cardPrice = parseFloat(card.getAttribute('data-price'));
                    
                    let showCard = true;
                    
                    // Filter by category
                    if (category && category !== '' && cardCategory !== category) {
                        showCard = false;
                    }
                    
                    // Filter by price range
                    if (additionalFilters.minPrice && cardPrice < parseFloat(additionalFilters.minPrice)) {
                        showCard = false;
                    }
                    
                    if (additionalFilters.maxPrice && cardPrice > parseFloat(additionalFilters.maxPrice)) {
                        showCard = false;
                    }
                    
                    // Show or hide card
                    card.style.display = showCard ? 'block' : 'none';
                });
            }
            
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
            
            // Rent Now button functionality
            const rentButtons = document.querySelectorAll('.btn-rent');
            rentButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const vehicleCard = this.closest('.vehicle-card');
                    const vehicleName = vehicleCard.querySelector('.vehicle-title').textContent;
                    const vehiclePrice = vehicleCard.querySelector('.vehicle-price').textContent;
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        // If logged in, redirect to booking page
                        alert(`Renting ${vehicleName} for ${vehiclePrice}`);
                        // In real implementation, redirect to booking page with vehicle details
                        // window.location.href = `booking.php?vehicle=${encodeURIComponent(vehicleName)}`;
                    <?php else: ?>
                        // If not logged in, show login modal
                        alert('Please login to rent a vehicle');
                        // Show login modal
                        const loginBtn = document.getElementById('login-btn');
                        if (loginBtn) {
                            loginBtn.click();
                        }
                    <?php endif; ?>
                });
            });
            
            // View Details button functionality
            const detailsButtons = document.querySelectorAll('.btn-details');
            detailsButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const vehicleCard = this.closest('.vehicle-card');
                    const vehicleName = vehicleCard.querySelector('.vehicle-title').textContent;
                    
                    // Show vehicle details modal or redirect to details page
                    alert(`Viewing details for ${vehicleName}`);
                    // In real implementation:
                    // window.location.href = `vehicle-details.php?id=${vehicleId}`;
                });
            });
            
            // Add animation to vehicle cards on scroll
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
            
            // Set initial styles for animation
            vehicleCards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(card);
            });
        });
    </script>
</body>
</html>