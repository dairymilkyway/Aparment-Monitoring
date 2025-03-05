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
    $query = "SELECT r.room_number FROM rooms r 
              JOIN room_requests rr ON r.id = rr.room_id 
              WHERE rr.user_id = ? AND rr.status = 'approved'";
    $stmt = $conn->prepare($query);
    $stmt->execute([$user_id]);
    $rented_room = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<link rel="stylesheet" href="<?php echo $root_path; ?>css/navbar.css">

<nav class="navbar">
    <a href="<?php echo $root_path; ?>index.php" class="navbar-brand">
        <span>🏠 Apartment Monitoring System</span>
    </a>
    <div class="navbar-links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Logged-in user -->
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['role']); ?>!</span>
            <?php if ($rented_room): ?>
                <a href="<?php echo $root_path; ?>user/dashboard.php">Dashboard</a>
                <a href="<?php echo $root_path; ?>user/maintenance.php">Submit Maintenance Request</a>
            <?php else: ?>
                <a href="<?php echo $root_path; ?>user/dashboard.php">Request Room</a>
            <?php endif; ?>
            <a href="<?php echo $root_path; ?>logout.php" class="logout-btn">Logout</a>
        <?php else: ?>
            <!-- Guest -->
            <a href="<?php echo $root_path; ?>login.php">Login</a>
            <a href="<?php echo $root_path; ?>register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>