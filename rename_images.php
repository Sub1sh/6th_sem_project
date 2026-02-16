<?php
// rename_images.php - Rename images to proper categories
echo "<h1>🔄 Renaming Images by Category</h1>";

$image_folder = 'images/';

if (!is_dir($image_folder)) {
    die("Images folder not found!");
}

// Define new names based on content
$rename_map = [
    // Trucks
    'T-King-4-Ton-Light-Truck.jpg' => 'eicher-pro-2049.jpg',
    'Yellow-Truck.png' => 'mahindra-bolero-pickup.png',
    'tipper-truck.png' => 'tata-407-truck.png',
    'toyota-grand-cabin-12-1-passenger_truck.png' => 'toyota-truck.png',
    
    // Buses
    'White Bus on Rural Road.png' => 'volvo-bus.png',
    'bus-2.png' => 'tata-starbus.png',
    'bus-3.png' => 'ashok-leyland-viking.png',
    'bus-4.png' => 'mahindra-cruzio.png',
    'bus-5.png' => 'force-traveller.png',
    
    // Vans
    'vehicles 1.jpg' => 'force-traveller-van.jpg',
    'vehicles 2.jpg' => 'toyota-hiace.jpg',
    'vehicles 3.png' => 'maruti-eeco.png',
    'vehicles 4.png' => 'tata-winger.png',
    'vehicles 5.png' => 'mahindra-supro.png',
    
    // Motorcycles
    'vehicles 6.png' => 'royal-enfield.png',
    'bike-1.png' => 'hero-splendor.png',
    'bike-2.png' => 'bajaj-pulsar.png',
    'bike-3.png' => 'tvs-apache.png',
    'scooter-1.png' => 'honda-activa.png',
    
    // SUVs
    'car-1.png' => 'toyota-fortuner.png',
    'car-2.png' => 'honda-city.png',
    'car-3.png' => 'mahindra-thar.png',
    'car-5.png' => 'hyundai-creta.png',
    'car-7.png' => 'kia-seltos.png',
    'car-8.png' => 'tata-nexon.png',
    'suv-extra-1.png' => 'mahindra-xuv700.png',
    'suv-extra-2.png' => 'ford-endeavour.png',
    'vehicles.jpg' => 'jeep-compass.jpg',
    
    // Sedans
    'vehicle-1.png' => 'toyota-camry.png',
    'vehicle-3.png' => 'ford-mustang.png',
    'vehicle-4.png' => 'tesla-model3.png',
    'vehicle-5.png' => 'bmw-x5.png',
    'vehicle-6.png' => 'mercedes-cclass.png',
    
    // Electric
    'electric-1.png' => 'mg-zsev.png',
    'electric-2.png' => 'tata-nexon-ev.png',
    'electric-3.png' => 'hyundai-kona-ev.png',
    
    // Luxury
    'luxury-2.png' => 'bmw-7series.png',
    'luxury-3.png' => 'audi-q7.png',
    'luxury-4.png' => 'range-rover.png',
    
    // MPV
    'mpv-1.png' => 'toyota-innova.png',
    
    // Default
    'default-car.jpg' => 'default-car.jpg'
];

echo "<table border='1' cellpadding='8' style='border-collapse:collapse; width:100%;'>";
echo "<tr style='background:#333; color:white;'><th>Original Name</th><th>New Name</th><th>Status</th></tr>";

$renamed_count = 0;
foreach ($rename_map as $old => $new) {
    $old_path = $image_folder . $old;
    $new_path = $image_folder . $new;
    
    if (file_exists($old_path)) {
        if (rename($old_path, $new_path)) {
            echo "<tr style='background:#d4edda;'>";
            echo "<td>$old</td>";
            echo "<td><strong>$new</strong></td>";
            echo "<td style='color:green;'>✅ Renamed</td>";
            $renamed_count++;
        } else {
            echo "<tr style='background:#f8d7da;'>";
            echo "<td>$old</td>";
            echo "<td>$new</td>";
            echo "<td style='color:red;'>❌ Failed to rename</td>";
        }
    } else {
        echo "<tr style='background:#fff3cd;'>";
        echo "<td>$old</td>";
        echo "<td>$new</td>";
        echo "<td style='color:orange;'>⚠️ Original file not found</td>";
    }
}
echo "</table>";

echo "<p style='margin-top:20px;'><strong>Total renamed: $renamed_count images</strong></p>";
echo "<p><a href='scan_images.php' style='padding:10px 20px; background:#4CAF50; color:white; text-decoration:none; border-radius:5px;'>Check New Names</a></p>";
?>