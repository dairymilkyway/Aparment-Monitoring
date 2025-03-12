<?php
include "db.php";

// Determine the path to the root directory
$root_path = "";
if (strpos($_SERVER['PHP_SELF'], '/user/') !== false) {
    $root_path = "../";
}

// Check if the user has a rented room
$rented_room = false;
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'user') {
    $user_id = $_SESSION['user_id'];
    
    $query = "SELECT r.* FROM rooms r 
              JOIN room_requests rr ON r.id = rr.room_id
              JOIN users u ON rr.user_id = u.id 
              WHERE rr.user_id = ?  AND rr.status = 'approved'";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([$user_id]);
    $rented_rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Determine current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="<?php echo $root_path; ?>css/navbar.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<nav class="navbar">
    <a href="<?php echo $root_path; ?>index.php" class="navbar-brand">
        <span><i class="fas fa-building"></i> Apartment Monitoring</span>
    </a>

    <div class="navbar-links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Logged-in user -->
            <span>
                <i class="fas fa-user-circle"></i>
                <?php echo htmlspecialchars($_SESSION['role']); ?>
            </span>
            
            <a href="<?php echo $root_path; ?>user/dashboard.php" 
               class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
               <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            
            <a href="<?php echo $root_path; ?>user/request_room.php"
               class="<?php echo ($current_page == 'request_room.php') ? 'active' : ''; ?>">
               <i class="fas fa-key"></i> Rent Room
            </a>
            
            <a href="<?php echo $root_path; ?>user/maintenance_requests.php"
               class="<?php echo ($current_page == 'maintenance_requests.php') ? 'active' : ''; ?>">
               <i class="fas fa-wrench"></i> Maintenance
            </a>
            
            <a href="<?php echo $root_path; ?>logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        <?php else: ?>
            <!-- Guest -->
            <a href="<?php echo $root_path; ?>login.php"
               class="<?php echo ($current_page == 'login.php') ? 'active' : ''; ?>">
               <i class="fas fa-sign-in-alt"></i> Login
            </a>
            
            <a href="<?php echo $root_path; ?>register.php"
               class="<?php echo ($current_page == 'register.php') ? 'active' : ''; ?>">
               <i class="fas fa-user-plus"></i> Register
            </a>
        <?php endif; ?>
    </div>
</nav>