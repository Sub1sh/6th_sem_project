<?php
// update_images_sql.php - Update database with new image names
include_once("connection.php");

echo "<h1>🔄 Updating Database with New Image Names</h1>";

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Map vehicle IDs to new image names
$vehicle_image_map = [
    // Toyota Vehicles
    1 => 'toyota-camry.png',        // Toyota Camry
    6 => 'toyota-camry.png',        // Toyota Corolla Altis
    12 => 'toyota-fortuner.png',    // Toyota Fortuner
    29 => 'toyota-hiace.jpg',       // Toyota HiAce
    45 => 'toyota-innova.png',      // Toyota Innova Crysta
    
    // Honda Vehicles
    2 => 'honda-city.png',          // Honda Civic (using city image)
    7 => 'honda-city.png',          // Honda Civic RS
    13 => 'honda-city.png',         // Honda City
    37 => 'honda-activa.png',       // Honda Activa
    
    // Ford Vehicles
    3 => 'ford-mustang.png',        // Ford Mustang
    8 => 'ford-mustang.png',        // Ford Mustang GT
    47 => 'ford-endeavour.png',     // Ford Endeavour
    55 => 'ford-f150-lightning.jpg', // Will need to download
    
    // BMW
    5 => 'bmw-x5.png',              // BMW X5
    10 => 'bmw-x5.png',             // BMW X5 xDrive
    39 => 'bmw-7series.png',        // BMW 7 Series
    56 => 'bmw-ix.jpg',             // Will need to download
    69 => 'bmw-s1000rr.jpg',        // Will need to download
    
    // Mercedes
    11 => 'mercedes-cclass.png',    // Mercedes C-Class
    38 => 'mercedes-sclass.png',    // Mercedes S-Class
    70 => 'mercedes-tourismo.jpg',  // Will need to download
    
    // Mahindra
    14 => 'mahindra-thar.png',      // Mahindra Thar
    21 => 'mahindra-bolero-pickup.png', // Mahindra Bolero
    27 => 'mahindra-cruzio.png',    // Mahindra Cruzio
    32 => 'mahindra-supro.png',     // Mahindra Supro
    46 => 'mahindra-xuv700.png',    // Mahindra XUV700
    
    // Tata
    17 => 'tata-nexon.png',         // Tata Nexon
    19 => 'tata-407-truck.png',     // Tata 407 Truck
    22 => 'tata-truck.png',         // Tata Signa
    25 => 'tata-starbus.png',       // Tata Starbus
    31 => 'tata-winger.png',        // Tata Winger
    43 => 'tata-nexon-ev.png',      // Tata Nexon EV
    
    // Hyundai
    15 => 'hyundai-creta.png',      // Hyundai Creta
    44 => 'hyundai-kona-ev.png',    // Hyundai Kona Electric
    
    // Kia
    16 => 'kia-seltos.png',         // Kia Seltos
    
    // Ashok Leyland
    20 => 'ashok-leyland-dost.png', // Ashok Leyland Dost
    26 => 'ashok-leyland-viking.png', // Ashok Leyland Viking
    
    // Eicher
    23 => 'eicher-pro-2049.jpg',    // Eicher Pro 2049
    
    // Volvo
    24 => 'volvo-bus.png',          // Volvo Bus
    59 => 'volvo-vnl.jpg',          // Will need to download
    
    // Force
    28 => 'force-traveller.jpg',    // Force Traveller
    
    // Maruti
    30 => 'maruti-eeco.png',        // Maruti Eeco
    
    // Royal Enfield
    33 => 'royal-enfield.png',      // Royal Enfield
    
    // Hero
    34 => 'hero-splendor.png',      // Hero Splendor
    
    // Bajaj
    35 => 'bajaj-pulsar.png',       // Bajaj Pulsar
    
    // TVS
    36 => 'tvs-apache.png',         // TVS Apache
    
    // Jeep
    18 => 'jeep-compass.jpg',       // Jeep Compass
    
    // MG
    42 => 'mg-zsev.png',            // MG ZS EV
    
    // Audi
    40 => 'audi-q7.png',            // Audi Q7
    
    // Land Rover
    41 => 'range-rover.png',        // Range Rover
    
    // Lamborghini
    48 => 'lamborghini-huracan.jpg', // Will need to download
    62 => 'lamborghini-urus.jpg',    // Will need to download
    
    // Ferrari
    49 => 'ferrari-f8.jpg',         // Will need to download
    
    // Porsche
    50 => 'porsche-911.jpg',        // Will need to download
    63 => 'porsche-cayenne.jpg',    // Will need to download
    
    // Aston Martin
    51 => 'aston-martin-db11.jpg',  // Will need to download
    
    // Maserati
    52 => 'maserati-mc20.jpg',      // Will need to download
    
    // Rivian
    53 => 'rivian-r1t.jpg',         // Will need to download
    
    // Lucid
    54 => 'lucid-air.jpg',          // Will need to download
    
    // Kenworth
    57 => 'kenworth-t680.jpg',      // Will need to download
    
    // Peterbilt
    58 => 'peterbilt-579.jpg',      // Will need to download
    
    // Rolls-Royce
    60 => 'rolls-royce-cullinan.jpg', // Will need to download
    
    // Bentley
    61 => 'bentley-bentayga.jpg',   // Will need to download
    
    // Classic Cars
    64 => 'mustang-1969.jpg',       // Will need to download
    65 => 'camaro-1970.jpg',        // Will need to download
    66 => 'beetle-1967.jpg',        // Will need to download
    
    // Ducati
    67 => 'ducati-panigale.jpg',    // Will need to download
    
    // Harley
    68 => 'harley-street-glide.jpg', // Will need to download
    
    // Scania
    71 => 'scania-interlink.jpg'    // Will need to download
];

