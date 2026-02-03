<?php
session_start();
include_once(__DIR__ . "/connection.php");

// get logged-in user id if available
$userId = $_SESSION['user_id'] ?? null;

// default vehicle type
$preferredVehicle = null;

if ($userId) {
    // try to get the last rented vehicle for this user
    $sql = "SELECT v.vehicle_name 
            FROM payments p 
            JOIN vehicle v ON p.vehicle_id = v.id 
            WHERE p.user_id = '$userId' 
            ORDER BY p.id DESC LIMIT 1";

    $res = mysqli_query($conn, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $preferredVehicle = $row['vehicle_name'];
    }
}

// fetch all vehicles, with preferred vehicle first if available
if ($preferredVehicle) {
    $query = "SELECT * FROM vehicle 
              ORDER BY (vehicle_name = '$preferredVehicle') DESC, id DESC";
} else {
    $query = "SELECT * FROM vehicle ORDER BY id DESC";
}

$result = mysqli_query($conn, $query);
?>

<section class="vehicles" id="recommended">
    <h1 class="heading">Recommended for you<span></span></h1>

    <div class="swiper vehicles-slider">
        <div class="swiper-wrapper">

            <?php if ($result && mysqli_num_rows($result) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <div class="swiper-slide box">
                        <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['vehicle_name']; ?>">
                        <div class="content">
                            <h3><?php echo $row['vehicle_name']; ?></h3>

                            <div class="price">
                                <span>Price: </span> Rs. <?php echo $row['amount']; ?>
                            </div>

                            <p>
                                Model: <?php echo $row['model']; ?>
                                <span class="fas fa-circle"></span>
                                Transmission: <?php echo $row['transmission']; ?>
                                <span class="fas fa-circle"></span>
                                Fuel: <?php echo $row['fuel_type']; ?>
                            </p>

                            <a href="#" class="btn">Check Out</a>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p style="padding:20px;">No recommendations available.</p>
            <?php } ?>

        </div>
        <div class="swiper-pagination"></div>
    </div>
</section>