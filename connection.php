<?php
// connection.php - Updated with better connection handling

define('ENVIRONMENT', 'development');

// Try different connection methods
class DatabaseConnector {
    private $conn;
    
    public function connect() {
        $configs = [
            ['host' => '127.0.0.1', 'user' => 'root', 'pass' => '', 'name' => 'rental'],
            ['host' => 'localhost', 'user' => 'root', 'pass' => '', 'name' => 'rental'],
            ['host' => '127.0.0.1', 'user' => 'root', 'pass' => 'root', 'name' => 'rental'], // If you set root password
        ];
        
        foreach ($configs as $config) {
            $this->conn = @mysqli_connect(
                $config['host'],
                $config['user'],
                $config['pass'],
                $config['name'],
                3306
            );
            
            if ($this->conn) {
                mysqli_set_charset($this->conn, 'utf8mb4');
                return $this->conn;
            }
        }
        
        $this->handleConnectionError();
        return false;
    }
    
    private function handleConnectionError() {
        $error = mysqli_connect_error();
        
        if (ENVIRONMENT === 'development') {
            $message = "Database Connection Error: $error<br>";
            $message .= "Possible Solutions:<br>";
            $message .= "1. Check if XAMPP MySQL is running<br>";
            $message .= "2. Try using 127.0.0.1 instead of localhost<br>";
            $message .= "3. Check MySQL user privileges<br>";
            $message .= "4. Verify database name exists<br>";
            
            die($message);
        } else {
            die("Database connection error. Please contact administrator.");
        }
    }
    
    public function getConnection() {
        if (!$this->conn || !mysqli_ping($this->conn)) {
            return $this->connect();
        }
        return $this->conn;
    }
}

// Create connection
$dbConnector = new DatabaseConnector();
$conn = $dbConnector->connect();

if (!$conn) {
    die("Failed to establish database connection");
}

// Test connection
if (ENVIRONMENT === 'development') {
    $test = mysqli_query($conn, "SELECT 1");
    if (!$test) {
        die("Connection test failed: " . mysqli_error($conn));
    }
}
?>