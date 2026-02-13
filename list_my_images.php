<?php
// list_my_images.php - Show what images you actually have
echo "<h2>📁 Your Actual Images</h2>";

if (is_dir('images')) {
    $files = scandir('images');
    echo "<p>Found " . (count($files) - 2) . " files in images folder</p>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Filename</th><th>Preview</th></tr>";
    
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($file) . "</td>";
            echo "<td><img src='images/" . urlencode($file) . "' width='100' height='80' style='object-fit:cover;' onerror='this.src=\"https://via.placeholder.com/100x80?text=Error\"'></td>";
            echo "</tr>";
        }
    }
    echo "</table>";
} else {
    echo "<p style='color:red'>❌ images folder doesn't exist!</p>";
}
?>