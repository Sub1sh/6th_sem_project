<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/aboutus.css">

    <title>About Us - Travel_X</title>
</head>
<body>
    
<?php include("nav.php"); ?>

<section class="body">

    <div class="au">
        <h1>About Us</h1>
    </div>

    <!-- OUR COMPANY -->
    <div class="company section">
        <div class="imgs">
            <img src="image/company.png" alt="Company Image">
        </div>
        <div class="para">
            <h2>OUR COMPANY</h2>
            <p>
                Travel_X is Nepal's premier vehicle rental service, established in 2015 with a vision to transform transportation solutions across the Himalayan nation. Starting with just 5 vehicles in Kathmandu, we have grown to become one of Nepal's most trusted rental services with over 500 vehicles across 15 locations. Our diverse fleet ranges from economy cars for budget-conscious travelers to luxury vehicles for special occasions,
                <span id="dots"> ... </span>
                <span id="more">along with trucks and commercial vehicles for business needs. We take pride in our Nepali roots while maintaining international service standards. Our team of 150+ dedicated professionals includes experienced drivers, maintenance experts, and customer service representatives who understand the unique challenges of Nepal's diverse terrain and road conditions. From the bustling streets of Kathmandu to the challenging mountain roads of Mustang, Travel_X has been the trusted choice for both locals and tourists seeking reliable transportation solutions.</span>
            </p>
            <button onclick="company()" id="myBtn">Read more</button>
        </div>
    </div>

    <!-- OUR MISSION -->
    <div class="mission section">
        <div class="imgs">
            <img src="image/mission.png" alt="Mission Image">
        </div>
        <div class="para">
            <h2>OUR MISSION</h2>
            <p>
                At Travel_X, our mission is to provide safe, reliable, and accessible transportation solutions that empower mobility across Nepal. We are committed to enhancing travel experiences through well-maintained vehicles, professional service, and competitive pricing. We strive to understand and meet the unique transportation needs of our diverse clientele,
                <span id="dots2"> ... </span>
                <span id="more2">whether they are tourists exploring Nepal's natural wonders, businesses requiring logistical support, or families celebrating special occasions. Our commitment extends beyond rentals - we aim to contribute to Nepal's tourism industry and economic development by creating employment opportunities and supporting local communities. Through continuous innovation and customer-focused service, we work to set new standards in Nepal's vehicle rental industry while maintaining our core values of integrity, reliability, and exceptional customer care.</span>
            </p>
            <button onclick="mission()" id="myBtn2">Read more</button>
        </div>
    </div>

    <!-- OUR CLIENTS -->
    <div class="clients section">
        <div class="imgs">
            <img src="image/clients.png" alt="Clients Image">
        </div>
        <div class="para">
            <h2>OUR CLIENTS</h2>
            <p>
                Travel_X serves a diverse clientele that reflects the vibrant tapestry of Nepal itself. Our customers range from international tourists seeking to explore the Himalayas to local businesses requiring reliable transportation for their operations. We proudly serve diplomatic missions, international NGOs, corporate clients, and thousands of individual customers who trust us for their mobility needs.
                <span id="dots3"> ... </span>
                <span id="more3">Our partnerships extend to major hotels, travel agencies, and tourism operators across Nepal who recommend our services to their guests. We're honored to have supported numerous film productions, adventure expeditions, and cultural events throughout the country. What unites our diverse client base is their need for dependable vehicles that can handle Nepal's unique road conditions while providing comfort and safety. Their continued trust and valuable feedback have been instrumental in shaping our services and driving our growth over the years.</span>
            </p>
            <button onclick="clients()" id="myBtn3">Read more</button>
        </div>
    </div>

</section>

<?php include("footer.php"); ?>

<!-- Swiper JS -->
<script src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>

<!-- About Us JS -->
<script src="js/aboutus.js"></script>

</body>
</html>