echo "<table border='1' cellpadding='8' style='border-collapse:collapse; width:100%;'>";
echo "<tr style='background:#333; color:white;'><th>ID</th><th>Vehicle</th><th>New Image</th><th>Status</th><th>File Exists?</th></tr>";

$update_count = 0;
$missing_count = 0;

foreach ($vehicle_image_map as $id => $image_name) {
    // Get vehicle details
    $vehicle_sql = "SELECT brand, model FROM vehicles WHERE id = $id";
    $vehicle_result = mysqli_query($conn, $vehicle_sql);
    
    if (!$vehicle_result || mysqli_num_rows($vehicle_result) == 0) {
        continue;
    }
    
    $vehicle = mysqli_fetch_assoc($vehicle_result);
    $full_name = $vehicle['brand'] . ' ' . $vehicle['model'];
    
    $image_path = 'images/' . $image_name;
    $exists = file_exists($image_path) ? '✅' : '❌';
    
    if (!file_exists($image_path)) {
        $missing_count++;
    }
    
    $update_sql = "UPDATE vehicles SET image_url = '$image_path' WHERE id = $id";
    
    if (mysqli_query($conn, $update_sql)) {
        $status = "<span style='color:green;'>✅ Updated</span>";
        $update_count++;
    } else {
        $status = "<span style='color:red;'>❌ Failed</span>";
    }
    
    $row_color = $exists == '✅' ? '#d4edda' : '#fff3cd';
    echo "<tr style='background:$row_color'>";
    echo "<td>$id</td>";
    echo "<td>$full_name</td>";
    echo "<td>$image_name</td>";
    echo "<td>$status</td>";
    echo "<td>$exists</td>";
    echo "</tr>";
}

echo "</table>";

echo "<div style='margin-top:20px; padding:20px; background:#f5f5f5; border-radius:10px;'>";
echo "<h2>Summary</h2>";
echo "<p>✅ Updated: <strong>$update_count</strong> vehicles</p>";
echo "<p>❌ Missing images: <strong>$missing_count</strong> (need to download)</p>";

// List missing images
if ($missing_count > 0) {
    echo "<h3>Missing Images to Download:</h3>";
    echo "<ul>";
    foreach ($vehicle_image_map as $id => $image_name) {
        $image_path = 'images/' . $image_name;
        if (!file_exists($image_path)) {
            echo "<li>$image_name (for vehicle ID $id)</li>";
        }
    }
    echo "</ul>";
    echo "<p><a href='download_missing.php' style='padding:10px 20px; background:#FF9800; color:white; text-decoration:none; border-radius:5px;'>Download Missing Images</a></p>";
}

echo "</div>";

mysqli_close($conn);
?>