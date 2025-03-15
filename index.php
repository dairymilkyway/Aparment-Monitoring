<?php
session_start();
include "includes/db.php";

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect based on the user's role
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
        exit();
    } else {
        header("Location: user/dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartment Monitoring System</title>
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hero {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #0056b3, #00c6ff);
            padding: 100px 20px;
        }
        
        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') center/cover;
            opacity: 0.15;
            z-index: 0;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 900px;
            margin: 0 auto;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            margin-bottom: 25px;
        }
        
        .hero p {
            font-size: 1.5rem;
            margin-bottom: 40px;
            font-weight: 300;
        }
        
        .testimonial-section {
            background-color: #f9f9f9;
            padding: 60px 0;
        }
        
        .testimonial {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin: 20px 0;
            position: relative;
        }
        
        .testimonial::before {
            content: """;
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 5rem;
            color: #f0f0f0;
            z-index: 0;
        }
        
        .testimonial p {
            position: relative;
            z-index: 1;
        }
        
        .cta-section {
            background: linear-gradient(135deg, #007bff, #00c6ff);
            color: white;
            padding: 50px 0;
            text-align: center;
            margin-top: 20px;
        }
        
        .stats-row {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
            margin: 30px 0;
        }
        
        .stat-item {
            text-align: center;
            flex: 1;
            min-width: 200px;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>
<body>
    <?php include "includes/navbar.php"; ?>
    
    <div class="hero">
        <div class="hero-content">
            <h1><i class="fas fa-building"></i> Apartment Monitoring System</h1>
            <p>The easiest way to manage your apartment rentals and bookings</p>
            <div class="cta-buttons">
                <a href="login.php" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="register.php" class="btn btn-secondary"><i class="fas fa-user-plus"></i> Register</a>
            </div>
        </div>
    </div>

    <div class="features-section">
        <div class="container">
            <h2>Simplify Your Apartment Management</h2>
            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-number">100+</div>
                    <p>Apartments Managed</p>
                </div>
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <p>Happy Tenants</p>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <p>Support Access</p>
                </div>
            </div>
            <div class="features">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-home"></i></div>
                    <h3>Room Management</h3>
                    <p>Easily view available rooms and manage your bookings in real-time with our intuitive interface.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-calendar-alt"></i></div>
                    <h3>Booking Tracking</h3>
                    <p>Keep track of your due dates and occupancy period with automated reminders and notifications.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-user-shield"></i></div>
                    <h3>Secure Access</h3>
                    <p>Role-based access control ensures your data remains secure and private at all times.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="cta-section">
        <div class="container">
            <h2>Ready to get started?</h2>
            <p>Join hundreds of property managers who trust our system</p>
            <div class="cta-buttons">
                <a href="register.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Create Free Account</a>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Apartment Monitoring System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>