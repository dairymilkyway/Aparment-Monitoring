<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
include "../includes/db.php";

// Fetch tenant information
$query = "SELECT * FROM tenants";
$stmt = $conn->prepare($query);
$stmt->execute();
$tenants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch rent payment information
$query = "SELECT * FROM payments";
$stmt = $conn->prepare($query);
$stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch maintenance requests
$query = "SELECT * FROM maintenance_requests";
$stmt = $conn->prepare($query);
$stmt->execute();
$maintenance_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user information
$query = "SELECT * FROM users";
$stmt = $conn->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch room information
$query = "SELECT * FROM rooms";
$stmt = $conn->prepare($query);
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <script>
        function showSection(sectionId) {
            // Hide all sections
            document.getElementById('tenants-section').style.display = 'none';
            document.getElementById('payments-section').style.display = 'none';
            document.getElementById('maintenance-section').style.display = 'none';
            document.getElementById('users-section').style.display = 'none';
            document.getElementById('rooms-section').style.display = 'none';
            
            // Show the selected section
            document.getElementById(sectionId).style.display = 'block';
        }
        
        // Show the tenants section by default
        window.onload = function() {
            showSection('tenants-section');
        }
    </script>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <h2>Admin Dashboard</h2>
            <ul>
                <li><a href="#" onclick="showSection('tenants-section')">Manage Tenants</a></li>
                <li><a href="#" onclick="showSection('payments-section')">Track Rent Payments</a></li>
                <li><a href="#" onclick="showSection('maintenance-section')">Handle Maintenance Requests</a></li>
                <li><a href="#" onclick="showSection('users-section')">Manage Users</a></li>
                <li><a href="#" onclick="showSection('rooms-section')">Manage Rooms</a></li>
            </ul>
        </div>
        <div class="content">
            <!-- Include Tenants Section -->
            <?php include "tenants.php"; ?>
            
            <!-- Include Payments Section -->
            <?php include "payments.php"; ?>
            
            <!-- Include Maintenance Section -->
            <?php include "maintenance.php"; ?>
            
            <!-- Include Users Section -->
            <?php include "users.php"; ?>
            
            <!-- Include Rooms Section -->
            <?php include "rooms.php"; ?>
        </div>
    </div>
</body>
</html>