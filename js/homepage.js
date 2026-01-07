document.addEventListener("DOMContentLoaded", () => {

    // ===================== Cache DOM Elements =====================
    const menu = document.querySelector('#menu-btn');
    const navbar = document.querySelector('.navbar');
    const header = document.querySelector('.header');
    const loginForm = document.querySelector('.login-form-container');
    const loginBtn = document.querySelector('#login-btn');
    const closeLoginBtn = document.querySelector('#close-login-form');
    const homeSection = document.querySelector('.home');

    // ===================== Menu Toggle =====================
    if (menu && navbar) {
        menu.addEventListener('click', () => {
            menu.classList.toggle('fa-times');
            navbar.classList.toggle('active');
        });
    }

    // ===================== Login Form Toggle =====================
    if (loginBtn && loginForm) {
        loginBtn.addEventListener('click', () => {
            loginForm.classList.add('active');
        });
    }

    if (closeLoginBtn && loginForm) {
        closeLoginBtn.addEventListener('click', () => {
            loginForm.classList.remove('active');
        });
    }

    // Close login when clicking outside
    document.addEventListener('click', (e) => {
        if (loginForm && loginForm.classList.contains('active') &&
            !e.target.closest('.login-form-container') &&
            !e.target.closest('#login-btn')) {
            loginForm.classList.remove('active');
        }
    });

    // Close login on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape" && loginForm && loginForm.classList.contains('active')) {
            loginForm.classList.remove('active');
        }
    });

    // ===================== Header Scroll Effect =====================
    window.addEventListener('scroll', () => {
        if (menu && navbar) {
            menu.classList.remove('fa-times');
            navbar.classList.remove('active');
        }
        if (header) {
            header.classList.toggle('active', window.scrollY > 0);
        }
    });

    // ===================== Parallax Effect =====================
    const isTouchDevice = () => ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);

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
                elm.style.transform = `translateX(0px) translateY(0px)`;
            });
        });
    }

    // ===================== Swiper Sliders =====================
    function initSwiper(selector, config = {}) {
        try {
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
                        1024: { slidesPerView: 3 }
                    },
                    ...config
                });
            }
        } catch (err) {
            console.error(`Swiper init error for ${selector}:`, err);
        }
        return null;
    }

    const vehiclesSwiper = initSwiper(".vehicles-slider");
    const featuredSwiper = initSwiper(".featured-slider");
    const reviewSwiper = initSwiper(".review-slider");

    // Hover pause functionality
    function addHoverPause(swiperInstance, selector) {
        const element = document.querySelector(selector);
        if (element && swiperInstance) {
            element.addEventListener('mouseenter', () => swiperInstance.autoplay.stop());
            element.addEventListener('mouseleave', () => swiperInstance.autoplay.start());
        }
    }

    addHoverPause(vehiclesSwiper, ".vehicles-slider");
    addHoverPause(featuredSwiper, ".featured-slider");
    addHoverPause(reviewSwiper, ".review-slider");

    // ===================== Lazy Load / Intersection Observer =====================
    const observerOptions = { root: null, rootMargin: '0px', threshold: 0.1 };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
            }
        });
    }, observerOptions);

    document.querySelectorAll('section').forEach(section => observer.observe(section));

    // ===================== Keyboard Navigation for Sliders =====================
    document.addEventListener('keydown', (e) => {
        const activeSlide = document.activeElement.closest('.swiper-slide');
        if (!activeSlide) return;

        switch (e.key) {
            case 'ArrowLeft':
                e.preventDefault();
                vehiclesSwiper?.slidePrev();
                featuredSwiper?.slidePrev();
                reviewSwiper?.slidePrev();
                break;
            case 'ArrowRight':
                e.preventDefault();
                vehiclesSwiper?.slideNext();
                featuredSwiper?.slideNext();
                reviewSwiper?.slideNext();
                break;
        }
    });

    // ===================== Smooth Scroll =====================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

});
