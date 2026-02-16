<?php
// fix_all_vehicle_images.php - Complete fix for ALL vehicle types
include_once("connection.php");

echo "<h1>🚗 COMPLETE VEHICLE IMAGE ORGANIZER</h1>";

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// ===== DEFINE PROPER IMAGES FOR EACH VEHICLE TYPE =====
$category_images = [
    'Sports Car' => [
        'lamborghini-huracan.jpg',
        'ferrari-f8.jpg',
        'porsche-911.jpg',
        'maserati-mc20.jpg',
        'vehicle-3.png'
    ],
    'Luxury Coupe' => [
        'aston-martin-db11.jpg',
        'luxury-1.png',
        'luxury-2.png'
    ],
    'Electric Sedan' => [
        'vehicle-4.png',
        'lucid-air.jpg',
        'electric-1.png'
    ],
    'Electric Truck' => [
        'rivian-r1t.jpg',
        'f150-lightning.jpg',
        'electric-2.png'
    ],
    'Electric SUV' => [
        'bmw-ix.jpg',
        'electric-3.png',
        'vehicle-5.png'
    ],
    'SUV' => [
        'car-1.png',
        'car-3.png',
        'car-5.png',
        'car-7.png',
        'car-8.png',
        'suv-extra-1.png',
        'suv-extra-2.png',
        'vehicle-5.png'
    ],
    'Sedan' => [
        'vehicle-1.png',
        'vehicle-2.png',
        'vehicle-6.png',
        'car-2.png'
    ],
    'Truck' => [
        'tipper-truck.png',
        'T-King-4-Ton-Light-Truck.jpg',
        'kenworth-t680.jpg',
        'peterbilt-579.jpg',
        'volvo-vnl.jpg'
    ],
    'Pickup Truck' => [
        'Yellow-Truck.png',
        'tw.png',
        'toyota-grand-cabin-12-1-passenger_truck.png'
    ],
    'Semi Truck' => [
        'kenworth-t680.jpg',
        'peterbilt-579.jpg',
        'volvo-vnl.jpg'
    ],
    'Bus' => [
        'White Bus on Rural Road.png',
        'bus-2.png',
        'bus-3.png',
        'bus-4.png',
        'bus-5.png'
    ],
    'Luxury Bus' => [
        'mercedes-tourismo.jpg',
        'White Bus on Rural Road.png'
    ],
    'Coach Bus' => [
        'scania-interlink.jpg',
        'bus-2.png'
    ],
    'Van' => [
        'vehicles 1.jpg',
        'vehicles 2.jpg',
        'vehicles 3.png',
        'vehicles 4.png',
        'vehicles 5.png'
    ],
    'Motorcycle' => [
        'vehicles 6.png',
        'bike-1.png',
        'bike-2.png',
        'bike-3.png'
    ],
    'Sport Bike' => [
        'ducati-panigale.jpg',
        'bmw-s1000rr.jpg',
        'bike-2.png'
    ],
    'Cruiser' => [
        'harley-street-glide.jpg',
        'vehicles 6.png'
    ],
    'Scooter' => [
        'scooter-1.png'
    ],
    'Luxury Sedan' => [
        'luxury-1.png',
        'luxury-2.png',
        'vehicle-6.png'
    ],
    'Luxury SUV' => [
        'rolls-royce-cullinan.jpg',
        'bentley-bentayga.jpg',
        'lamborghini-urus.jpg',
        'porsche-cayenne.jpg',
        'luxury-3.png',
        'luxury-4.png'
    ],
    'Performance SUV' => [
        'porsche-cayenne.jpg',
        'lamborghini-urus.jpg'
    ],
    'Classic Car' => [
        'mustang-1969.jpg',
        'camaro-1970.jpg',
        'beetle-1967.jpg'
    ],
    'MPV' => [
        'mpv-1.png'
    ]
];

