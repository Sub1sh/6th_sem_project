<?php
// check_images.php - Check if image files exist
echo "<h2>Checking Image Files</h2>";

$images_to_check = [
    'images/vehicle-1.png',
    'images/vehicle-2.png',
    'images/vehicle-3.png',
    'images/vehicle-4.png',
    'images/vehicle-5.png',
    'images/vehicle-6.png',
    'images/car-1.png',
    'images/car-2.png',
    'images/car-3.png',
    'images/car-5.png',
    'images/car-7.png',
    'images/car-8.png',
    'images/tipper-truck.png',
    'images/Yellow-Truck.png',
    'images/White Bus on Rural Road.png',
    'images/vehicles 1.jpg',
    'images/vehicles 2.jpg',
    'images/vehicles 3.png',
    'images/vehicles 4.png',
    'images/vehicles 5.png',
    'images/vehicles 6.png'
];

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Image Path</th><th>Exists?</th><th>Preview</th></tr>";

foreach ($images_to_check as $path) {
    echo "<tr>";
    echo "<td>$path</td>";
    if (file_exists($path)) {
        echo "<td style='color:green'>✅ YES</td>";
        echo "<td><img src='$path' width='100' height='80' style='object-fit:cover'></td>";
    } else {
        echo "<td style='color:red'>❌ NO</td>";
        echo "<td>-</td>";
    }
    echo "</tr>";
}

echo "</table>";

// Also check if the images folder exists
echo "<h3>Images Directory:</h3>";
if (is_dir('images')) {
    echo "✅ 'images' folder exists<br>";
    $files = scandir('images');
    echo "Files in images folder: " . count($files) . "<br>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
} else {
    echo "❌ 'images' folder does NOT exist!<br>";
    echo "Current directory: " . __DIR__;
}
?>