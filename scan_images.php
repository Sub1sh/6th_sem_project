<?php
// scan_images.php - Scan and display all actual images in your folder
echo "<h1>📸 SCANNING YOUR ACTUAL IMAGES</h1>";

$image_folder = 'images/';

if (!is_dir($image_folder)) {
    echo "<p style='color:red'>❌ Images folder not found!</p>";
    exit;
}

$files = scandir($image_folder);
$images = [];

foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        // Get file extension
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        // Check if it's an image file
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $images[] = $file;
        }
    }
}

echo "<p>Found <strong>" . count($images) . "</strong> images in your folder</p>";

echo "<div style='display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;'>";

foreach ($images as $img) {
    $img_path = $image_folder . $img;
    $file_size = filesize($img_path);
    $size_kb = round($file_size / 1024, 2);
    
    echo "<div style='border:1px solid #ddd; padding:10px; border-radius:8px; text-align:center; background:#f9f9f9;'>";
    echo "<img src='$img_path' style='width:100%; height:150px; object-fit:cover; border-radius:5px;' onerror='this.style.display=\"none\"'>";
    echo "<div style='margin-top:10px; font-weight:bold;'>" . htmlspecialchars($img) . "</div>";
    echo "<div style='color:#666; font-size:12px;'>" . $size_kb . " KB</div>";
    echo "</div>";
}

echo "</div>";

// Also show raw list for copying
echo "<h2>📋 Image List (for copying)</h2>";
echo "<textarea style='width:100%; height:200px; padding:10px;' readonly>";
foreach ($images as $img) {
    echo $img . "\n";
}
echo "</textarea>";
?>