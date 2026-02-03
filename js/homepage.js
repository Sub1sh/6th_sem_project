document.addEventListener("DOMContentLoaded", () => {
    console.log("Travel_X - Vehicle Rental Service initialized");

    // ===================== Cache DOM Elements =====================
    const menuBtn = document.querySelector('#menu-btn');
    const navbar = document.querySelector('.navbar');
    const header = document.querySelector('.header');
    const loginForm = document.querySelector('.login-form-container');
    const loginBtn = document.querySelector('#login-btn');
    const closeLoginBtn = document.querySelector('#close-login-form');
    const homeSection = document.querySelector('.home');
    const scrollTopBtn = document.querySelector('.scroll-top');

    // ===================== Menu Toggle =====================
    if (menuBtn && navbar) {
        menuBtn.addEventListener('click', () => {
            menuBtn.classList.toggle('fa-times');
            navbar.classList.toggle('active');
            document.body.style.overflow = navbar.classList.contains('active') ? 'hidden' : '';
        });
    }

    // ===================== Login Form Toggle =====================
    if (loginBtn && loginForm) {
        loginBtn.addEventListener('click', (e) => {
            e.preventDefault();
            loginForm.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    if (closeLoginBtn && loginForm) {
        closeLoginBtn.addEventListener('click', () => {
            loginForm.classList.remove('active');
            document.body.style.overflow = '';
        });
    }

    // Close login when clicking outside
    document.addEventListener('click', (e) => {
        if (loginForm && loginForm.classList.contains('active') &&
            !e.target.closest('.login-form-container') &&
            !e.target.closest('#login-btn')) {
            loginForm.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    // Close login on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape" && loginForm && loginForm.classList.contains('active')) {
            loginForm.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    // ===================== Header Scroll Effect =====================
    window.addEventListener('scroll', () => {
        // Close mobile menu on scroll
        if (menuBtn && navbar && navbar.classList.contains('active')) {
            menuBtn.classList.remove('fa-times');
            navbar.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        // Sticky header
        if (header) {
            header.classList.toggle('active', window.scrollY > 50);
        }
        
        // Scroll to top button
        if (scrollTopBtn) {
            scrollTopBtn.classList.toggle('active', window.scrollY > 300);
        }
    });

    // ===================== Parallax Effect =====================
    const isTouchDevice = () => ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);

    if (homeSection && !isTouchDevice()) {
        homeSection.addEventListener('mousemove', (e) => {
            const parallaxElements = homeSection.querySelectorAll('.home-parallax');
            const x = e.clientX / window.innerWidth;
            const y = e.clientY / window.innerHeight;
            
            parallaxElements.forEach((elm, index) => {
                const speed = parseFloat(elm.dataset.speed) || 0.5;
                const xPos = (x * speed * 100) - (speed * 50);
                const yPos = (y * speed * 100) - (speed * 50);
                
                elm.style.transform = `translate(${xPos}px, ${yPos}px)`;
            });
        });

        homeSection.addEventListener('mouseleave', () => {
            const parallaxElements = homeSection.querySelectorAll('.home-parallax');
            parallaxElements.forEach(elm => {
                elm.style.transform = `translate(0, 0)`;
                elm.style.transition = 'transform 0.5s ease-out';
            });
            
            setTimeout(() => {
                parallaxElements.forEach(elm => {
                    elm.style.transition = '';
                });
            }, 500);
        });
    }

    // ===================== Swiper Sliders =====================
    function initSwiper(selector, config = {}) {
        try {
            const element = document.querySelector(selector);
            if (element) {
                return new Swiper(selector, {
                    grabCursor: true,
                    centeredSlides: true,
                    spaceBetween: 20,
                    loop: true,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true
                    },
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true,
                        dynamicBullets: true
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    breakpoints: {
                        0: {
                            slidesPerView: 1,
                            spaceBetween: 10
                        },
                        640: {
                            slidesPerView: 2,
                            spaceBetween: 15
                        },
                        1024: {
                            slidesPerView: 3,
                            spaceBetween: 20
                        },
                        1200: {
                            slidesPerView: 4,
                            spaceBetween: 25
                        }
                    },
                    on: {
                        init: function () {
                            // Add loaded class to images
                            this.slides.forEach(slide => {
                                const img = slide.querySelector('img');
                                if (img && !img.complete) {
                                    img.addEventListener('load', () => {
                                        img.classList.add('loaded');
                                    });
                                } else if (img) {
                                    img.classList.add('loaded');
                                }
                            });
                        }
                    },
                    ...config
                });
            }
        } catch (err) {
            console.error(`Swiper init error for ${selector}:`, err);
        }
        return null;
    }

    // Initialize all swipers
    const vehiclesSwiper = initSwiper(".vehicles-slider", {
        slidesPerView: 1,
        spaceBetween: 20,
        autoplay: { delay: 3000 }
    });

    const featuredSwiper = initSwiper(".featured-slider", {
        slidesPerView: 1,
        spaceBetween: 20,
        autoplay: { delay: 4000 }
    });

    const reviewSwiper = initSwiper(".review-slider", {
        slidesPerView: 1,
        spaceBetween: 20,
        autoplay: { delay: 3500 },
        effect: 'coverflow',
        coverflowEffect: {
            rotate: 50,
            stretch: 0,
            depth: 100,
            modifier: 1,
            slideShadows: true,
        }
    });

    // ===================== Intersection Observer for Animations =====================
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                
                // Add animation delay for child elements
                const animatedElements = entry.target.querySelectorAll('.box, .icons, .btn');
                animatedElements.forEach((el, index) => {
                    el.style.animationDelay = `${index * 0.1}s`;
                    el.classList.add('animated');
                });
            }
        });
    }, observerOptions);

    // Observe all sections
    document.querySelectorAll('section').forEach(section => {
        observer.observe(section);
    });

    // ===================== Smooth Scroll =====================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const target = document.querySelector(targetId);
            if (target) {
                // Close mobile menu if open
                if (menuBtn && navbar.classList.contains('active')) {
                    menuBtn.classList.remove('fa-times');
                    navbar.classList.remove('active');
                    document.body.style.overflow = '';
                }
                
                // Smooth scroll to target
                window.scrollTo({
                    top: target.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });

    // ===================== Form Validation =====================
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                
                // Add validation styles
                const inputs = this.querySelectorAll('input[required], textarea[required]');
                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.classList.add('invalid');
                        input.addEventListener('input', () => {
                            if (input.value.trim()) {
                                input.classList.remove('invalid');
                            }
                        }, { once: true });
                    }
                });
            }
        });
    });

    // ===================== Newsletter Form =====================
    const newsletterForm = document.querySelector('.newsletter form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            if (email) {
                // Simulate submission
                const submitBtn = this.querySelector('input[type="submit"]');
                const originalText = submitBtn.value;
                submitBtn.value = 'Subscribing...';
                submitBtn.disabled = true;
                
                setTimeout(() => {
                    alert('Thank you for subscribing to our newsletter!');
                    this.reset();
                    submitBtn.value = originalText;
                    submitBtn.disabled = false;
                }, 1500);
            }
        });
    }

    // ===================== Scroll to Top =====================
    if (scrollTopBtn) {
        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // ===================== Lazy Loading Images =====================
    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.src; // Trigger load if not already loaded
                    imageObserver.unobserve(img);
                }
            });
        });

        lazyImages.forEach(img => imageObserver.observe(img));
    }

    // ===================== Keyboard Navigation =====================
    document.addEventListener('keydown', (e) => {
        // Navigate sliders with arrow keys
        if (e.target.closest('.swiper-slide')) {
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
        }
    });

    // ===================== Initialize AOS (if available) =====================
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
    }

    // ===================== Performance Optimizations =====================
    // Debounce resize events
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            // Update swipers on resize
            vehiclesSwiper?.update();
            featuredSwiper?.update();
            reviewSwiper?.update();
        }, 250);
    });

    console.log("All scripts loaded successfully");
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animated {
        animation: fadeInUp 0.6s ease forwards;
    }
    
    .box:hover {
        transform: translateY(-5px) !important;
        transition: transform 0.3s ease !important;
    }
    
    .invalid {
        border-color: #ff4757 !important;
        box-shadow: 0 0 5px rgba(255, 71, 87, 0.3) !important;
    }
    
    .btn:hover {
        transform: translateY(-3px) !important;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2) !important;
    }
    
    .swiper-slide {
        transition: all 0.3s ease;
    }
    
    .swiper-slide:hover {
        z-index: 10;
    }
`;
document.head.appendChild(style);