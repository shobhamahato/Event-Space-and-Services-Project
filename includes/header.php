<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- use isset to start session-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EventSpace | Event Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
            padding: 15px 0;

        }

        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 600;
            color: #fff !important;
            
        }

        .navbar-brand i {
            color: #1cc88a;
        }

        .nav-link {
            color: #fff !important;
            font-weight: 500;
            margin-left: 15px;
            transition: 0.3s;
        }

        .nav-link:hover {
            color: #1cc88a !important;
        }

        .btn-register {
            background-color: #1cc88a;
            color: white !important;
            padding: 8px 18px;
            border-radius: 30px;
            font-weight: 600;
            margin-left: 15px;
        }

        .btn-register:hover {
            background-color: #17a673;
        }

        /* Main Dropdown */
        .dropdown-menu {
            width: 150px; /* reduced width */
            border-radius: 12px;
            padding: 4px 0;
            border: none;
            background: linear-gradient(135deg, #203a43, #2c5364);
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        }

        .dropdown-item {
            font-size: 14px;
            padding: 8px 15px;
            color: #ffffff;
            transition: 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: #1cc88a;
            color: #ffffff;
        }

        /* Submenu */
        .dropdown-submenu {
            position: relative;
        }

        /* .vendor-submenu {
            display: none;
            position: absolute;
            top: 0;
            left: 100%;
            width: 210px; 
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #1f2f36, #294654);
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        } */
.vendor-submenu {
    display: none;
    position: absolute;
    top: 0;
    left: 100%;
    min-width: 210px;
}

.dropdown-submenu:hover .vendor-submenu {
    display: block;
}

/* Mobile Fix */
@media (max-width: 991px) {
    .vendor-submenu {
        position: static;
        display: block;
        margin-left: 15px;
    }
}
        .dropdown-submenu:hover .vendor-submenu {
            display: block;
        }

        /* Remove extra arrow */
        .dropdown-submenu > a::after {
            content: "";
        }

    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container px-4">

        <a class="navbar-brand" href="/EventProject/index.php">
            <i class="fas fa-calendar-check p-3"></i> EventSpace
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">

                <li class="nav-item">
                    <a class="nav-link" href="/EventProject/index.php">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/EventProject/services.php">Services</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/EventProject/about.php">About</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/EventProject/contact.php">Contact</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="./auth/login.php">Login</a>
                </li>

                <!-- Register Dropdown -->
                <li class="nav-item dropdown">
                    <a class="btn btn-register dropdown-toggle"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        Register
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">

                        <li>
                            <a class="dropdown-item" href="/EventProject/auth/user_register.php">
                                User
                            </a>
                        </li>

                        <li class="dropdown-submenu">
                            <a class="dropdown-item" href="#">
                                Vendor
                            </a>

                            <ul class="dropdown-menu vendor-submenu">
                                <li><a class="dropdown-item" href="/EventProject/auth/vendor/decorator_register.php">Decorators</a></li>
                                <li><a class="dropdown-item" href="/EventProject/auth/vendor/caterer_register.php">Caterers</a></li>
                                <li><a class="dropdown-item" href="/EventProject/auth/vendor/venue_register.php">Venues</a></li>
                                <li><a class="dropdown-item" href="/EventProject/auth/vendor/music_register.php">Music & DJ</a></li>
                                <li><a class="dropdown-item" href="/EventProject/auth/vendor/photo_register.php">Photography</a></li>
                                <li><a class="dropdown-item" href="/EventProject/auth/vendor/beauty_register.php">Beauty & Parlour</a></li>
                                <li><a class="dropdown-item" href="/EventProject/auth/vendor/card_register.php">Cards</a></li>
                            </ul>
                        </li>

                    </ul>
                </li>

            </ul>
        </div>
    </div>
</nav>