<?php
// map_actual_images.php - Map your ACTUAL images to vehicles
include_once("connection.php");

echo "<h1>🗺️ MAPPING ACTUAL IMAGES TO VEHICLES</h1>";

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get all actual images from folder
$image_folder = 'images/';
$actual_images = [];

if (is_dir($image_folder)) {
    $files = scandir($image_folder);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $actual_images[] = $file;
            }
        }
    }
}

echo "<p>Found <strong>" . count($actual_images) . "</strong> actual images</p>";

// Display actual images
echo "<div style='display:flex; flex-wrap:wrap; gap:10px; margin-bottom:20px;'>";
foreach ($actual_images as $img) {
    echo "<span style='background:#e0e0e0; padding:5px 10px; border-radius:20px; font-size:12px;'>$img</span>";
}
echo "</div>";

// Get all vehicles
$sql = "SELECT id, brand, model, type FROM vehicles ORDER BY id";
$result = mysqli_query($conn, $sql);

echo "<table border='1' cellpadding='8' style='border-collapse:collapse; width:100%;'>";
echo "<tr style='background:#333; color:white;'><th>ID</th><th>Vehicle</th><th>Type</th><th>Suggested Image</th><th>Action</th></tr>";

$image_index = 0;
$total_images = count($actual_images);

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
    $full_name = $row['brand'] . ' ' . $row['model'];
    $type = $row['type'];
    
    // Suggest an image based on vehicle type or name
    $suggested_image = '';
    
    // Try to find matching image by brand or model
    foreach ($actual_images as $img) {
        $img_lower = strtolower($img);
        $brand_lower = strtolower($row['brand']);
        $model_lower = strtolower($row['model']);
        
        if (strpos($img_lower, $brand_lower) !== false || strpos($img_lower, $model_lower) !== false) {
            $suggested_image = $img;
            break;
        }
    }
    
    // If no match, cycle through images
    if (empty($suggested_image) && $total_images > 0) {
        $suggested_image = $actual_images[$image_index % $total_images];
        $image_index++;
    }
    
    echo "<tr>";
    echo "<td>$id</td>";
    echo "<td><strong>$full_name</strong><br><small>$type</small></td>";
    echo "<td>$type</td>";
    echo "<td>";
    if ($suggested_image) {
        echo "<img src='images/$suggested_image' style='height:50px; width:80px; object-fit:cover; vertical-align:middle; margin-right:10px;'> ";
        echo $suggested_image;
    } else {
        echo "No image available";
    }
    echo "</td>";
    echo "<td>";
    echo "<form method='POST' style='display:inline;'>";
    echo "<input type='hidden' name='vehicle_id' value='$id'>";
    echo "<select name='selected_image'>";
    echo "<option value=''>Select image</option>";
    foreach ($actual_images as $img) {
        $selected = ($img == $suggested_image) ? 'selected' : '';
        echo "<option value='$img' $selected>$img</option>";
    }
    echo "</select>";
    echo "<button type='submit' name='assign' style='margin-left:5px; padding:5px 10px;'>Assign</button>";
    echo "</form>";
    echo "</td>";
    echo "</tr>";
}

echo "</table>";

// Handle form submission
if (isset($_POST['assign']) && isset($_POST['vehicle_id']) && isset($_POST['selected_image']) && !empty($_POST['selected_image'])) {
    $vehicle_id = (int)$_POST['vehicle_id'];
    $selected_image = mysqli_real_escape_string($conn, $_POST['selected_image']);
    $image_path = 'images/' . $selected_image;
    
    $update_sql = "UPDATE vehicles SET image_url = '$image_path' WHERE id = $vehicle_id";
    
    if (mysqli_query($conn, $update_sql)) {
        echo "<div style='background:#4CAF50; color:white; padding:10px; margin-top:20px; border-radius:5px;'>✅ Vehicle ID $vehicle_id updated to $selected_image</div>";
        echo "<script>setTimeout(function(){ window.location.href = 'map_actual_images.php'; }, 2000);</script>";
    } else {
        echo "<div style='background:#f44336; color:white; padding:10px; margin-top:20px; border-radius:5px;'>❌ Error: " . mysqli_error($conn) . "</div>";
    }
}

mysqli_close($conn);
?>