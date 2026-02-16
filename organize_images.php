<?php
// organize_images.php - Organize images by vehicle type
include_once("connection.php");

echo "<h1>🖼️ ORGANIZING IMAGES BY VEHICLE TYPE</h1>";

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get all vehicles with their correct types
$sql = "SELECT id, brand, model, type FROM vehicles ORDER BY id";
$result = mysqli_query($conn, $sql);

echo "<table border='1' cellpadding='8' style='border-collapse:collapse; width:100%;'>";
echo "<tr style='background:#333; color:white;'><th>ID</th><th>Vehicle</th><th>Type</th><th>Assigned Image</th><th>Status</th></tr>";

// Map vehicle types to appropriate images
$type_image_map = [
    'SUV' => ['car-1.png', 'car-3.png', 'car-5.png', 'car-7.png', 'car-8.png', 'suv-extra-1.png', 'suv-extra-2.png', 'vehicles.jpg'],
    'Sedan' => ['vehicle-1.png', 'vehicle-2.png', 'vehicle-6.png', 'car-2.png'],
    'Sports Car' => ['vehicle-3.png', 'lamborghini-huracan.jpg', 'ferrari-f8.jpg', 'porsche-911.jpg', 'maserati-mc20.jpg'],
    'Truck' => ['tipper-truck.png', 'T-King-4-Ton-Light-Truck.jpg', 'kenworth-t680.jpg', 'peterbilt-579.jpg', 'volvo-vnl.jpg'],
    'Electric Truck' => ['rivian-r1t.jpg', 'f150-lightning.jpg', 'electric-2.png'],
    'Semi Truck' => ['kenworth-t680.jpg', 'peterbilt-579.jpg', 'volvo-vnl.jpg'],
    'Bus' => ['White Bus on Rural Road.png', 'bus-2.png', 'bus-3.png', 'bus-4.png', 'bus-5.png'],
    'Luxury Bus' => ['mercedes-tourismo.jpg', 'White Bus on Rural Road.png'],
    'Coach Bus' => ['scania-interlink.jpg', 'bus-2.png'],
    'Van' => ['vehicles 1.jpg', 'vehicles 2.jpg', 'vehicles 3.png', 'vehicles 4.png', 'vehicles 5.png'],
    'Motorcycle' => ['vehicles 6.png', 'bike-1.png', 'bike-2.png', 'bike-3.png'],
    'Sport Bike' => ['ducati-panigale.jpg', 'bmw-s1000rr.jpg', 'bike-2.png'],
    'Cruiser' => ['harley-street-glide.jpg', 'vehicles 6.png'],
    'Scooter' => ['scooter-1.png'],
    'Luxury Coupe' => ['aston-martin-db11.jpg', 'luxury-1.png', 'luxury-2.png'],
    'Classic Car' => ['mustang-1969.jpg', 'camaro-1970.jpg', 'beetle-1967.jpg'],
    'MPV' => ['mpv-1.png']
];

$type_counters = [];
$update_count = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
    $type = $row['type'];
    $full_name = $row['brand'] . ' ' . $row['model'];
    
    // Initialize counter for this type
    if (!isset($type_counters[$type])) {
        $type_counters[$type] = 0;
    }
    
    // Get images for this type
    $images = isset($type_image_map[$type]) ? $type_image_map[$type] : ['vehicle-1.png'];
    
    // Select image cyclically
    $image_index = $type_counters[$type] % count($images);
    $selected_image = $images[$image_index];
    $type_counters[$type]++;
    
    $image_path = 'images/' . $selected_image;
    
    // Check if image exists
    if (!file_exists($image_path)) {
        // Try to find any image with similar name
        $found = false;
        $all_files = scandir('images');
        foreach ($all_files as $file) {
            if ($file != '.' && $file != '..') {
                if (strpos(strtolower($file), strtolower($row['brand'])) !== false || 
                    strpos(strtolower($file), strtolower($row['model'])) !== false) {
                    $selected_image = $file;
                    $image_path = 'images/' . $file;
                    $found = true;
                    break;
                }
            }
        }
        
        if (!$found) {
            // Use default
            $selected_image = 'vehicle-1.png';
            $image_path = 'images/vehicle-1.png';
        }
    }
    
    // Update database
    $update_sql = "UPDATE vehicles SET image_url = '$image_path' WHERE id = $id";
    
    if (mysqli_query($conn, $update_sql)) {
        $status = "<span style='color:green;'>✅ Updated</span>";
        $update_count++;
    } else {
        $status = "<span style='color:red;'>❌ Failed</span>";
    }
    
    $image_exists = file_exists($image_path) ? '✅' : '❌';
    
    echo "<tr>";
    echo "<td>$id</td>";
    echo "<td>$full_name</td>";
    echo "<td>$type</td>";
    echo "<td>$selected_image $image_exists</td>";
    echo "<td>$status</td>";
    echo "</tr>";
}

echo "</table>";

echo "<div style='margin-top:20px; padding:20px; background:#f5f5f5; border-radius:10px;'>";
echo "<h2>✅ Summary</h2>";
echo "<p>Updated <strong>$update_count</strong> vehicles with appropriate images</p>";

echo "<h3>📊 Images by Type:</h3>";
foreach ($type_counters as $type => $count) {
    echo "<p><strong>$type</strong>: $count vehicles</p>";
}

echo "<p><a href='test_vehicles.php' style='padding:10px 20px; background:#4CAF50; color:white; text-decoration:none; border-radius:5px;'>View All Vehicles</a> ";
echo "<a href='dashboard.php' style='padding:10px 20px; background:#2196F3; color:white; text-decoration:none; border-radius:5px;'>Dashboard</a></p>";
echo "</div>";

mysqli_close($conn);
?>