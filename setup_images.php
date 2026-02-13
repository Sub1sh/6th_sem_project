<?php
// setup_images.php - Create images folder and add vehicle pictures

echo "<h1>🚗 Setting Up Vehicle Images</h1>";

// Create images folder if it doesn't exist
if (!is_dir('images')) {
    mkdir('images', 0777, true);
    echo "✅ Created 'images' folder<br>";
} else {
    echo "✅ 'images' folder already exists<br>";
}

// List of vehicle images we need
$vehicles = [
    // Sedans
    ['name' => 'Toyota Camry', 'file' => 'vehicle-1.png', 'color' => '#FF6B6B'],
    ['name' => 'Honda Civic', 'file' => 'vehicle-2.png', 'color' => '#4ECDC4'],
    ['name' => 'Ford Mustang', 'file' => 'vehicle-3.png', 'color' => '#45B7D1'],
    ['name' => 'Tesla Model 3', 'file' => 'vehicle-4.png', 'color' => '#96CEB4'],
    ['name' => 'BMW X5', 'file' => 'vehicle-5.png', 'color' => '#FFEAA7'],
    ['name' => 'Mercedes C-Class', 'file' => 'vehicle-6.png', 'color' => '#DDA0DD'],
    
    // SUVs
    ['name' => 'Toyota Fortuner', 'file' => 'car-1.png', 'color' => '#FF9999'],
    ['name' => 'Honda City', 'file' => 'car-2.png', 'color' => '#99FF99'],
    ['name' => 'Mahindra Thar', 'file' => 'car-3.png', 'color' => '#9999FF'],
    ['name' => 'Hyundai Creta', 'file' => 'car-5.png', 'color' => '#FFCC99'],
    ['name' => 'Kia Seltos', 'file' => 'car-7.png', 'color' => '#CC99FF'],
    ['name' => 'Tata Nexon', 'file' => 'car-8.png', 'color' => '#99CCFF'],
    
    // Trucks
    ['name' => 'Tata Truck', 'file' => 'tipper-truck.png', 'color' => '#A0522D'],
    ['name' => 'Mahindra Pickup', 'file' => 'Yellow-Truck.png', 'color' => '#FFD700'],
    
    // Buses
    ['name' => 'Volvo Bus', 'file' => 'White Bus on Rural Road.png', 'color' => '#F0F0F0'],
    ['name' => 'City Bus', 'file' => 'vehicle-4.png', 'color' => '#98FB98'],
    
    // Vans
    ['name' => 'Toyota Van', 'file' => 'vehicles 2.jpg', 'color' => '#FFB6C1'],
    ['name' => 'Maruti Van', 'file' => 'vehicles 3.png', 'color' => '#87CEEB'],
    ['name' => 'Tata Van', 'file' => 'vehicles 4.png', 'color' => '#DDA0DD'],
    ['name' => 'Mahindra Van', 'file' => 'vehicles 5.png', 'color' => '#F0E68C'],
    
    // Bikes
    ['name' => 'Royal Enfield', 'file' => 'vehicles 6.png', 'color' => '#B0E0E6'],
    ['name' => 'Hero Splendor', 'file' => 'vehicle-1.png', 'color' => '#FFB6C1'],
    ['name' => 'Bajaj Pulsar', 'file' => 'vehicle-2.png', 'color' => '#98FB98'],
    ['name' => 'TVS Apache', 'file' => 'vehicle-3.png', 'color' => '#87CEEB'],
    ['name' => 'Honda Activa', 'file' => 'vehicle-4.png', 'color' => '#DDA0DD']
];

echo "<h2>Creating Vehicle Images:</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;'>";

foreach ($vehicles as $v) {
    $filepath = 'images/' . $v['file'];
    
    if (!file_exists($filepath)) {
        // Create a simple image with vehicle name
        $im = imagecreatetruecolor(400, 300);
        
        // Parse color
        list($r, $g, $b) = sscanf($v['color'], "#%02x%02x%02x");
        $bg = imagecolorallocate($im, $r, $g, $b);
        $text_color = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);
        
        imagefill($im, 0, 0, $bg);
        
        // Add border
        imagerectangle($im, 0, 0, 399, 299, $black);
        
        // Add vehicle name
        $display_name = $v['name'];
        if (strlen($display_name) > 15) {
            $display_name = substr($display_name, 0, 15) . '...';
        }
        
        // Center text approximately
        $x = 50;
        $y = 150;
        
        imagestring($im, 5, $x, $y, $display_name, $text_color);
        imagestring($im, 3, $x, $y + 20, $v['file'], $text_color);
        
        // Add car icon
        imagestring($im, 5, $x + 150, $y, '🚗', $text_color);
        
        // Save based on file extension
        if (pathinfo($v['file'], PATHINFO_EXTENSION) == 'jpg') {
            imagejpeg($im, $filepath, 90);
        } else {
            imagepng($im, $filepath, 9);
        }
        imagedestroy($im);
        
        echo "<div style='border:1px solid #ccc; padding:10px; text-align:center; background:{$v['color']}20;'>";
        echo "<img src='$filepath' width='150' height='120' style='object-fit:cover; border-radius:5px;'><br>";
        echo "<strong>{$v['name']}</strong><br>";
        echo "<small style='color:green'>✅ Created</small>";
        echo "</div>";
    } else {
        echo "<div style='border:1px solid #ccc; padding:10px; text-align:center; background:#f0f0f0;'>";
        echo "<img src='$filepath' width='150' height='120' style='object-fit:cover; border-radius:5px;'><br>";
        echo "<strong>{$v['name']}</strong><br>";
        echo "<small style='color:blue'>⏭️ Already exists</small>";
        echo "</div>";
    }
}

echo "</div>";

echo "<h3>✅ Images folder setup complete!</h3>";
echo "<p><a href='check_images.php' style='padding:10px 20px; background:#4CAF50; color:white; text-decoration:none; border-radius:5px;'>Check Images</a> ";
echo "<a href='recommendation.php' style='padding:10px 20px; background:#2196F3; color:white; text-decoration:none; border-radius:5px;'>View Vehicles</a></p>";
?>