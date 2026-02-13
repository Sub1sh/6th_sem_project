<?php
// download_images.php - Download free vehicle images for all 47 vehicles
echo "<h1>📥 Downloading Vehicle Images for 47 Vehicles</h1>";

// Create images folder if it doesn't exist
if (!is_dir('images')) {
    mkdir('images', 0777, true);
    echo "✅ Created 'images' folder<br>";
}

// Free stock images from Unsplash (different vehicles for each type)
$image_urls = [
    // Sedans (IDs 1-11)
    'vehicle-1.png' => 'https://images.unsplash.com/photo-1553440569-bcc63803a83d?w=600&h=400&fit=crop', // Toyota Camry/Sedan
    'vehicle-2.png' => 'https://images.unsplash.com/photo-1563720223486-3296af5b6e8a?w=600&h=400&fit=crop', // Honda Civic
    'vehicle-3.png' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=600&h=400&fit=crop', // Ford Mustang/Sports
    'vehicle-4.png' => 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=600&h=400&fit=crop', // Tesla/Electric
    'vehicle-5.png' => 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=600&h=400&fit=crop', // BMW/Luxury SUV
    'vehicle-6.png' => 'https://images.unsplash.com/photo-1558981285-6f0c94958bb6?w=600&h=400&fit=crop', // Mercedes/Luxury Sedan
    
    // SUVs (IDs 12-18)
    'car-1.png' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&h=400&fit=crop', // Toyota Fortuner
    'car-2.png' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=600&h=400&fit=crop', // Honda City
    'car-3.png' => 'https://images.unsplash.com/photo-1532974297617-c0f05fe48bff?w=600&h=400&fit=crop', // Mahindra Thar
    'car-5.png' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=600&h=400&fit=crop', // Hyundai Creta
    'car-7.png' => 'https://images.unsplash.com/photo-1556189250-72ba954cfc2b?w=600&h=400&fit=crop', // Kia Seltos
    'car-8.png' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=600&h=400&fit=crop', // Tata Nexon
    
    // Trucks (IDs 19-23) - NEW IMAGES FOR TW.PNG AND YELLOW-TRUCK.PNG
    'tipper-truck.png' => 'https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?w=600&h=400&fit=crop', // Tata Truck
    'tw.png' => 'https://images.unsplash.com/photo-1517153295259-74eb0b416cee?w=600&h=400&fit=crop', // NEW: Ashok Leyland Truck
    'Yellow-Truck.png' => 'https://images.unsplash.com/photo-1533073526757-2c8ca1df9f1c?w=600&h=400&fit=crop', // NEW: Yellow Truck
    'toyota-grand-cabin-12-1-passenger_truck.png' => 'https://images.unsplash.com/photo-1566936737687-8f392a237b5c?w=600&h=400&fit=crop', // Toyota Truck
    'T-King-4-Ton-Light-Truck.jpg' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=600&h=400&fit=crop', // Eicher Truck
    
    // Buses (IDs 24-28)
    'White Bus on Rural Road.png' => 'https://images.unsplash.com/photo-1570125909232-eb263c188f7e?w=600&h=400&fit=crop', // Volvo Bus
    'bus-2.png' => 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=600&h=400&fit=crop', // City Bus
    'bus-3.png' => 'https://images.unsplash.com/photo-1570125909232-eb263c188f7e?w=600&h=400&fit=crop', // School Bus
    'bus-4.png' => 'https://images.unsplash.com/photo-1469285994282-454ceb49e63c?w=600&h=400&fit=crop', // Tourist Bus
    'bus-5.png' => 'https://images.unsplash.com/photo-1494905998402-395d579af36f?w=600&h=400&fit=crop', // Mini Bus
    
    // Vans (IDs 29-32)
    'vehicles 1.jpg' => 'https://images.unsplash.com/photo-1469285994282-454ceb49e63c?w=600&h=400&fit=crop', // Force Traveller
    'vehicles 2.jpg' => 'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?w=600&h=400&fit=crop', // Toyota HiAce
    'vehicles 3.png' => 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=600&h=400&fit=crop', // Maruti Eeco
    'vehicles 4.png' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=600&h=400&fit=crop', // Tata Winger
    'vehicles 5.png' => 'https://images.unsplash.com/photo-1556189250-72ba954cfc2b?w=600&h=400&fit=crop', // Mahindra Supro
    
    // Motorcycles (IDs 33-37)
    'vehicles 6.png' => 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=600&h=400&fit=crop', // Royal Enfield
    'bike-1.png' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?w=600&h=400&fit=crop', // Hero Splendor
    'bike-2.png' => 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?w=600&h=400&fit=crop', // Bajaj Pulsar
    'bike-3.png' => 'https://images.unsplash.com/photo-1591637333184-19aa84b3e01f?w=600&h=400&fit=crop', // TVS Apache
    'scooter-1.png' => 'https://images.unsplash.com/photo-1583267746897-2cf415887172?w=600&h=400&fit=crop', // Honda Activa
    
    // Luxury Vehicles (IDs 38-41)
    'luxury-1.png' => 'https://images.unsplash.com/photo-1563720223486-3296af5b6e8a?w=600&h=400&fit=crop', // Mercedes S-Class
    'luxury-2.png' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=600&h=400&fit=crop', // BMW 7 Series
    'luxury-3.png' => 'https://images.unsplash.com/photo-1603584173870-7f23fdae1b7a?w=600&h=400&fit=crop', // Audi Q7
    'luxury-4.png' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&h=400&fit=crop', // Range Rover
    
    // Electric Vehicles (IDs 42-44)
    'electric-1.png' => 'https://images.unsplash.com/photo-1560958089-b8a1929cea89?w=600&h=400&fit=crop', // MG ZS EV
    'electric-2.png' => 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=600&h=400&fit=crop', // Tata Nexon EV
    'electric-3.png' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=600&h=400&fit=crop', // Hyundai Kona
    
    // MPV & Others (IDs 45-47)
    'mpv-1.png' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=600&h=400&fit=crop', // Toyota Innova
    'suv-extra-1.png' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&h=400&fit=crop', // Mahindra XUV700
    'suv-extra-2.png' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=600&h=400&fit=crop', // Ford Endeavour
    
    // Additional images for fallback
    'vehicles.jpg' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&h=400&fit=crop',
    'default-car.jpg' => 'https://images.unsplash.com/photo-1553440569-bcc63803a83d?w=600&h=400&fit=crop'
];

