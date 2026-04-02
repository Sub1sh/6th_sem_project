<?php
include_once(__DIR__ . "/connection.php");

echo "<h2>Vehicles Table Structure:</h2>";
$result = $conn->query("SHOW COLUMNS FROM vehicles");
if ($result) {
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>Column Name</th><th>Type</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "Vehicles table doesn't exist yet.";
}

echo "<h2>Sample Data (if any):</h2>";
$data = $conn->query("SELECT * FROM vehicles LIMIT 5");
if ($data && $data->num_rows > 0) {
    while ($row = $data->fetch_assoc()) {
        echo "<pre>";
        print_r($row);
        echo "</pre>";
    }
} else {
    echo "No data in vehicles table.";
}
?>