<?php
// -------------------------------
// User Authentication Functions
// -------------------------------

/**
 * Check if a user is logged in.
 * If logged in, return user data from database.
 * If not, redirect to login page.
 *
 * @param mysqli $conn
 * @return array|null
 */
function check_login($conn)
{
    session_start();

    if (isset($_SESSION['user_id'])) {
        $id = intval($_SESSION['user_id']); // ensure $id is an integer for security

        // Prepare SQL query to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
            return $user_data;
        }
    }

    // Redirect to login page if not logged in
    header("Location: Login.php");
    exit();
}

/**
 * Generate a random numeric string.
 *
 * @param int $length Minimum length is 5
 * @return string Random number string
 */
function random_num($length = 5)
{
    $length = max(5, intval($length)); // ensure minimum length of 5
    $len = rand(4, $length); // randomize the actual length

    $text = "";
    for ($i = 0; $i < $len; $i++) {
        $text .= rand(0, 9);
    }

    return $text;
}
?>
