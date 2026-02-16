<?php
// test_category.php - View vehicles by category
include_once("connection.php");

$category = isset($_GET['type']) ? $_GET['type'] : 'all';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Vehicle Gallery - TravelX</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #f5f5f5; padding: 20px; }
        .header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; 
            padding: 30px; 
            border-radius: 15px; 
            margin-bottom: 30px; 
            text-align: center;
        }
        .category-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-bottom: 30px;
        }
        .category-tab {
            padding: 10px 20px;
            background: white;
            border-radius: 30px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .category-tab:hover, .category-tab.active {
            background: #8E2DE2;
            color: white;
        }
        .vehicle-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }
        .vehicle-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .vehicle-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(142, 45, 226, 0.2);
        }
        .vehicle-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .vehicle-info {
            padding: 20px;
        }
        .vehicle-name {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .vehicle-type {
            color: #8E2DE2;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        .vehicle-price {
            font-size: 1.3rem;
            color: #e74c3c;
            font-weight: bold;
        }
        @media (max-width: 768px) {
            .vehicle-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚗 TravelX Vehicle Gallery</h1>
        <p>Browse our collection of vehicles</p>
    </div>
    
    <div class="category-tabs">
        <a href="?type=all" class="category-tab <?php echo $category == 'all' ? 'active' : ''; ?>">All</a>
        <?php
        $types = mysqli_query($conn, "SELECT DISTINCT type FROM vehicles ORDER BY type");
        while ($t = mysqli_fetch_assoc($types)) {
            $active = ($category == $t['type']) ? 'active' : '';
            echo "<a href='?type=" . urlencode($t['type']) . "' class='category-tab $active'>" . $t['type'] . "</a>";
        }
        ?>
    </div>
    
    <div class="vehicle-grid">
        <?php
        if ($category == 'all') {
            $sql = "SELECT * FROM vehicles ORDER BY type, brand";
        } else {
            $escaped = mysqli_real_escape_string($conn, $category);
            $sql = "SELECT * FROM vehicles WHERE type = '$escaped' ORDER BY brand";
        }
        
        $result = mysqli_query($conn, $sql);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $image = $row['image_url'] ?? 'images/vehicle-1.png';
            if (!file_exists($image)) {
                $image = 'https://via.placeholder.com/400x300/8E2DE2/ffffff?text=' . urlencode($row['brand']);
            }
            ?>
            <div class="vehicle-card">
                <img src="<?php echo $image; ?>" alt="<?php echo $row['brand'] . ' ' . $row['model']; ?>">
                <div class="vehicle-info">
                    <div class="vehicle-name"><?php echo $row['brand'] . ' ' . $row['model']; ?></div>
                    <div class="vehicle-type"><?php echo $row['type']; ?></div>
                    <div class="vehicle-price">Rs. <?php echo number_format($row['daily_rate'], 2); ?>/day</div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</body>
</html>