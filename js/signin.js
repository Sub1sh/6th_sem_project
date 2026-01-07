document.addEventListener("DOMContentLoaded", () => {

    // ===================== Cache DOM Elements =====================
    const menu = document.querySelector('#menu-btn');
    const navbar = document.querySelector('.navbar');
    const header = document.querySelector('.header');
    const loginForm = document.querySelector('.login-form-container');
    const homeSection = document.querySelector('.home');

    // ===================== Menu Toggle =====================
    menu?.addEventListener('click', () => {
        menu.classList.toggle('fa-times');
        navbar?.classList.toggle('active');
    });

    // ===================== Login Modal =====================
    document.querySelector('#login-btn')?.addEventListener('click', () => {
        loginForm?.classList.toggle('active');
    });

    document.querySelector('#close-login-form')?.addEventListener('click', () => {
        loginForm?.classList.remove('active');
    });

    // ===================== Header Scroll Effect =====================
    window.addEventListener('scroll', () => {
        menu?.classList.remove('fa-times');
        navbar?.classList.remove('active');
        header?.classList.toggle('active', window.scrollY > 0);
    });

    // ===================== Parallax Effect =====================
    const isTouchDevice = () => 'ontouchstart' in window || navigator.maxTouchPoints > 0;

    if (homeSection && !isTouchDevice()) {
        homeSection.addEventListener('mousemove', (e) => {
            document.querySelectorAll('.home-parallax').forEach(elm => {
                const speed = parseFloat(elm.dataset.speed) || 1;
                const x = (window.innerWidth - e.pageX * speed) / 90;
                const y = (window.innerHeight - e.pageY * speed) / 90;
                elm.style.transform = `translateX(${y}px) translateY(${x}px)`;
            });
        });

        homeSection.addEventListener('mouseleave', () => {
            document.querySelectorAll('.home-parallax').forEach(elm => {
                elm.style.transform = 'translateX(0px) translateY(0px)';
            });
        });
    }

    // ===================== Swiper Initialization =====================
    const initSwiper = (selector) => {
        if (document.querySelector(selector)) {
            return new Swiper(selector, {
                grabCursor: true,
                centeredSlides: true,
                spaceBetween: 20,
                loop: true,
                autoplay: { delay: 9500, disableOnInteraction: false },
                pagination: { el: ".swiper-pagination", clickable: true },
                breakpoints: {
                    0: { slidesPerView: 1 },
                    768: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 },
                },
            });
        }
        return null;
    };

    const vehiclesSwiper = initSwiper(".vehicles-slider");
    const featuredSwiper = initSwiper(".featured-slider");
    const reviewSwiper = initSwiper(".review-slider");

});
