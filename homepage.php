<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/homepage.css">

</head>
<body>

<?php include("nav.php");?>
 
<div class="login-form-container">

    <span id="close-login-form" class="fas fa-times"></span>

    <form action="">
    <h3>USER LOGIN</h3>
    <input type="email" placeholder="email" class="box" required>
    <input type="password" placeholder="password" class="box" required>
    
    <p>Forget Your Password? <a href="#">Click Here</a></p>
    
    <input type="submit" value="Login" class="btn">
    
    <p>Or Login With</p>
    <div class="buttons">
        <a href="#" class="btn">Google</a>
        <a href="#" class="btn">Facebook</a>
    </div>

    <p>Don't Have An Account? <a href="signUp.php">Create One</a></p>

    <!-- Admin Login Link -->
   <p>
  Are you an admin?
  <a href="adminLogin.php" onclick="closeLoginPopup()">Login as Admin</a>
</p>

</form>


</div>

<section class="home" id="home">

    <h3 data-speed="0" class="home-parallax">welcome to <lowercase>Travel_X "The name you can trust"</lowercase></h3>
	
	<h4 data-speed="0" class="home-parallax">vehicle rental service in Nepal </h4>
	
	<h5 data-speed="0" class="home-parallax">spells out beyond the travelling </h5>

    <img data-speed="0" class="home-parallax" src="image/vehicles 5.png" alt="">

    <a data-speed=".3" href="#" class="btn home-parallax">rent vehicles</a>

</section>

<section class="icons-container">

    <div class="icons">
        <i class="fas fa-home"></i>
        <div class="content">
            <h3>100+</h3>
            <p>branches</p>
        </div>
    </div>

    <div class="icons">
        <i class="fas fa-car"></i>
        <div class="content">
            <h3>5430+</h3>
            <p> rented out</p>
        </div>
    </div>

    <div class="icons">
        <i class="fas fa-users"></i>
        <div class="content">
            <h3>720+</h3>
            <p>happy clients</p>
        </div>
    </div>

    <div class="icons">
        <i class="fas fa-taxi"></i>
        <div class="content">
            <h3>250+</h3>
            <p>new arrivals</p>
        </div>
    </div>

</section>

