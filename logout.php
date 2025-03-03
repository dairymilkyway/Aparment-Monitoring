<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: /apartment-monitoring/index.php");
exit();
?>