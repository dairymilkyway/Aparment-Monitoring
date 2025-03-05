<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
include "../includes/db.php";
include "fetch.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.section').forEach(section => {
                section.style.display = 'none';
            });
            
            // Show the selected section
            document.getElementById(sectionId).style.display = 'block';
        }
        
        function toggleDropdown() {
            var dropdownContent = document.getElementById("dropdown-content");
            if (dropdownContent.style.display === "block") {
                dropdownContent.style.display = "none";
            } else {
                dropdownContent.style.display = "block";
            }
        }
        
        // Show the tenants section by default
        window.onload = function() {
            showSection('tenants-section');
            
            // Show the rooms section if there's a success or error message
            <?php if (isset($_SESSION['success_message']) || isset($_SESSION['error_message'])): ?>
                showSection('rooms-section');
            <?php endif; ?>
        }
    </script>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <h2>Admin Dashboard</h2>
            <ul>
                <li><a href="#" onclick="showSection('tenants-section')">Tenants</a></li>
                <li><a href="#" onclick="showSection('payments-section')">Rent Payments</a></li>
                <li><a href="#" onclick="showSection('maintenance-section')">Maintenance Requests</a></li>
                <li>
                    <a href="#" onclick="toggleDropdown()">Manage Rooms</a>
                    <ul id="dropdown-content" style="display: none;">
                        <li><a href="#" onclick="showSection('rooms-section')">Rooms</a></li>
                        <li><a href="#" onclick="showSection('room-requests-section')">Requests</a></li>
                    </ul>
                </li>
                <li><a href="#" onclick="showSection('users-section')">Manage Users</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="content">
            <!-- Flash Messages -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="flash-message success">
                    <?php 
                        echo $_SESSION['success_message']; 
                        unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="flash-message error">
                    <?php 
                        echo $_SESSION['error_message']; 
                        unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>
            
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
            
            <!-- Include Room Requests Section -->
            <?php include "room_requests.php"; ?>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('table').DataTable();
        });
    </script>
</body>
</html>