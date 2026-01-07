document.addEventListener("DOMContentLoaded", () => {

    // ================= Elements =================
    const menu = document.querySelector('#menu-btn');
    const navbar = document.querySelector('.navbar');
    const header = document.querySelector('.header');
    const loginForm = document.querySelector('.login-form-container');
    const loginBtn = document.querySelector('#login-btn');
    const closeLoginBtn = document.querySelector('#close-login-form');

    // ================= Menu Toggle =================
    if(menu && navbar){
        menu.addEventListener('click', () => {
            menu.classList.toggle('fa-times');
            navbar.classList.toggle('active');
        });
    }

    // ================= Login Form Toggle =================
    if(loginBtn && loginForm){
        loginBtn.addEventListener('click', () => {
            loginForm.classList.add('active'); // always open
        });
    }

    if(closeLoginBtn && loginForm){
        closeLoginBtn.addEventListener('click', () => {
            loginForm.classList.remove('active');
        });
    }

    // Optional: close login form when clicking outside
    document.addEventListener('click', (e) => {
        if(loginForm && loginForm.classList.contains('active') && 
           !e.target.closest('.login-form-container') && 
           !e.target.closest('#login-btn')) {
            loginForm.classList.remove('active');
        }
    });

    // Optional: close login form on Escape key
    document.addEventListener('keydown', (e) => {
        if(e.key === "Escape" && loginForm && loginForm.classList.contains('active')){
            loginForm.classList.remove('active');
        }
    });

    // ================= Header Scroll Effect =================
    window.addEventListener('scroll', () => {
        if(menu && navbar){
            menu.classList.remove('fa-times');
            navbar.classList.remove('active');
        }
        if(header){
            header.classList.toggle('active', window.scrollY > 0);
        }
    });

    // ================= Read More / Read Less Functionality =================
    function toggleContent(dotsId, moreTextId, btnId){
        const dots = document.getElementById(dotsId);
        const moreText = document.getElementById(moreTextId);
        const btnText = document.getElementById(btnId);

        if(!dots || !moreText || !btnText){
            console.error(`Elements not found: ${dotsId}, ${moreTextId}, ${btnId}`);
            return;
        }

        const isExpanded = dots.style.display === "none";

        dots.style.display = isExpanded ? "inline" : "none";
        moreText.style.display = isExpanded ? "none" : "inline";
        btnText.innerHTML = isExpanded ? "Read more" : "Read less";
    }

    // Specific section functions
    window.company = () => toggleContent('dots', 'more', 'myBtn');
    window.mission = () => toggleContent('dots2', 'more2', 'myBtn2');
    window.clients = () => toggleContent('dots3', 'more3', 'myBtn3');

    // ================= Smooth Scroll =================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e){
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if(target){
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

});