<section class="vehicles" id="vehicles">

    <h1 class="heading"> popular renting vehicles<span></span> </h1>

    <div class="swiper vehicles-slider">

        <div class="swiper-wrapper">

            <div class="swiper-slide box">
                <img src="image/vehicle-1.png" alt="">
                <div class="content">
                    <h3> Rosso Corsa Red Ferrari</h3>
                    <div class="price"> <span>type : </span> luxury car </div>
                    <p>
                        new
                        <span class="fas fa-circle"></span> 2021
                        <span class="fas fa-circle"></span> automatic
                        <span class="fas fa-circle"></span> petrol
                        <span class="fas fa-circle"></span> 211mph
                    </p>
                    <a href="#" class="btn">check out</a>
                </div>
            </div>

            <div class="swiper-slide box">
                <img src="image/vehicle-2.png" alt="">
                <div class="content">
                    <h3>Grigio Silverstone Ferrari</h3>
                    <div class="price"> <span>type : </span> luxury car  </div>
                    <p>
                        new
                        <span class="fas fa-circle"></span> 2022
                        <span class="fas fa-circle"></span> automatic
                        <span class="fas fa-circle"></span> petrol
                        <span class="fas fa-circle"></span> 211mph
                    </p>
                    <a href="#" class="btn">check out</a>
                </div>
            </div>
			
			<div class="swiper-slide box">
                <img src="image/Yellow-Truck.png" alt="">
                <div class="content">
                    <h3>GENLVON Yellow Truck</h3>
                    <div class="price"> <span>type : </span> Truck </div>
                    <p>
                        new
                        <span class="fas fa-circle"></span> 2021
                        <span class="fas fa-circle"></span> manual
                        <span class="fas fa-circle"></span> diesel
                        <span class="fas fa-circle"></span> 55mph
                    </p>
                    <a href="#" class="btn">check out</a>
                </div>
            </div>
			
			<div class="swiper-slide box">
                <img src="image/truck.png" alt="">
                <div class="content">
                    <h3>IVECO Red Cargo Truck</h3>
                    <div class="price"> <span>type : </span> Truck </div>
                    <p>
                        new
                        <span class="fas fa-circle"></span> 2017
                        <span class="fas fa-circle"></span> manual
                        <span class="fas fa-circle"></span> diesel
                        <span class="fas fa-circle"></span> 51mph
                    </p>
                    <a href="#" class="btn">check out</a>
                </div>
            </div>

			<div class="swiper-slide box">
                <img src="image/indian-trucks.png" alt="">
                <div class="content">
                    <h3>Ashok Leyland Lorry</h3>
                    <div class="price"> <span>type : </span> Lorry </div>
                    <p>
                        new
                        <span class="fas fa-circle"></span> 2020
                        <span class="fas fa-circle"></span> manual
                        <span class="fas fa-circle"></span> diesel
                        <span class="fas fa-circle"></span> 53mph
                    </p>
                    <a href="#" class="btn">check out</a>
                </div>
            </div> 
			
			<div class="swiper-slide box">
                <img src="image/lorry.png" alt="">
                <div class="content">
                    <h3>Eicher Motors Box Lorry</h3>
                    <div class="price"> <span>type : </span> Lorry </div>
                    <p>
                        new
                        <span class="fas fa-circle"></span> 2018
                        <span class="fas fa-circle"></span> manual
                        <span class="fas fa-circle"></span> diesel
                        <span class="fas fa-circle"></span> 65mph
                    </p>
                    <a href="#" class="btn">check out</a>
                </div>
            </div>
			<div class="swiper-slide box">
                <img src="image/cargo-truck.png" alt="">
                <div class="content">
                    <h3>Quon Cargo Truck</h3>
                    <div class="price"> <span>type : </span> Truck </div>
                    <p>
                        new
                        <span class="fas fa-circle"></span> 2021
                        <span class="fas fa-circle"></span> manual
                        <span class="fas fa-circle"></span> diesel
                        <span class="fas fa-circle"></span> 52mph
                    </p>
                    <a href="#" class="btn">check out</a>
                </div>
            </div> 
			
			<div class="swiper-slide box">
                <img src="image/945-9457840_forland-dump-truck-6-tons-forland-trucks.png" alt="">
                <div class="content">
                    <h3>Hino Dump Truck</h3>
                    <div class="price"> <span>type : </span> Truck </div>
                    <p>
                        new
                        <span class="fas fa-circle"></span> 2019
                        <span class="fas fa-circle"></span> automatic
                        <span class="fas fa-circle"></span> diesel
                        <span class="fas fa-circle"></span> 50mph
                    </p>
                    <a href="#" class="btn">check out</a>
                </div>
            </div>
			
            <div class="swiper-slide box">
                <img src="image/vehicle-3.png" alt="">
                <div class="content">
                    <h3>PERODUA (DAIHATSU) AXIA</h3>
                    <div class="price"> <span>type : </span> General Car </div>
                    <p>
                        new
                        <span class="fas fa-circle"></span> 2021
                        <span class="fas fa-circle"></span> automatic
                        <span class="fas fa-circle"></span> petrol
                        <span class="fas fa-circle"></span> 183mph
                    </p>
                    <a href="#" class="btn">check out</a>
                </div>
            </div>

            <div class="swiper-slide box">
                <img src="image/vehicle-4.png" alt="">
                <div class="content">
                    <h3>Nissan Skyline GT-R R33</h3>
                    <div class="price"> <span>type: </span> Luxury Car </div>
                    <p>
                        new
                        <span class="fas fa-circle"></span> 2022
                        <span class="fas fa-circle"></span> automatic
                        <span class="fas fa-circle"></span> petrol
                        <span class="fas fa-circle"></span> 203mph
                    </p>
                    <a href="#" class="btn">check out</a>
                </div>
            </div>

            <div class="swiper-slide box">
                <img src="image/vehicle-5.png" alt="">
                <div class="content">
                    <h3>Perodua Bezza Prime Sedan</h3>
                    <div class="price"> <span>type: </span> Premium Car </div>
                    <p>
                        new
                        <span class="fas fa-circle"></span> 2021
                        <span class="fas fa-circle"></span> automatic
                        <span class="fas fa-circle"></span> petrol
                        <span class="fas fa-circle"></span> 183mph
                    </p>
                    <a href="#" class="btn">check out</a>
                </div>
            </div>

            <div class="swiper-slide box">
                <img src="image/vehicle-6.png" alt="">
                <div class="content">
                    <h3>TOYOTA NZE 141</h3>
                    <div class="price"> <span>type : </span> Premium Car </div>
                    <p>
                        new
                        <span class="fas fa-circle"></span> 2019
                        <span class="fas fa-circle"></span> automatic
                        <span class="fas fa-circle"></span> petrol
                        <span class="fas fa-circle"></span> 180mph
                    </p>
                    <a href="#" class="btn">check out</a>
                </div>
            </div>

        </div>

        <div class="swiper-pagination"></div>

    </div>