echo "<div style='display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;'>";
$success_count = 0;
$fail_count = 0;

foreach ($image_urls as $filename => $url) {
    $filepath = 'images/' . $filename;
    
    // Special handling for tw.png and Yellow-Truck.png - always download new version
    if ($filename == 'tw.png' || $filename == 'Yellow-Truck.png') {
        if (file_exists($filepath)) {
            echo "<div style='border:2px solid #FF9800; padding:10px; text-align:center; background:#fff3e0;'>";
            echo "<strong style='color:orange'>🔄 Replacing:</strong><br>";
            echo "<small>$filename</small><br>";
            // Delete old file
            unlink($filepath);
            echo "</div>";
        }
    }
    
    if (!file_exists($filepath)) {
        // Download image with timeout
        $context = stream_context_create(['http' => ['timeout' => 30]]);
        $image_data = @file_get_contents($url, false, $context);
        
        if ($image_data) {
            file_put_contents($filepath, $image_data);
            echo "<div style='border:2px solid #4CAF50; padding:10px; text-align:center; background:#f0fff0;'>";
            echo "<img src='$filepath' width='150' height='120' style='object-fit:cover; border-radius:5px;'><br>";
            echo "<strong style='color:green'>✅ Downloaded:</strong><br>";
            echo "<small>$filename</small>";
            echo "</div>";
            $success_count++;
        } else {
            echo "<div style='border:2px solid #f44336; padding:10px; text-align:center; background:#fff0f0;'>";
            echo "<strong style='color:red'>❌ Failed:</strong><br>";
            echo "<small>$filename</small><br>";
            echo "<small>Connection error</small>";
            echo "</div>";
            $fail_count++;
        }
    } else {
        echo "<div style='border:2px solid #2196F3; padding:10px; text-align:center; background:#f0f8ff;'>";
        echo "<img src='$filepath' width='150' height='120' style='object-fit:cover; border-radius:5px;'><br>";
        echo "<strong style='color:blue'>⏭️ Already exists:</strong><br>";
        echo "<small>$filename</small>";
        echo "</div>";
    }
    
    // Flush output to show progress
    ob_flush();
    flush();
}

echo "</div>";

// Summary
echo "<div style='margin-top:30px; padding:20px; background:#f5f5f5; border-radius:10px;'>";
echo "<h2>📊 Download Summary</h2>";
echo "<p style='font-size:1.2em;'>";
echo "✅ Successfully downloaded: <strong style='color:green'>$success_count</strong> images<br>";
echo "❌ Failed: <strong style='color:red'>$fail_count</strong> images<br>";
echo "📁 Total images in folder: <strong>" . (count(scandir('images')) - 2) . "</strong><br>";
echo "</p>";

// Show which vehicles got which images
echo "<h3>🖼️ Image Mapping Guide:</h3>";
echo "<table border='1' cellpadding='8' style='border-collapse:collapse; width:100%;'>";
echo "<tr style='background:#333; color:white;'><th>Vehicle ID</th><th>Brand</th><th>Model</th><th>Image File</th><th>Status</th></tr>";

