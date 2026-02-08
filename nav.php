<?php
// nav.php - Navigation Bar with Search
// Remove session_start() from here since it's already started elsewhere
// session_start(); // REMOVE THIS LINE

// Instead, check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Rental System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Navbar Styles */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 5%;
            height: 80px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .header.scrolled {
            height: 70px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.12);
        }

        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            gap: 12px;
        }

        .logo-icon {
            font-size: 2.2rem;
            animation: bounce 2s infinite;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1;
        }

        .logo-primary {
            font-size: 1.8rem;
            font-weight: 800;
            color: #2d3748;
            letter-spacing: 0.5px;
        }

        .logo-secondary {
            font-size: 2rem;
            font-weight: 900;
            color: #f9d806; /* Yellow theme */
            margin-left: 2px;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        /* Navigation */
        .navbar ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            color: #4a5568;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link i {
            font-size: 1.1rem;
            color: #718096;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: linear-gradient(135deg, rgba(249, 216, 6, 0.1) 0%, rgba(249, 216, 6, 0.1) 100%);
            color: #130f40;
            transform: translateY(-2px);
        }

        .nav-link:hover i {
            color: #f9d806;
            transform: scale(1.1);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 3px;
            background: linear-gradient(to right, #f9d806, #ffee80);
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .nav-link.active::after,
        .nav-link:hover::after {
            width: 60%;
        }

        /* SEARCH BAR IN NAVBAR */
        .navbar-search-container {
            position: relative;
            margin: 0 20px;
            flex: 1;
            max-width: 500px;
        }

        .navbar-search-form {
            position: relative;
        }

        .navbar-search-input {
            width: 100%;
            padding: 12px 50px 12px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 30px;
            background: #f8f9fa;
            color: #333;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            outline: none;
        }

        .navbar-search-input:focus {
            border-color: #f9d806;
            background: white;
            box-shadow: 0 0 0 3px rgba(249, 216, 6, 0.1);
            transform: scale(1.02);
        }

        .navbar-search-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: #f9d806;
            color: #130f40;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .navbar-search-btn:hover {
            background: #130f40;
            color: #f9d806;
            transform: translateY(-50%) scale(1.1);
        }

        /* Search Suggestions */
        .navbar-search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            max-height: 350px;
            overflow-y: auto;
            z-index: 1001;
            display: none;
            margin-top: 10px;
            border: 1px solid #e2e8f0;
        }

        .navbar-suggestion-item {
            padding: 12px 20px;
            cursor: pointer;
            border-bottom: 1px solid #f1f1f1;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-suggestion-item:hover {
            background: #fffde7;
            padding-left: 25px;
        }

        .navbar-suggestion-item:last-child {
            border-bottom: none;
        }

        .navbar-suggestion-name {
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        .navbar-suggestion-type {
            font-size: 0.8rem;
            color: #666;
            background: #f1f1f1;
            padding: 3px 10px;
            border-radius: 15px;
        }

        .navbar-suggestion-item:hover .navbar-suggestion-name {
            color: #f9d806;
        }

        /* Dropdown Menu */
        .nav-dropdown {
            position: relative;
        }

        .dropdown-toggle {
            position: relative;
            padding-right: 40px !important;
        }

        .dropdown-arrow {
            position: absolute;
            right: 15px;
            font-size: 0.8rem;
            transition: transform 0.3s ease;
        }

        .nav-dropdown:hover .dropdown-arrow {
            transform: rotate(180deg);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            min-width: 220px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1001;
            overflow: hidden;
        }

        .nav-dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu li {
            border-bottom: 1px solid #f1f1f1;
        }

        .dropdown-menu li:last-child {
            border-bottom: none;
        }

        .dropdown-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            color: #4a5568;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .dropdown-menu a:hover {
            background: #ffee80;
            color: #130f40;
            padding-left: 25px;
        }

        .dropdown-menu a i {
            width: 20px;
            text-align: center;
        }

        .divider {
            border-top: 1px solid #e2e8f0;
            margin: 8px 0;
        }

        .logout-btn {
            color: #e53e3e !important;
        }

        .logout-btn:hover {
            background: #fff5f5 !important;
        }

        /* Header Actions */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 28px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-login {
            background: transparent;
            color: #f9d806;
            border: 2px solid #f9d806;
        }

        .btn-login:hover {
            background: rgba(249, 216, 6, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(249, 216, 6, 0.2);
        }

        .btn-signup {
            background: linear-gradient(135deg, #f9d806 0%, #ffee80 100%);
            color: #130f40;
            border: none;
        }

        .btn-signup:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(249, 216, 6, 0.4);
        }

        .btn-text {
            transition: all 0.3s ease;
        }

        /* User Mini Profile */
        .user-mini-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: rgba(249, 216, 6, 0.1);
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-mini-profile:hover {
            background: rgba(249, 216, 6, 0.15);
            transform: translateY(-2px);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #f9d806, #ffee80);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #130f40;
            font-size: 1.2rem;
        }

        .user-name {
            font-weight: 500;
            color: #4a5568;
            font-size: 0.95rem;
        }

        /* Mobile Menu Toggle */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            padding: 10px;
            cursor: pointer;
            position: relative;
            width: 40px;
            height: 40px;
            z-index: 1002;
        }

        .menu-icon {
            display: block;
            width: 25px;
            height: 3px;
            background: #4a5568;
            margin: 5px 0;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        .menu-toggle.active .menu-icon:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px);
        }

        .menu-toggle.active .menu-icon:nth-child(2) {
            opacity: 0;
        }

        .menu-toggle.active .menu-icon:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .navbar-search-container {
                max-width: 400px;
            }
        }

        @media (max-width: 1024px) {
            .navbar ul {
                gap: 4px;
            }
            
            .nav-link {
                padding: 10px 16px;
                font-size: 0.95rem;
            }
            
            .navbar-search-container {
                max-width: 350px;
                margin: 0 15px;
            }
        }

        @media (max-width: 900px) {
            .navbar-search-container {
                max-width: 300px;
                margin: 0 10px;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 0 20px;
                height: 70px;
            }

            .navbar {
                position: fixed;
                top: 70px;
                left: 0;
                right: 0;
                background: white;
                transform: translateY(-100%);
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                padding: 20px;
                z-index: 999;
            }

            .navbar.active {
                transform: translateY(0);
                opacity: 1;
                visibility: visible;
            }

            .navbar ul {
                flex-direction: column;
                gap: 10px;
            }

            .nav-link {
                padding: 15px 20px;
                justify-content: flex-start;
                border-radius: 10px;
            }

            .nav-link:hover {
                transform: translateX(5px);
            }

            /* Search bar in mobile menu */
            .navbar-search-container {
                max-width: 100%;
                margin: 0 0 20px 0;
                order: -1;
            }

            .dropdown-menu {
                position: static;
                box-shadow: none;
                background: #fffde7;
                margin-top: 10px;
                opacity: 1;
                visibility: visible;
                transform: none;
            }

            .nav-dropdown:hover .dropdown-menu {
                transform: none;
            }

            .menu-toggle {
                display: block;
            }

            .btn .btn-text {
                display: none;
            }

            .btn {
                padding: 12px;
            }

            .btn i {
                margin: 0;
            }

            .user-mini-profile .user-name {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .logo-text {
                display: none;
            }

            .header {
                padding: 0 15px;
            }
            
            .navbar-search-input {
                padding: 10px 45px 10px 15px;
                font-size: 0.9rem;
            }
            
            .navbar-search-btn {
                width: 32px;
                height: 32px;
            }
        }
    </style>
</head>
<body>

<header class="header">

    <!-- Logo -->
    <a href="homepage.php" class="logo">
        <span class="logo-icon">🚗</span>
        <span class="logo-text">
            <span class="logo-primary">Travel</span><span class="logo-secondary">X</span>
        </span>
    </a>

    <!-- Navigation -->
    <nav class="navbar">
        <ul>
            <li>
                <a href="homepage.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
            </li>
            <li>
                <a href="vehicles.php" class="nav-link">
                    <i class="fas fa-car"></i>
                    <span>Vehicles</span>
                </a>
            </li>
            <li>
                <a href="about us.php" class="nav-link">
                    <i class="fas fa-info-circle"></i>
                    <span>About Us</span>
                </a>
            </li>
            <li>
                <a href="contact.php" class="nav-link">
                    <i class="fas fa-phone-alt"></i>
                    <span>Contact</span>
                </a>
            </li>
        </ul>
        
        <!-- SEARCH BAR - Added between navigation and user menu -->
        <div class="navbar-search-container">
            <form method="GET" action="search.php" class="navbar-search-form" id="navbarSearchForm">
                <input type="text" 
                       name="q"
                       id="navbarSearchInput" 
                       class="navbar-search-input" 
                       placeholder="Search vehicles (e.g., Toyota, SUV, Truck)..."
                       autocomplete="off"
                       aria-label="Search vehicles">
                <button type="submit" class="navbar-search-btn" id="navbarSearchBtn" aria-label="Search">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            
            <!-- Search Suggestions Dropdown -->
            <div class="navbar-search-suggestions" id="navbarSearchSuggestions"></div>
        </div>
        
        <ul>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li class="nav-dropdown">
                    <a href="#" class="nav-link dropdown-toggle">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo htmlspecialchars($_SESSION['username'] ?? 'Account'); ?></span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                        <li><a href="bookings.php"><i class="fas fa-calendar-alt"></i> My Bookings</a></li>
                        <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                        <li class="divider"></li>
                        <li><a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Auth Buttons / User Menu -->
    <div class="header-actions">
        <?php if(!isset($_SESSION['user_id'])): ?>
            <!-- Login Button -->
            <button id="login-btn" class="btn btn-login" aria-label="Login">
                <i class="fas fa-sign-in-alt"></i>
                <span class="btn-text">Login</span>
            </button>
            
            <a href="signup.php" class="btn btn-signup" aria-label="Sign Up">
                <i class="fas fa-user-plus"></i>
                <span class="btn-text">Sign Up</span>
            </a>
        <?php else: ?>
            <div class="user-mini-profile">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
            </div>
        <?php endif; ?>
        
        <!-- Mobile Menu Button -->
        <button id="menu-btn" class="menu-toggle" aria-label="Toggle Menu" aria-expanded="false">
            <span class="menu-icon"></span>
            <span class="menu-icon"></span>
            <span class="menu-icon"></span>
        </button>
    </div>

</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile Menu Toggle
        const menuBtn = document.getElementById('menu-btn');
        const navbar = document.querySelector('.navbar');
        const body = document.body;

        if (menuBtn) {
            menuBtn.addEventListener('click', function() {
                this.classList.toggle('active');
                navbar.classList.toggle('active');
                body.style.overflow = navbar.classList.contains('active') ? 'hidden' : '';
                // Hide search suggestions when closing menu
                if (!navbar.classList.contains('active')) {
                    hideSearchSuggestions();
                }
            });
        }

        // Close menu when clicking on a link
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (menuBtn) menuBtn.classList.remove('active');
                if (navbar) navbar.classList.remove('active');
                body.style.overflow = '';
                hideSearchSuggestions();
            });
        });

        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header');
            if (header) {
                if (window.scrollY > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            }
        });

        // ============ SEARCH FUNCTIONALITY ============
        const searchInput = document.getElementById('navbarSearchInput');
        const searchForm = document.getElementById('navbarSearchForm');
        const searchSuggestions = document.getElementById('navbarSearchSuggestions');
        
        if (searchInput && searchForm && searchSuggestions) {
            // Common search suggestions
            const commonSearches = [
                {name: 'Toyota', type: 'Brand'},
                {name: 'Honda', type: 'Brand'},
                {name: 'Ford', type: 'Brand'},
                {name: 'SUV', type: 'Type'},
                {name: 'Sedan', type: 'Type'},
                {name: 'Truck', type: 'Type'},
                {name: 'Bus', type: 'Type'},
                {name: 'Van', type: 'Type'},
                {name: 'Motorcycle', type: 'Type'},
                {name: 'Electric', type: 'Fuel'},
                {name: 'Diesel', type: 'Fuel'},
                {name: 'Automatic', type: 'Transmission'},
                {name: 'Manual', type: 'Transmission'},
                {name: 'Luxury', type: 'Category'},
                {name: 'Budget', type: 'Category'}
            ];

            // Spelling corrections
            const spellingCorrections = {
                'truk': 'truck',
                'seden': 'sedan',
                'toyotta': 'toyota',
                'hnda': 'honda',
                'fordd': 'ford',
                'bmvv': 'bmw',
                'mersedes': 'mercedes',
                'automatik': 'automatic',
                'manul': 'manual',
                'elecric': 'electric',
                'diseal': 'diesel',
                'patrol': 'petrol',
                'scootr': 'scooter',
                'bajjaj': 'bajaj',
                'mahindr': 'mahindra'
            };

            // Show suggestions on focus
            searchInput.addEventListener('focus', function() {
                showCommonSuggestions();
            });

            // Handle input for suggestions
            searchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                if (query.length === 0) {
                    showCommonSuggestions();
                } else {
                    showFilteredSuggestions(query);
                }
            });

            // Handle form submission
            searchForm.addEventListener('submit', function(e) {
                const query = searchInput.value.trim();
                if (query.length === 0) {
                    e.preventDefault();
                    searchInput.focus();
                }
                hideSearchSuggestions();
            });

            // Hide suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.navbar-search-container')) {
                    hideSearchSuggestions();
                }
            });

            // Show common suggestions
            function showCommonSuggestions() {
                searchSuggestions.innerHTML = '';
                commonSearches.forEach(item => {
                    const suggestionItem = createSuggestionItem(item);
                    searchSuggestions.appendChild(suggestionItem);
                });
                searchSuggestions.style.display = 'block';
            }

            // Show filtered suggestions
            function showFilteredSuggestions(query) {
                // Check for spelling corrections
                let correctedQuery = query;
                if (spellingCorrections[query]) {
                    correctedQuery = spellingCorrections[query];
                }

                // Filter suggestions
                const filtered = commonSearches.filter(item => 
                    item.name.toLowerCase().includes(correctedQuery) || 
                    item.type.toLowerCase().includes(correctedQuery)
                );

                // Add spelling correction if needed
                if (spellingCorrections[query] && spellingCorrections[query] !== query) {
                    filtered.unshift({
                        name: `Did you mean "${spellingCorrections[query]}"?`,
                        type: 'Suggestion',
                        isCorrection: true,
                        correction: spellingCorrections[query]
                    });
                }

                // Show suggestions
                searchSuggestions.innerHTML = '';
                if (filtered.length > 0) {
                    filtered.forEach(item => {
                        const suggestionItem = createSuggestionItem(item);
                        searchSuggestions.appendChild(suggestionItem);
                    });
                    searchSuggestions.style.display = 'block';
                } else {
                    hideSearchSuggestions();
                }
            }

            // Create suggestion item
            function createSuggestionItem(item) {
                const div = document.createElement('div');
                div.className = 'navbar-suggestion-item';
                
                if (item.isCorrection) {
                    div.innerHTML = `<span class="navbar-suggestion-name">${item.name}</span>`;
                    div.addEventListener('click', function() {
                        searchInput.value = item.correction;
                        searchInput.focus();
                        hideSearchSuggestions();
                    });
                } else {
                    div.innerHTML = `
                        <span class="navbar-suggestion-name">${item.name}</span>
                        <span class="navbar-suggestion-type">${item.type}</span>
                    `;
                    div.addEventListener('click', function() {
                        searchInput.value = item.name;
                        searchForm.submit();
                    });
                }
                
                return div;
            }

            // Hide suggestions
            function hideSearchSuggestions() {
                searchSuggestions.style.display = 'none';
            }

            // Handle keyboard navigation
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    hideSearchSuggestions();
                } else if (e.key === 'Enter' && searchSuggestions.style.display === 'block') {
                    const firstSuggestion = searchSuggestions.querySelector('.navbar-suggestion-item');
                    if (firstSuggestion) {
                        e.preventDefault();
                        firstSuggestion.click();
                    }
                }
            });
        }
        // ============ END SEARCH FUNCTIONALITY ============
    });
</script>

</body>
</html>