</section>

<section class="services" id="services">

    <h1 class="heading"> our services <span></span> </h1>

    <div class="box-container">

        <div class="box">
            <i class="fas fa-car"></i>
            <h3>self drive</h3>
            <p>We offer a wide range of self drive cars in Nepal from economical to luxury on daily, weekly and monthly terms. </p>
            <a href="#" class="btn"> read more</a>
        </div>

        <div class="box">
            <i class="fas fa-taxi"></i>
            <h3>tours/chauffeur driven</h3>
            <p>At Travel_X VEHICLE RENTAL SERVICE, providing you with the best experience of Nepal is our highest priority.</p>
            <a href="#" class="btn"> read more</a>
        </div>

        <div class="box">
            <i class="fas fa-calendar"></i>
            <h3>weddings & events</h3>
            <p>We are well geared for weddings and special occasions ensuring that the day's transport runs smoothly and in a punctual manner.</p>
            <a href="#" class="btn"> read more</a>
        </div>

        <div class="box">
            <i class="fas fa-plane"></i>
            <h3>airport/city transfers</h3>
            <p>We offer city and airport transfers in Nepal from the Kathmandu International Airport to any location in the country.</p>
            <a href="#" class="btn"> read more</a>
        </div>

        <div class="box">
            <i class="fas fa-gas-pump"></i>
            <h3>oil change</h3>
            <p>Once you experience our oil change, you'll see firsthand why millions of motorists turn to us for asset protection.</p>
            <a href="#" class="btn"> read more</a>
        </div>

        <div class="box">
            <i class="fas fa-headset"></i>
            <h3>24/7 support</h3>
            <p>Our 24/7 service guarantee is backed by an in-house staff on call on-site, additional AA cover as well as an extensive network of garages offering roadside assistance if required.</p>
            <a href="#" class="btn"> read more</a>
        </div>

    </div>

</section>