$vehicles = [
    1 => ['Toyota', 'Camry', 'vehicle-1.png'],
    2 => ['Honda', 'Civic', 'vehicle-2.png'],
    3 => ['Ford', 'Mustang', 'vehicle-3.png'],
    4 => ['Tesla', 'Model 3', 'vehicle-4.png'],
    5 => ['BMW', 'X5', 'vehicle-5.png'],
    6 => ['Toyota', 'Corolla Altis', 'vehicle-1.png'],
    7 => ['Honda', 'Civic RS', 'vehicle-2.png'],
    8 => ['Ford', 'Mustang GT', 'vehicle-3.png'],
    9 => ['Tesla', 'Model 3', 'vehicle-4.png'],
    10 => ['BMW', 'X5 xDrive', 'vehicle-5.png'],
    11 => ['Mercedes', 'C-Class', 'vehicle-6.png'],
    12 => ['Toyota', 'Fortuner', 'car-1.png'],
    13 => ['Honda', 'City', 'car-2.png'],
    14 => ['Mahindra', 'Thar', 'car-3.png'],
    15 => ['Hyundai', 'Creta', 'car-5.png'],
    16 => ['Kia', 'Seltos', 'car-7.png'],
    17 => ['Tata', 'Nexon', 'car-8.png'],
    18 => ['Jeep', 'Compass', 'vehicles.jpg'],
    19 => ['Tata', '407 Truck', 'tipper-truck.png'],
    20 => ['Ashok Leyland', 'Dost', 'tw.png'], // NEW IMAGE
    21 => ['Mahindra', 'Bolero Pickup', 'Yellow-Truck.png'], // NEW IMAGE
    22 => ['Tata', 'Signa 4825.TK', 'toyota-grand-cabin-12-1-passenger_truck.png'],
    23 => ['Eicher', 'Pro 2049', 'T-King-4-Ton-Light-Truck.jpg'],
    24 => ['Volvo', '9400 Multi-Axle', 'White Bus on Rural Road.png'],
    25 => ['Tata', 'Starbus Ultra', 'bus-2.png'],
    26 => ['Ashok Leyland', 'Viking', 'bus-3.png'],
    27 => ['Mahindra', 'Cruzio', 'bus-4.png'],
    28 => ['Force', 'Traveller', 'vehicles 1.jpg'],
    29 => ['Toyota', 'HiAce', 'vehicles 2.jpg'],
    30 => ['Maruti', 'Eeco', 'vehicles 3.png'],
    31 => ['Tata', 'Winger', 'vehicles 4.png'],
    32 => ['Mahindra', 'Supro', 'vehicles 5.png'],
    33 => ['Royal Enfield', 'Classic 350', 'vehicles 6.png'],
    34 => ['Hero', 'Splendor Plus', 'bike-1.png'],
    35 => ['Bajaj', 'Pulsar 220F', 'bike-2.png'],
    36 => ['TVS', 'Apache RTR 160', 'bike-3.png'],
    37 => ['Honda', 'Activa 6G', 'scooter-1.png'],
    38 => ['Mercedes', 'S-Class', 'luxury-1.png'],
    39 => ['BMW', '7 Series', 'luxury-2.png'],
    40 => ['Audi', 'Q7', 'luxury-3.png'],
    41 => ['Land Rover', 'Range Rover Vogue', 'luxury-4.png'],
    42 => ['MG', 'ZS EV', 'electric-1.png'],
    43 => ['Tata', 'Nexon EV', 'electric-2.png'],
    44 => ['Hyundai', 'Kona Electric', 'electric-3.png'],
    45 => ['Toyota', 'Innova Crysta', 'mpv-1.png'],
    46 => ['Mahindra', 'XUV700', 'suv-extra-1.png'],
    47 => ['Ford', 'Endeavour', 'suv-extra-2.png']
];

foreach ($vehicles as $id => $data) {
    $image_file = $data[2];
    $image_path = 'images/' . $image_file;
    $exists = file_exists($image_path);
    $status = $exists ? '✅ Yes' : '❌ No';
    $color = $exists ? '#e8f5e8' : '#ffebee';
    
    // Highlight the updated images
    if ($image_file == 'tw.png' || $image_file == 'Yellow-Truck.png') {
        $color = '#fff3e0'; // Orange highlight for updated images
    }
    
    echo "<tr style='background:$color'>";
    echo "<td>$id</td>";
    echo "<td>{$data[0]}</td>";
    echo "<td>{$data[1]}</td>";
    echo "<td>$image_file</td>";
    echo "<td>$status</td>";
    echo "</tr>";
}
echo "</table>";

// Highlight the updated vehicles
echo "<div style='margin-top:20px; padding:15px; background:#fff3e0; border-left:4px solid #FF9800;'>";
echo "<h3 style='color:#FF9800; margin:0;'>🔄 Updated Images:</h3>";
echo "<p><strong>tw.png</strong> - New truck image for Ashok Leyland Dost (ID 20)<br>";
echo "<strong>Yellow-Truck.png</strong> - New yellow truck image for Mahindra Bolero Pickup (ID 21)</p>";
echo "</div>";

echo "<h3>🔗 Next Steps:</h3>";
echo "<p>";
echo "1. <a href='check_images.php' style='padding:5px 15px; background:#4CAF50; color:white; text-decoration:none; border-radius:5px;'>Check All Images</a><br>";
echo "2. <a href='update_image_paths.php' style='padding:5px 15px; background:#FF9800; color:white; text-decoration:none; border-radius:5px;'>Update Database Paths</a><br>";
echo "3. <a href='recommendation.php' style='padding:5px 15px; background:#2196F3; color:white; text-decoration:none; border-radius:5px;'>View Vehicles</a><br>";
echo "</p>";
echo "</div>";
?>