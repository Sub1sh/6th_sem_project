<?php
// update_image_paths.php - Update database with correct image paths
include_once("connection.php");

echo "<h1>🔄 Updating Database Image Paths</h1>";

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Image mapping for all 47 vehicles
$updates = [
    1 => 'images/vehicle-1.png',   // Toyota Camry
    2 => 'images/vehicle-2.png',   // Honda Civic
    3 => 'images/vehicle-3.png',   // Ford Mustang
    4 => 'images/vehicle-4.png',   // Tesla Model 3
    5 => 'images/vehicle-5.png',   // BMW X5
    6 => 'images/vehicle-1.png',   // Toyota Corolla Altis
    7 => 'images/vehicle-2.png',   // Honda Civic RS
    8 => 'images/vehicle-3.png',   // Ford Mustang GT
    9 => 'images/vehicle-4.png',   // Tesla Model 3
    10 => 'images/vehicle-5.png',  // BMW X5 xDrive
    11 => 'images/vehicle-6.png',  // Mercedes C-Class
    12 => 'images/car-1.png',      // Toyota Fortuner
    13 => 'images/car-2.png',      // Honda City
    14 => 'images/car-3.png',      // Mahindra Thar
    15 => 'images/car-5.png',      // Hyundai Creta
    16 => 'images/car-7.png',      // Kia Seltos
    17 => 'images/car-8.png',      // Tata Nexon
    18 => 'images/vehicles.jpg',   // Jeep Compass
    19 => 'images/tipper-truck.png', // Tata 407 Truck
    20 => 'images/tw.png',          // Ashok Leyland Dost (UPDATED)
    21 => 'images/Yellow-Truck.png', // Mahindra Bolero Pickup (UPDATED)
    22 => 'images/toyota-grand-cabin-12-1-passenger_truck.png', // Tata Signa
    23 => 'images/T-King-4-Ton-Light-Truck.jpg', // Eicher Pro
    24 => 'images/White Bus on Rural Road.png', // Volvo Bus
    25 => 'images/bus-2.png',       // Tata Starbus Ultra
    26 => 'images/bus-3.png',       // Ashok Leyland Viking
    27 => 'images/bus-4.png',       // Mahindra Cruzio
    28 => 'images/vehicles 1.jpg',  // Force Traveller
    29 => 'images/vehicles 2.jpg',  // Toyota HiAce
    30 => 'images/vehicles 3.png',  // Maruti Eeco
    31 => 'images/vehicles 4.png',  // Tata Winger
    32 => 'images/vehicles 5.png',  // Mahindra Supro
    33 => 'images/vehicles 6.png',  // Royal Enfield
    34 => 'images/bike-1.png',      // Hero Splendor
    35 => 'images/bike-2.png',      // Bajaj Pulsar
    36 => 'images/bike-3.png',      // TVS Apache
    37 => 'images/scooter-1.png',   // Honda Activa
    38 => 'images/luxury-1.png',    // Mercedes S-Class
    39 => 'images/luxury-2.png',    // BMW 7 Series
    40 => 'images/luxury-3.png',    // Audi Q7
    41 => 'images/luxury-4.png',    // Range Rover
    42 => 'images/electric-1.png',  // MG ZS EV
    43 => 'images/electric-2.png',  // Tata Nexon EV
    44 => 'images/electric-3.png',  // Hyundai Kona
    45 => 'images/mpv-1.png',       // Toyota Innova
    46 => 'images/suv-extra-1.png', // Mahindra XUV700
    47 => 'images/suv-extra-2.png'  // Ford Endeavour
];

echo "<table border='1' cellpadding='8' style='border-collapse:collapse; width:100%;'>";
echo "<tr style='background:#333; color:white;'><th>ID</th><th>Vehicle</th><th>Image Path</th><th>Status</th><th>File Exists?</th></tr>";

$success_count = 0;
$fail_count = 0;

foreach ($updates as $id => $path) {
    // Get vehicle details
    $vehicle_query = "SELECT brand, model FROM vehicles WHERE id = $id";
    $vehicle_result = mysqli_query($conn, $vehicle_query);
    $vehicle = mysqli_fetch_assoc($vehicle_result);
    
    $vehicle_name = $vehicle ? $vehicle['brand'] . ' ' . $vehicle['model'] : 'Unknown';
    
    // Update database
    $sql = "UPDATE vehicles SET image_url = '$path' WHERE id = $id";
    
    if (mysqli_query($conn, $sql)) {
        $status = "<span style='color:green'>✅ Updated</span>";
        $success_count++;
    } else {
        $status = "<span style='color:red'>❌ Failed: " . mysqli_error($conn) . "</span>";
        $fail_count++;
    }
    
    // Check if file exists
    $file_exists = file_exists($path) ? "<span style='color:green'>✅ Yes</span>" : "<span style='color:red'>❌ No</span>";
    
    // Highlight updated images
    $row_color = '';
    if ($id == 20 || $id == 21) {
        $row_color = ' style="background:#fff3e0;"'; // Orange highlight for updated trucks
    }
    
    echo "<tr$row_color>";
    echo "<td>$id</td>";
    echo "<td>$vehicle_name</td>";
    echo "<td>$path</td>";
    echo "<td>$status</td>";
    echo "<td>$file_exists</td>";
    echo "</tr>";
}

echo "</table>";

echo "<div style='margin-top:20px; padding:20px; background:#f5f5f5; border-radius:10px;'>";
echo "<h2>📊 Update Summary</h2>";
echo "<p style='font-size:1.2em;'>";
echo "✅ Successfully updated: <strong style='color:green'>$success_count</strong> vehicles<br>";
echo "❌ Failed: <strong style='color:red'>$fail_count</strong> vehicles<br>";
echo "</p>";

// Highlight the updated trucks
echo "<div style='margin-top:20px; padding:15px; background:#fff3e0; border-left:4px solid #FF9800;'>";
echo "<h3 style='color:#FF9800; margin:0;'>🔄 Updated Truck Images:</h3>";
echo "<p><strong>ID 20 (Ashok Leyland Dost):</strong> images/tw.png<br>";
echo "<strong>ID 21 (Mahindra Bolero Pickup):</strong> images/Yellow-Truck.png</p>";
echo "</div>";

echo "<h3>🔗 Next Steps:</h3>";
echo "<p>";
echo "1. <a href='check_images.php' style='padding:5px 15px; background:#4CAF50; color:white; text-decoration:none; border-radius:5px;'>Verify Images</a><br>";
echo "2. <a href='recommendation.php' style='padding:5px 15px; background:#2196F3; color:white; text-decoration:none; border-radius:5px;'>View Recommended Vehicles</a><br>";
echo "3. <a href='vehicles.php' style='padding:5px 15px; background:#FF9800; color:white; text-decoration:none; border-radius:5px;'>View All Vehicles</a><br>";
echo "</p>";
echo "</div>";

mysqli_close($conn);
?>