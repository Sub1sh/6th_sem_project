<?php
// search.php - Complete Vehicle Search with Fuzzy Matching

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
    <title>Search Vehicles - Rental System</title>
    
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
            padding-top: 80px; /* Added for fixed navbar */
        }
        
        .search-container {
            max-width: 1400px;
            margin: 30px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .search-header {
            background: linear-gradient(to right, #4A00E0, #8E2DE2);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .search-header h1 {
            font-size: 2.8rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .search-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .search-box-container {
            padding: 30px 40px;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
        }
        
        .search-box {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .search-input {
            width: 100%;
            padding: 20px 25px;
            padding-right: 70px;
            font-size: 1.2rem;
            border: 3px solid #e0e0e0;
            border-radius: 15px;
            transition: all 0.3s ease;
            outline: none;
        }
        
        .search-input:focus {
            border-color: #8E2DE2;
            box-shadow: 0 0 0 0.25rem rgba(142, 45, 226, 0.25);
        }
        
        .search-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: #8E2DE2;
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .search-btn:hover {
            background: #4A00E0;
            transform: translateY(-50%) scale(1.05);
        }
        
        .filters {
            padding: 20px 40px;
            background: white;
            border-bottom: 1px solid #eee;
        }
        
        .filter-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }
        
        .filter-tag {
            background: #f0f0f0;
            padding: 8px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            border: 2px solid transparent;
            text-decoration: none;
            color: #333;
            display: inline-block;
        }
        
        .filter-tag:hover, .filter-tag.active {
            background: #8E2DE2;
            color: white;
            border-color: #8E2DE2;
            text-decoration: none;
        }
        
        .results-container {
            padding: 40px;
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
            line-height: 1.3;
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
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .book-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(142, 45, 226, 0.3);
            color: white;
            text-decoration: none;
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
        
        .suggestions-box {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            margin-top: 5px;
        }
        
        .suggestion-item {
            padding: 15px 20px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .suggestion-item:hover {
            background: #f8f9fa;
        }
        
        .suggestion-item:last-child {
            border-bottom: none;
        }
        
        .suggestion-name {
            font-weight: 600;
            color: #333;
        }
        
        .suggestion-type {
            font-size: 0.9rem;
            color: #666;
            background: #f0f0f0;
            padding: 3px 10px;
            border-radius: 15px;
        }
        
        .highlight {
            background-color: #fffacd;
            font-weight: bold;
            padding: 2px;
            border-radius: 3px;
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
            
            .search-header h1 {
                font-size: 2rem;
            }
            
            .search-input {
                padding: 15px 20px;
                font-size: 1rem;
            }
            
            .results-grid {
                grid-template-columns: 1fr;
            }
            
            .filters {
                padding: 15px 20px;
            }
            
            .filter-tags {
                gap: 8px;
            }
            
            .filter-tag {
                padding: 6px 15px;
                font-size: 0.9rem;
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
        
        .vehicle-status.rented {
            background: rgba(244, 67, 54, 0.9);
        }
        
        .vehicle-status.maintenance {
            background: rgba(255, 152, 0, 0.9);
        }
    </style>
</head>
<body>

<!-- Include Navbar -->
<?php include_once('nav.php'); ?>

<div class="search-container">
    <!-- Header -->
    <div class="search-header">
        <h1><i class="fas fa-search"></i> Find Your Perfect Ride</h1>
        <p>Search from 47 vehicles. Spelling mistakes? No problem! Try "truk", "seden", "toyotta"</p>
    </div>
    
    <!-- Search Box -->
    <div class="search-box-container">
        <div class="search-box">
            <form method="GET" action="" id="searchForm">
                <input type="text" 
                       name="q"
                       id="searchInput" 
                       class="search-input" 
                       placeholder="Search vehicles by name, brand, type (e.g., 'toyota', 'sedan', 'SUV', 'truk', 'bus')..."
                       autocomplete="off"
                       value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="search-btn" id="searchBtn">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            
            <!-- Suggestions Dropdown -->
            <div class="suggestions-box" id="suggestionsBox"></div>
        </div>
    </div>
    
    <!-- Quick Filters -->
    <div class="filters">
        <h5 class="text-center mb-3">Quick Filters:</h5>
        <div class="filter-tags">
            <a href="?q=<?php echo urlencode($search_query); ?>&filter=all" 
               class="filter-tag <?php echo ($filter == 'all') ? 'active' : ''; ?>" 
               data-filter="all">All Vehicles</a>
            <a href="?q=<?php echo urlencode($search_query); ?>&filter=sedan" 
               class="filter-tag <?php echo ($filter == 'sedan') ? 'active' : ''; ?>" 
               data-filter="sedan">Sedan</a>
            <a href="?q=<?php echo urlencode($search_query); ?>&filter=suv" 
               class="filter-tag <?php echo ($filter == 'suv') ? 'active' : ''; ?>" 
               data-filter="suv">SUV</a>
            <a href="?q=<?php echo urlencode($search_query); ?>&filter=truck" 
               class="filter-tag <?php echo ($filter == 'truck') ? 'active' : ''; ?>" 
               data-filter="truck">Truck</a>
            <a href="?q=<?php echo urlencode($search_query); ?>&filter=bus" 
               class="filter-tag <?php echo ($filter == 'bus') ? 'active' : ''; ?>" 
               data-filter="bus">Bus</a>
            <a href="?q=<?php echo urlencode($search_query); ?>&filter=van" 
               class="filter-tag <?php echo ($filter == 'van') ? 'active' : ''; ?>" 
               data-filter="van">Van</a>
            <a href="?q=<?php echo urlencode($search_query); ?>&filter=motorcycle" 
               class="filter-tag <?php echo ($filter == 'motorcycle') ? 'active' : ''; ?>" 
               data-filter="motorcycle">Bike</a>
            <a href="?q=<?php echo urlencode($search_query); ?>&filter=luxury" 
               class="filter-tag <?php echo ($filter == 'luxury') ? 'active' : ''; ?>" 
               data-filter="luxury">Luxury</a>
            <a href="?q=<?php echo urlencode($search_query); ?>&filter=electric" 
               class="filter-tag <?php echo ($filter == 'electric') ? 'active' : ''; ?>" 
               data-filter="electric">Electric</a>
        </div>
    </div>
    
    <!-- Results -->
    <div class="results-container">
        <?php
        // SIMPLE SEARCH LOGIC - Fixed version
        $sql = "SELECT * FROM vehicles WHERE status = 'available'";
        
        // Add search conditions
        if (!empty($search_query)) {
            $search_terms = explode(' ', $search_query);
            $search_conditions = [];
            
            // Escape search terms for safety
            foreach ($search_terms as $term) {
                if (strlen($term) > 1) {
                    $escaped_term = mysqli_real_escape_string($conn, $term);
                    $search_conditions[] = "(brand LIKE '%$escaped_term%' OR 
                                           model LIKE '%$escaped_term%' OR 
                                           type LIKE '%$escaped_term%' OR 
                                           description LIKE '%$escaped_term%' OR 
                                           tags LIKE '%$escaped_term%')";
                }
            }
            
            if (!empty($search_conditions)) {
                $sql .= " AND (" . implode(" AND ", $search_conditions) . ")";
            }
        }
        
        // Add filter conditions
        if ($filter != 'all') {
            $escaped_filter = mysqli_real_escape_string($conn, $filter);
            $sql .= " AND (type LIKE '%$escaped_filter%' OR 
                          brand LIKE '%$escaped_filter%' OR 
                          tags LIKE '%$escaped_filter%')";
        }
        
        // Add ordering
        $sql .= " ORDER BY daily_rate ASC";
        
        // Debug: Uncomment to see the SQL query
        // echo "<!-- DEBUG SQL: $sql -->";
        
        // Execute query
        $result = mysqli_query($conn, $sql);
        
        if ($result === false) {
            // Handle query error
            echo '<div class="alert alert-danger">Database error: ' . mysqli_error($conn) . '</div>';
            $total_results = 0;
        } else {
            $total_results = mysqli_num_rows($result);
        }
        
        // Display search info
        echo '<div class="search-info">';
        if (!empty($search_query) || $filter != 'all') {
            echo 'Found <strong>' . $total_results . '</strong> vehicles';
            if (!empty($search_query)) {
                echo ' for "<strong>' . htmlspecialchars($search_query) . '</strong>"';
            }
            if ($filter != 'all') {
                echo ' in <strong>' . ucfirst($filter) . '</strong> category';
            }
        } else {
            echo 'Showing all <strong>' . $total_results . '</strong> available vehicles';
        }
        echo '</div>';
        
        if ($result && $total_results > 0) {
            echo '<div class="results-grid">';
            while ($row = mysqli_fetch_assoc($result)) {
                // Handle image fallback
                $image_url = $row['image_url'];
                if (empty($image_url) || !file_exists($image_url)) {
                    // Try different image paths
                    $possible_paths = [
                        'images/vehicle-' . ($row['id'] % 6 + 1) . '.png',
                        'images/car-' . ($row['id'] % 8 + 1) . '.png',
                        'images/vehicles ' . ($row['id'] % 6 + 1) . '.jpg',
                        'images/vehicles ' . ($row['id'] % 6 + 1) . '.png',
                        'images/default-vehicle.jpg'
                    ];
                    
                    foreach ($possible_paths as $path) {
                        if (file_exists($path)) {
                            $image_url = $path;
                            break;
                        }
                    }
                    
                    if (empty($image_url)) {
                        $image_url = 'https://images.unsplash.com/photo-1553440569-bcc63803a83d?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
                    }
                }
                
                // Status badge class
                $status_class = strtolower($row['status']);
                $status_text = ucfirst($row['status']);
                
                // Highlight search terms in text
                $highlighted_model = $row['model'];
                $highlighted_brand = $row['brand'];
                $highlighted_description = $row['description'];
                
                if (!empty($search_query)) {
                    $search_terms = explode(' ', $search_query);
                    foreach ($search_terms as $term) {
                        if (strlen($term) > 1) {
                            $highlighted_model = preg_replace("/($term)/i", '<span class="highlight">$1</span>', $highlighted_model);
                            $highlighted_brand = preg_replace("/($term)/i", '<span class="highlight">$1</span>', $highlighted_brand);
                            $highlighted_description = preg_replace("/($term)/i", '<span class="highlight">$1</span>', $highlighted_description);
                        }
                    }
                }
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
                        <h3 class="vehicle-title"><?php echo $highlighted_model; ?></h3>
                        
                        <div class="vehicle-brand">
                            <i class="fas fa-car"></i>
                            <?php echo $highlighted_brand; ?> • <?php echo $row['year']; ?>
                        </div>
                        
                        <div class="vehicle-price">
                            Rs. <?php echo number_format($row['daily_rate'], 2); ?>/day
                        </div>
                        
                        <div class="vehicle-details">
                            <span class="detail-badge">
                                <i class="fas fa-cog"></i>
                                <?php echo htmlspecialchars($row['transmission']); ?>
                            </span>
                            <span class="detail-badge">
                                <i class="fas fa-gas-pump"></i>
                                <?php echo htmlspecialchars($row['fuel_type']); ?>
                            </span>
                            <span class="detail-badge">
                                <i class="fas fa-tachometer-alt"></i>
                                <?php echo htmlspecialchars($row['top_speed']); ?> km/h
                            </span>
                            <span class="detail-badge">
                                <i class="fas fa-palette"></i>
                                <?php echo htmlspecialchars($row['color']); ?>
                            </span>
                        </div>
                        
                        <p class="vehicle-description">
                            <?php echo $highlighted_description; ?>
                        </p>
                        
                        <a href="book.php?id=<?php echo $row['id']; ?>" class="book-btn">
                            <i class="fas fa-calendar-check"></i> Book Now
                        </a>
                    </div>
                </div>
                <?php
            }
            echo '</div>';
            // Free result set
            mysqli_free_result($result);
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
        
        // Close connection (optional, as it will close automatically)
        // mysqli_close($conn);
        ?>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Search suggestions script -->
<script>
$(document).ready(function() {
    const searchInput = $('#searchInput');
    const suggestionsBox = $('#suggestionsBox');
    
    // Common misspellings and corrections
    const spellingCorrections = {
        'truk': 'truck',
        'seden': 'sedan',
        'toyotta': 'toyota',
        'hnda': 'honda',
        'fordd': 'ford',
        'bmvv': 'bmw',
        'mersedes': 'mercedes',
        'automatik': 'automatic',
        'manul': 'manual',
        'elecric': 'electric',
        'diseal': 'diesel',
        'patrol': 'petrol',
        'scootr': 'scooter',
        'bajjaj': 'bajaj',
        'mahindr': 'mahindra',
        'ashok': 'ashok leyland',
        'volvoo': 'volvo',
        'marut': 'maruti',
        'hyundae': 'hyundai'
    };
    
    // Common search suggestions
    const commonSearches = [
        {name: 'Toyota', type: 'Brand'},
        {name: 'Honda', type: 'Brand'},
        {name: 'Ford', type: 'Brand'},
        {name: 'SUV', type: 'Type'},
        {name: 'Sedan', type: 'Type'},
        {name: 'Truck', type: 'Type'},
        {name: 'Bus', type: 'Type'},
        {name: 'Van', type: 'Type'},
        {name: 'Motorcycle', type: 'Type'},
        {name: 'Electric', type: 'Fuel Type'},
        {name: 'Automatic', type: 'Transmission'},
        {name: 'Manual', type: 'Transmission'},
        {name: 'Luxury', type: 'Category'}
    ];
    
    // Show suggestions on focus
    searchInput.on('focus', function() {
        showSuggestions('');
    });
    
    // Handle input for suggestions
    searchInput.on('input', function() {
        const query = $(this).val().trim().toLowerCase();
        showSuggestions(query);
    });
    
    // Hide suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.search-box').length) {
            suggestionsBox.hide();
        }
    });
    
    // Function to show suggestions
    function showSuggestions(query) {
        if (query.length === 0) {
            showCommonSuggestions();
            return;
        }
        
        // Check for spelling corrections
        let correctedQuery = query;
        if (spellingCorrections[query]) {
            correctedQuery = spellingCorrections[query];
        }
        
        // Filter common searches
        const filtered = commonSearches.filter(item => 
            item.name.toLowerCase().includes(correctedQuery) || 
            item.type.toLowerCase().includes(correctedQuery)
        );
        
        // Add spelling correction if needed
        if (spellingCorrections[query] && spellingCorrections[query] !== query) {
            filtered.unshift({
                name: `Did you mean "${spellingCorrections[query]}"?`,
                type: 'Suggestion',
                isCorrection: true
            });
        }
        
        // Show suggestions
        if (filtered.length > 0) {
            suggestionsBox.empty();
            filtered.forEach(item => {
                const suggestionItem = $('<div>').addClass('suggestion-item');
                
                if (item.isCorrection) {
                    suggestionItem.html(`
                        <div>
                            <span class="suggestion-name">${item.name}</span>
                        </div>
                    `);
                    suggestionItem.on('click', function() {
                        searchInput.val(spellingCorrections[query]);
                        $('#searchForm').submit();
                    });
                } else {
                    suggestionItem.html(`
                        <div>
                            <span class="suggestion-name">${item.name}</span>
                        </div>
                        <span class="suggestion-type">${item.type}</span>
                    `);
                    suggestionItem.on('click', function() {
                        searchInput.val(item.name);
                        $('#searchForm').submit();
                    });
                }
                
                suggestionsBox.append(suggestionItem);
            });
            suggestionsBox.show();
        } else {
            suggestionsBox.hide();
        }
    }
    
    // Show common suggestions
    function showCommonSuggestions() {
        suggestionsBox.empty();
        commonSearches.forEach(item => {
            const suggestionItem = $('<div>').addClass('suggestion-item').html(`
                <div>
                    <span class="suggestion-name">${item.name}</span>
                </div>
                <span class="suggestion-type">${item.type}</span>
            `);
            
            suggestionItem.on('click', function() {
                searchInput.val(item.name);
                $('#searchForm').submit();
            });
            
            suggestionsBox.append(suggestionItem);
        });
        suggestionsBox.show();
    }
    
    // Filter tags click handler
    $('.filter-tag').on('click', function(e) {
        e.preventDefault();
        const filter = $(this).data('filter');
        const currentQuery = searchInput.val();
        window.location.href = `search.php?q=${encodeURIComponent(currentQuery)}&filter=${filter}`;
    });
    
    // Handle Enter key in search
    searchInput.on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            suggestionsBox.hide();
        }
    });
});
</script>

</body>
</html>