// ===== SPECIFIC VEHICLE MAPPING (for exact matches) =====
$specific_mapping = [
    // Toyota Vehicles
    'Toyota Camry' => 'vehicle-1.png',
    'Toyota Corolla Altis' => 'vehicle-1.png',
    'Toyota Fortuner' => 'car-1.png',
    'Toyota HiAce' => 'vehicles 2.jpg',
    'Toyota Innova Crysta' => 'mpv-1.png',
    
    // Honda Vehicles
    'Honda Civic' => 'vehicle-2.png',
    'Honda Civic RS' => 'vehicle-2.png',
    'Honda City' => 'car-2.png',
    'Honda Activa 6G' => 'scooter-1.png',
    
    // Ford Vehicles
    'Ford Mustang' => 'vehicle-3.png',
    'Ford Mustang GT' => 'vehicle-3.png',
    'Ford Endeavour' => 'suv-extra-2.png',
    'Ford F-150 Lightning' => 'f150-lightning.jpg',
    'Ford Mustang 1969' => 'mustang-1969.jpg',
    
    // Tesla
    'Tesla Model 3' => 'vehicle-4.png',
    
    // BMW
    'BMW X5' => 'vehicle-5.png',
    'BMW X5 xDrive' => 'vehicle-5.png',
    'BMW 7 Series' => 'luxury-2.png',
    'BMW iX' => 'bmw-ix.jpg',
    'BMW S1000RR' => 'bmw-s1000rr.jpg',
    
    // Mercedes
    'Mercedes C-Class' => 'vehicle-6.png',
    'Mercedes S-Class' => 'luxury-1.png',
    'Mercedes-Benz Tourismo' => 'mercedes-tourismo.jpg',
    
    // Mahindra
    'Mahindra Thar' => 'car-3.png',
    'Mahindra Bolero Pickup' => 'Yellow-Truck.png',
    'Mahindra Cruzio' => 'bus-4.png',
    'Mahindra Supro' => 'vehicles 5.png',
    'Mahindra XUV700' => 'suv-extra-1.png',
    
    // Tata
    'Tata Nexon' => 'car-8.png',
    'Tata 407 Truck' => 'tipper-truck.png',
    'Tata Signa 4825.TK' => 'toyota-grand-cabin-12-1-passenger_truck.png',
    'Tata Starbus Ultra' => 'bus-2.png',
    'Tata Winger' => 'vehicles 4.png',
    'Tata Nexon EV' => 'electric-2.png',
    
    // Hyundai
    'Hyundai Creta' => 'car-5.png',
    'Hyundai Kona Electric' => 'electric-3.png',
    
    // Kia
    'Kia Seltos' => 'car-7.png',
    
    // Ashok Leyland
    'Ashok Leyland Dost' => 'tw.png',
    'Ashok Leyland Viking' => 'bus-3.png',
    
    // Eicher
    'Eicher Pro 2049' => 'T-King-4-Ton-Light-Truck.jpg',
    
    // Volvo
    'Volvo 9400 Multi-Axle' => 'White Bus on Rural Road.png',
    'Volvo VNL 860' => 'volvo-vnl.jpg',
    
    // Force
    'Force Traveller' => 'vehicles 1.jpg',
    
    // Maruti
    'Maruti Eeco' => 'vehicles 3.png',
    
    // Royal Enfield
    'Royal Enfield Classic 350' => 'vehicles 6.png',
    
    // Hero
    'Hero Splendor Plus' => 'bike-1.png',
    
    // Bajaj
    'Bajaj Pulsar 220F' => 'bike-2.png',
    
    // TVS
    'TVS Apache RTR 160' => 'bike-3.png',
    
    // Jeep
    'Jeep Compass' => 'vehicles.jpg',
    
    // Luxury Brands
    'Lamborghini Huracan' => 'lamborghini-huracan.jpg',
    'Lamborghini Urus' => 'lamborghini-urus.jpg',
    'Ferrari F8 Tributo' => 'ferrari-f8.jpg',
    'Porsche 911 Turbo S' => 'porsche-911.jpg',
    'Porsche Cayenne Turbo GT' => 'porsche-cayenne.jpg',
    'Aston Martin DB11' => 'aston-martin-db11.jpg',
    'Maserati MC20' => 'maserati-mc20.jpg',
    'Audi Q7' => 'luxury-3.png',
    'Land Rover Range Rover Vogue' => 'luxury-4.png',
    'Rolls-Royce Cullinan' => 'rolls-royce-cullinan.jpg',
    'Bentley Bentayga' => 'bentley-bentayga.jpg',
    
    // Trucks
    'Kenworth T680' => 'kenworth-t680.jpg',
    'Peterbilt 579' => 'peterbilt-579.jpg',
    
    // Electric
    'Rivian R1T' => 'rivian-r1t.jpg',
    'Lucid Air' => 'lucid-air.jpg',
    'MG ZS EV' => 'electric-1.png',
    
    // Classic
    'Chevrolet Camaro 1970' => 'camaro-1970.jpg',
    'Volkswagen Beetle 1967' => 'beetle-1967.jpg',
    
    // Bikes
    'Ducati Panigale V4' => 'ducati-panigale.jpg',
    'Harley-Davidson Street Glide' => 'harley-street-glide.jpg',
    
    // Buses
    'Scania Interlink' => 'scania-interlink.jpg'
];

echo "<h2>📝 Fixing ALL Vehicle Images</h2>";

// Get all vehicles
$sql = "SELECT id, brand, model, type FROM vehicles ORDER BY id";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

echo "<table border='1' cellpadding='8' style='border-collapse:collapse; width:100%;'>";
echo "<tr style='background:#333; color:white;'><th>ID</th><th>Vehicle</th><th>Type</th><th>Current Image</th><th>New Image</th><th>Status</th></tr>";

