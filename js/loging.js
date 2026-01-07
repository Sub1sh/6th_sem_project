document.addEventListener("DOMContentLoaded", () => {

    // ================= Navbar Toggle =================
    const menuBtn = document.querySelector('#menu-btn');
    const navbar = document.querySelector('.navbar');

    if(menuBtn && navbar){
        menuBtn.addEventListener('click', () => {
            menuBtn.classList.toggle('fa-times');
            navbar.classList.toggle('active');
        });
    }

    // ================= Login Modal Toggle =================
    const loginBtns = document.querySelectorAll('#login-btn, #login-btn button'); // support multiple triggers
    const loginContainer = document.querySelector('.login-form-container');
    const closeLogin = document.querySelector('#close-login-form');

    loginBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            if(loginContainer){
                loginContainer.classList.add('active');
                showUser(); // default to user login
            }
        });
    });

    if(closeLogin && loginContainer){
        closeLogin.addEventListener('click', () => {
            loginContainer.classList.remove('active');
        });
    }

    // ================= Header Scroll Effect =================
    const header = document.querySelector('.header');
    window.addEventListener('scroll', () => {
        if(menuBtn && navbar){
            menuBtn.classList.remove('fa-times');
            navbar.classList.remove('active');
        }
        if(header){
            header.classList.toggle('active', window.scrollY > 0);
        }
    });

    // ================= Home Parallax Effect =================
    const home = document.querySelector('.home');
    if(home){
        home.addEventListener('mousemove', (e) => {
            document.querySelectorAll('.home-parallax').forEach(elm => {
                const speed = Number(elm.getAttribute('data-speed')) || 1;
                const x = (window.innerWidth - e.pageX * speed) / 90;
                const y = (window.innerHeight - e.pageY * speed) / 90;
                elm.style.transform = `translateX(${y}px) translateY(${x}px)`;
            });
        });

        home.addEventListener('mouseleave', () => {
            document.querySelectorAll('.home-parallax').forEach(elm => {
                elm.style.transform = `translateX(0px) translateY(0px)`;
            });
        });
    }

    // ================= Swiper Sliders =================
    const swiperSettings = {
        grabCursor: true,
        centeredSlides: true,
        spaceBetween: 20,
        loop: true,
        autoplay: { delay: 9500, disableOnInteraction: false },
        pagination: { el: ".swiper-pagination", clickable: true },
        breakpoints: {
            0: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 }
        }
    };

    new Swiper(".vehicles-slider", swiperSettings);
    new Swiper(".featured-slider", swiperSettings);
    new Swiper(".review-slider", swiperSettings);

    // ================= Toggle User/Admin Login Forms =================
    window.showUser = function() {
        const userForm = document.getElementById("userLogin");
        const adminForm = document.getElementById("adminLogin");
        if(userForm && adminForm){
            userForm.style.display = "block";
            adminForm.style.display = "none";
        }
    };

    window.showAdmin = function() {
        const userForm = document.getElementById("userLogin");
        const adminForm = document.getElementById("adminLogin");
        if(userForm && adminForm){
            userForm.style.display = "none";
            adminForm.style.display = "block";
        }
    };

});