<section class="featured" id="featured">

    <h1 class="heading"> <span></span> Car Vehicle Conditions </h1>
	<h1 class="swiper featured-slider"> <span></span>our sudden references :</h1>

    <div class="swiper featured-slider">

       <div class="swiper-wrapper">

            <div class="swiper-slide box">
                <img src="image/car-1.png" alt="">
                <div class="content">
                    <h3>MERCEDES BENZ C350E NEW C-CLASS</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <div class="condition">Leather Int. A/C, Ambient/ Mood lighting, Power Steering, Blue Tooth, With Driver Only</div>
                    <a href="#" class="btn">check out</a>
                </div>
            </div>

            <div class="swiper-slide box">
                <img src="image/car-2.png" alt="">
                <div class="content">
                    <h3>CHRYSLER 300C (ROLLS ROYCE FACELIFT)</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <div class="condition">Leather Int. A/C, Power Steering, CD Player, With Driver Only</div>
                    <a href="#" class="btn">check out</a>
                </div>
            </div>

            <div class="swiper-slide box">
                <img src="image/car-3.png" alt="">
                <div class="content">
                    <h3>JAGUAR XF 3.0 V6</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <div class="condition">Leather Int. A/C, Power Steering, CD Player, With Driver Only</div>
                    <a href="#" class="btn">check out</a>
                </div>
            </div>

            <div class="swiper-slide box">
                <img src="image/car-4.png" alt="">
                <div class="content">
                    <h3>PERODUA BEZZA PRIME SEDAN</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <div class="condition">Perodua Bezza Prime Sedan A/C, Power Steering, CD Player, Auto</div>
                    <a href="#" class="btn">check out</a>
                </div>
            </div>

       </div>

       <div class="swiper-pagination"></div>

    </div>

    <div class="swiper featured-slider">

        <div class="swiper-wrapper">
 
             <div class="swiper-slide box">
                 <img src="image/car-5.png" alt="">
                 <div class="content">
                     <h3>TOYOTA COROLLA NKR 165 AXIO HYBRID</h3>
                     <div class="stars">
                         <i class="fas fa-star"></i>
                         <i class="fas fa-star"></i>
                         <i class="fas fa-star"></i>
                         <i class="fas fa-star"></i>
                         <i class="fas fa-star-half-alt"></i>
                     </div>
                     <div class="condition">A/C, Power Steering, CD Player, Auto</div>
                     <a href="#" class="btn">check out</a>
                 </div>
             </div>
 
             <div class="swiper-slide box">
                 <img src="image/car-6.png" alt="">
                 <div class="content">
                     <h3>TOYOTA YARIS SEDAN/ BELTA</h3>
                     <div class="stars">
                         <i class="fas fa-star"></i>
                         <i class="fas fa-star"></i>
                         <i class="fas fa-star"></i>
                         <i class="fas fa-star"></i>
                         <i class="fas fa-star-half-alt"></i>
                     </div>
                     <div class="condition">A/C, Power Steering, CD Player, Auto</div>
                     <a href="#" class="btn">check out</a>
                 </div>
             </div>
 
             <div class="swiper-slide box">
                 <img src="image/car-7.png" alt="">
                 <div class="content">
                     <h3>BMW 320 - White</h3>
                     <div class="stars">
                         <i class="fas fa-star"></i>
                         <i class="fas fa-star"></i>
                         <i class="fas fa-star"></i>
                         <i class="fas fa-star"></i>
                         <i class="fas fa-star-half-alt"></i>
                     </div>
                     <div class="condition">Leather Int. A/C, Power Steering, CD Player, With Driver Only</div>
                     <a href="#" class="btn">check out</a>
                 </div>
             </div>
 
             <div class="swiper-slide box">
                 <img src="image/car-8.png" alt="">
                 <div class="content">
                     <h3>MERCEDES BENZ C-CLASS</h3>
                     <div class="stars">
                         <i class="fas fa-star"></i>
                         <i class="fas fa-star"></i>
                         <i class="fas fa-star"></i>
                         <i class="fas fa-star"></i>
                         <i class="fas fa-star-half-alt"></i>
                     </div>
                     <div class="condition">A/C, Power Steering, CD Player, With Driver Only</div>
                     <a href="#" class="btn">check out</a>
                 </div>
             </div>
 
        </div>
 
        <div class="swiper-pagination"></div>
 
     </div>

</section>

<section class="newsletter">
    
    <h3>subscribe for latest updates</h3>
    <p>You are able to recieve new updates about us</p>

   <form action="">
        <input type="email" placeholder="enter your email">
        <input type="submit" value="subscribe">
   </form>

</section>

<section class="reviews" id="reviews">

    <h1 class="heading"> client's reviews<span></span> </h1>

    <div class="swiper review-slider">

        <div class="swiper-wrapper">

            <div class="swiper-slide box">
                <img src="image/pic-1.png" alt="">
                <div class="content">
                    <p>Ohh wow it was really nice😍. I recieved a good service from them without delay.</p>
                    <h3>Rajesh Thapa</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                </div>
            </div>

            <div class="swiper-slide box">
                <img src="image/pic-2.png" alt="">
                <div class="content">
                    <p>Really happy for being a customer with Travel_X.</p>
                    <h3>Sunita Gurung</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                </div>
            </div>

            <div class="swiper-slide box">
                <img src="image/pic-3.png" alt="">
                <div class="content">
                    <p>I am adimred their punctuality and attendance. Keep it up👍 </p>
                    <h3>Bikash Shrestha</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                </div>
            </div>

            <div class="swiper-slide box">
                <img src="image/pic-4.png" alt="">
                <div class="content">
                    <p>Last year I needed to be at the airport at puncual. Once Travel_X came to my mind and called them and they were arrived two hours earlier🤗. </p>
                    <h3>Anita Rai</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                </div>
            </div>

            <div class="swiper-slide box">
                <img src="image/pic-5.png" alt="">
                <div class="content">
                    <p>Much satisfied!!👌</p>
                    <h3>Prakash Karki</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                </div>
            </div>

            <div class="swiper-slide box">
                <img src="image/pic-6.png" alt="">
                <div class="content">
                    <p>Best and indeed attractive vehicle collection they have. As well as they are provided new brands;necessary matchings (vehicles) accrording to customer requirements.</p>
                    <h3>Sabina Basnet</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                </div>
            </div>

        </div>

        <div class="swiper-pagination"></div>

    </div>