$update_count = 0;
$type_counters = [];

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
    $full_name = $row['brand'] . ' ' . $row['model'];
    $type = $row['type'];
    
    // Get current image
    $img_sql = "SELECT image_url FROM vehicles WHERE id = $id";
    $img_result = mysqli_query($conn, $img_sql);
    $current_image = '';
    if ($img_result && $img_row = mysqli_fetch_assoc($img_result)) {
        $current_image = basename($img_row['image_url']);
    }
    
    // Determine correct image
    $new_image = '';
    
    // First check specific mapping
    if (isset($specific_mapping[$full_name])) {
        $new_image = $specific_mapping[$full_name];
    }
    // Then check by brand + model combination
    elseif (isset($specific_mapping[$row['brand'] . ' ' . $row['model']])) {
        $new_image = $specific_mapping[$row['brand'] . ' ' . $row['model']];
    }
    else {
        // Use category-based mapping
        if (!isset($type_counters[$type])) {
            $type_counters[$type] = 0;
        }
        
        $type_images = $category_images[$type] ?? ['vehicle-1.png'];
        $image_index = $type_counters[$type] % count($type_images);
        $new_image = $type_images[$image_index];
        $type_counters[$type]++;
    }
    
    // Ensure image exists, if not use default
    $image_path = 'images/' . $new_image;
    if (!file_exists($image_path)) {
        // Try to find any image with similar name
        $found = false;
        if (is_dir('images')) {
            $files = scandir('images');
            foreach ($files as $file) {
                if (strpos(strtolower($file), strtolower($row['brand'])) !== false || 
                    strpos(strtolower($file), strtolower($row['model'])) !== false) {
                    $new_image = $file;
                    $found = true;
                    break;
                }
            }
        }
        if (!$found) {
            $new_image = 'vehicle-1.png';
        }
    }
    
    $full_path = 'images/' . $new_image;
    
    // Update database
    $update_sql = "UPDATE vehicles SET image_url = '$full_path' WHERE id = $id";
    
    if (mysqli_query($conn, $update_sql)) {
        $status = "<span style='color:green'>✅ Updated</span>";
        $update_count++;
    } else {
        $status = "<span style='color:red'>❌ Failed</span>";
    }
    
    // Color code based on change
    $row_color = ($current_image != $new_image) ? '#fff3e0' : '#e8f5e8';
    
    echo "<tr style='background:$row_color'>";
    echo "<td>$id</td>";
    echo "<td><strong>$full_name</strong></td>";
    echo "<td>$type</td>";
    echo "<td>" . ($current_image ?: 'None') . "</td>";
    echo "<td><strong>$new_image</strong></td>";
    echo "<td>$status</td>";
    echo "</tr>";
}

echo "</table>";

// Summary
echo "<div style='margin-top:30px; padding:20px; background:#f5f5f5; border-radius:10px;'>";
echo "<h2>✅ Fix Complete</h2>";
echo "<p>Updated <strong>$update_count</strong> vehicles with correct images</p>";

// Show category summaries
echo "<h3>📊 Images by Category:</h3>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;'>";

foreach ($category_images as $category => $images) {
    $count = isset($type_counters[$category]) ? $type_counters[$category] : 0;
    if ($count > 0) {
        echo "<div style='background:white; padding:15px; border-radius:10px; box-shadow:0 2px 5px rgba(0,0,0,0.1);'>";
        echo "<h4 style='margin:0 0 10px 0; color:#8E2DE2;'>$category</h4>";
        echo "<p><strong>$count vehicles</strong></p>";
        echo "<p style='font-size:0.9rem; color:#666;'>";
        $img_list = array_slice($images, 0, 3);
        echo implode(', ', $img_list);
        if (count($images) > 3) echo '...';
        echo "</p>";
        echo "</div>";
    }
}
echo "</div>";

// Action buttons
echo "<div style='margin-top:30px;'>";
echo "<a href='test_vehicles.php' style='padding:10px 20px; background:#4CAF50; color:white; text-decoration:none; border-radius:5px; margin-right:10px;'>View All Vehicles</a>";
echo "<a href='test_trucks.php' style='padding:10px 20px; background:#FF8E53; color:white; text-decoration:none; border-radius:5px; margin-right:10px;'>View Trucks</a>";
echo "<a href='test_buses.php' style='padding:10px 20px; background:#2196F3; color:white; text-decoration:none; border-radius:5px; margin-right:10px;'>View Buses</a>";
echo "<a href='dashboard.php' style='padding:10px 20px; background:#607D8B; color:white; text-decoration:none; border-radius:5px;'>Dashboard</a>";
echo "</div>";

echo "</div>";

mysqli_close($conn);
?>