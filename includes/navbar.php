<?php
// Determine the path to the root directory
$root_path = "";
if(strpos($_SERVER['PHP_SELF'], '/user/') !== false) {
    $root_path = "../";
} elseif(strpos($_SERVER['PHP_SELF'], '/admin/') !== false) {
    $root_path = "../../";
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
            <a href="<?php echo $root_path; ?>logout.php" class="logout-btn">Logout</a>
        <?php else: ?>
            <!-- Guest -->
            <a href="<?php echo $root_path; ?>login.php">Login</a>
            <a href="<?php echo $root_path; ?>register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>