</section>

<section class="contact" id="contact">

    <h1 class="heading"><span></span>contact us</h1>

    <div class="row">

        <p><iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3532.464434429981!2d85.3239493150562!3d27.70259938279401!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39eb19a64b5f13e1%3A0x28b2d0eacda46b98!2sKathmandu%2044600%2C%20Nepal!5e0!3m2!1sen!2s!4v1652975784969!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></p>
        
        <form action="">
            <h3>get in touch</h3>
            <input type="text" placeholder="your name" class="box">
            <input type="email" placeholder="your email" class="box">
            <input type="tel" placeholder="subject" class="box">
            <textarea placeholder="your message" class="box" cols="30" rows="10"></textarea>
            <input type="submit" value="send message" class="btn">
        </form>

    </div>

</section>

<section class="footer" id="footer">

    <div class="box-container">

        <div class="box">
            <h3>our branches</h3>
            <a href="#"> <i class="fas fa-map-marker-alt"></i> Kathmandu </a>
            <a href="#"> <i class="fas fa-map-marker-alt"></i> Pokhara </a>
            <a href="#"> <i class="fas fa-map-marker-alt"></i> Chitwan </a>
            <a href="#"> <i class="fas fa-map-marker-alt"></i> Lumbini </a>
            <a href="#"> <i class="fas fa-map-marker-alt"></i> Bhaktapur</a>
        </div>

        <div class="box">
            <h3>quick links</h3>
            <a href="#"> <i class="fas fa-arrow-right"></i> home </a>
            <a href="#"> <i class="fas fa-arrow-right"></i> about us </a>
            <a href="#"> <i class="fas fa-arrow-right"></i> vehicle fleet </a>
            <a href="#"> <i class="fas fa-arrow-right"></i> services</a>
			<a href="#"> <i class="fas fa-arrow-right"></i> rates</a>
            <a href="#"> <i class="fas fa-arrow-right"></i> reviews </a>
            <a href="#"> <i class="fas fa-arrow-right"></i> contact us </a>
        </div>

        <div class="box">
            <h3>contact info</h3>
            <a href="#"> <i class="fas fa-phone"></i> +977-456-7890 </a>
            <a href="#"> <i class="fas fa-phone"></i> +977-222-3333 </a>
            <a href="#"> <i class="fas fa-envelope"></i> travelx@gmail.com </a>
            <a href="#"> <i class="fas fa-map-marker-alt"></i> Kathmandu, Nepal - 44600 </a>
        </div>

        <div class="box">
            <h3>contact info</h3>
            <a href="#"> <i class="fab fa-facebook-f"></i> facebook </a>
            <a href="#"> <i class="fab fa-twitter"></i> twitter </a>
            <a href="#"> <i class="fab fa-instagram"></i> instagram </a>
            <a href="#"> <i class="fab fa-linkedin"></i> linkedin </a>
            <a href="#"> <i class="fab fa-pinterest"></i> pinterest </a>
        </div>

    </div>

    <div class="credit"> created by Travel_X | all copyrights reserved by Travel_X - 2025 </div>

</section>

<script src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>

<script src="js/homepage.js"></script>
<script>
function closeLoginPopup() {
    document.querySelector('.login-form-container').classList.remove('active');
}
</script>

</body>
</html>