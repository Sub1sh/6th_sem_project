<?php
session_start();
include("connection.php");

// Redirect to admin login if not logged in
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: adminLogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Travel_X</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/sidebar.css">

    <style>
        body {
            background-image: url("image/background 2.jpg");
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
        }

        .adminTopic {
            text-align: center;
            color: #fff;
            margin-top: 20px;
        }

        table {
            width: 95%;
            border-collapse: separate;
            margin: 50px auto;
            text-align: center;
            background-color: #fff;
            border-radius: 10px 10px 0 0;
        }

        table th, table td {
            padding: 10px;
            border-bottom: 2px solid #bbb;
            font-size: 18px;
        }

        table td a {
            color: white;
            text-decoration: none;
            font-weight: 700;
        }

        button {
            padding: 5px 10px;
            border-radius: 7px;
            background-color: red;
            color: white;
            border: 2px solid yellow;
            cursor: pointer;
        }

        button:hover {
            background-color: darkred;
        }

        .btnPolicy {
            margin-left: 20px;
            margin-top: 20px;
        }

        .sidebar2 {
            margin-left: 220px; /* adjust based on your sidebar width */
            padding: 20px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<input type="checkbox" id="check">
<label for="check">
    <i class="fa fa-bars" id="btn"></i>
    <i class="fa fa-times" id="cancle"></i>
</label>

<div class="sidebar">
    <header>
        <img src="image/pic-4.png" alt="Admin">
        <p><?php echo $_SESSION['username']; ?></p>
    </header>
    <ul>
        <li><a href="adminDash.php">Manage Driver</a></li>
        <li><a href="ManageVehicle.php">Manage Vehicle</a></li>
        <li><a href="payManage.php">Transaction</a></li>
        <li><a href="adminLogout.php">Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="sidebar2">
    <h1 class="adminTopic">Management of Driver</h1>

    <?php
    $sqlget = "SELECT * FROM driver";
    $sqldata = mysqli_query($conn, $sqlget) or die("Error getting data");

    echo "<table>";
    echo "<tr>
            <th>ID</th>
            <th>Driver Name</th>
            <th>Telephone</th>
            <th>Address</th>
            <th>Anticipate Amount</th>
            <th>Update</th>
            <th>Delete</th>
          </tr>";

    while ($row = mysqli_fetch_array($sqldata, MYSQLI_ASSOC)) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['driver_name']}</td>
                <td>{$row['telephone']}</td>
                <td>{$row['address']}</td>
                <td>{$row['anticipate_amount']}</td>
                <td>
                    <button>
                        <a href='updateDriver.php?id={$row['id']}'>Update</a>
                    </button>
                </td>
                <td>
                    <button>
                        <a href='deleteDriver.php?id={$row['id']}'>Delete</a>
                    </button>
                </td>
              </tr>";
    }

    echo "</table>";
    ?>

    <a href="AddDriver.php"><button class="btnPolicy">Add Driver</button></a>
</div>

</body>
</html>
