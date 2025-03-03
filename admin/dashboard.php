<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
}

// Query to fetch renter details
$query_renters = "SELECT r.room_number, rt.name AS renter_name, rt.due_date, rt.date_occupied, rt.number_of_pax 
                  FROM rooms r
                  LEFT JOIN renters rt ON r.id = rt.room_id";
$result_renters = $conn->query($query_renters);

// Query to fetch room details
$query_rooms = "SELECT id, room_number, status, created_at FROM rooms";
$result_rooms = $conn->query($query_rooms);

// Get statistics for dashboard
$total_rooms_query = "SELECT COUNT(*) as total FROM rooms";
$occupied_rooms_query = "SELECT COUNT(*) as total FROM rooms WHERE status = 'occupied'";
$available_rooms_query = "SELECT COUNT(*) as total FROM rooms WHERE status = 'available'";
$total_renters_query = "SELECT COUNT(*) as total FROM renters";

$total_rooms = $conn->query($total_rooms_query)->fetch(PDO::FETCH_ASSOC)['total'];
$occupied_rooms = $conn->query($occupied_rooms_query)->fetch(PDO::FETCH_ASSOC)['total'];
$available_rooms = $conn->query($available_rooms_query)->fetch(PDO::FETCH_ASSOC)['total'];
$total_renters = $conn->query($total_renters_query)->fetch(PDO::FETCH_ASSOC)['total'];

// Calculate occupancy rate
$occupancy_rate = $total_rooms > 0 ? round(($occupied_rooms / $total_rooms) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Apartment Monitoring</title>
    <link rel="stylesheet" href="../css/admindashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-dashboard">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-building"></i> Admin Panel</h2>
            </div>
            
            <div class="admin-profile">
                <div class="admin-avatar">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="admin-info">
                    <p>Welcome,</p>
                    <h3>Administrator</h3>
                </div>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item active">
                    <a href="#" id="dashboard-btn">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" id="renter-details-btn">
                        <i class="fas fa-users"></i>
                        <span>Renter Details</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" id="room-details-btn">
                        <i class="fas fa-door-open"></i>
                        <span>Room Details</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <p>&copy; <?php echo date('Y'); ?> Apartment Monitoring</p>
            </div>
        </div>
        
        <div class="content">
            <div class="top-bar">
                <div class="hamburger-menu">
                    <i class="fas fa-bars"></i>
                </div>
                <div class="page-title">
                    <h1>Admin Dashboard</h1>
                </div>
                <div class="top-bar-icons">
                    <a href="#" class="icon-link"><i class="fas fa-bell"></i></a>
                    <a href="#" class="icon-link"><i class="fas fa-cog"></i></a>
                </div>
            </div>
            
            <!-- Dashboard Overview -->
            <section id="dashboard-overview" class="details-section">
                <div class="stat-cards">
                    <div class="stat-card">
                        <div class="stat-card-icon blue">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="stat-card-info">
                            <h3>Total Rooms</h3>
                            <p class="stat-number"><?php echo $total_rooms; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card-icon green">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-card-info">
                            <h3>Available Rooms</h3>
                            <p class="stat-number"><?php echo $available_rooms; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card-icon orange">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div class="stat-card-info">
                            <h3>Occupied Rooms</h3>
                            <p class="stat-number"><?php echo $occupied_rooms; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card-icon purple">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-card-info">
                            <h3>Total Renters</h3>
                            <p class="stat-number"><?php echo $total_renters; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="dashboard-row">
                    <div class="dashboard-card large-card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-pie"></i> Occupancy Rate</h3>
                        </div>
                        <div class="card-body">
                            <div class="occupancy-chart">
                                <div class="progress-ring-container">
                                    <div class="progress-ring">
                                        <div class="progress-circle" style="--percentage: <?php echo $occupancy_rate; ?>;">
                                            <div class="progress-circle-inner">
                                                <span class="percentage"><?php echo $occupancy_rate; ?>%</span>
                                                <span class="label">Occupied</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-calendar"></i> Recent Activity</h3>
                        </div>
                        <div class="card-body">
                            <ul class="activity-list">
                                <li>
                                    <i class="fas fa-user-plus activity-icon"></i>
                                    <div class="activity-content">
                                        <p class="activity-text">New renter assigned to Room 101</p>
                                        <p class="activity-time">Today, 10:30 AM</p>
                                    </div>
                                </li>
                                <li>
                                    <i class="fas fa-door-open activity-icon"></i>
                                    <div class="activity-content">
                                        <p class="activity-text">Room 203 is now available</p>
                                        <p class="activity-time">Yesterday, 3:15 PM</p>
                                    </div>
                                </li>
                                <li>
                                    <i class="fas fa-user-check activity-icon"></i>
                                    <div class="activity-content">
                                        <p class="activity-text">Payment received for Room 305</p>
                                        <p class="activity-time">Mar 2, 2025</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <section id="renter-details" class="details-section" style="display:none;">
                <div class="dashboard-card full-width">
                    <div class="card-header">
                        <h3><i class="fas fa-users"></i> Renter Details</h3>
                        <div class="card-options">
                            <button class="btn-card-option"><i class="fas fa-filter"></i> Filter</button>
                            <button class="btn-card-option"><i class="fas fa-download"></i> Export</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Room Number</th>
                                        <th>Renter Name</th>
                                        <th>Due Date</th>
                                        <th>Date Occupied</th>
                                        <th>Number of Pax</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result_renters->fetch(PDO::FETCH_ASSOC)): 
                                        if($row['renter_name'] == null) continue; // Skip empty renters
                                    ?>
                                    <tr>
                                        <td><span class="room-badge"><?php echo htmlspecialchars($row['room_number']); ?></span></td>
                                        <td><?php echo htmlspecialchars($row['renter_name']); ?></td>
                                        <td><span class="date-cell"><?php echo htmlspecialchars($row['due_date']); ?></span></td>
                                        <td><span class="date-cell"><?php echo htmlspecialchars($row['date_occupied']); ?></span></td>
                                        <td><?php echo htmlspecialchars($row['number_of_pax']); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-action edit"><i class="fas fa-edit"></i></button>
                                                <button class="btn-action delete"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <section id="room-details" class="details-section" style="display:none;">
                <div class="dashboard-card full-width">
                    <div class="card-header">
                        <h3><i class="fas fa-door-open"></i> Room Details</h3>
                        <div class="card-options">
                            <button class="btn-card-option"><i class="fas fa-plus"></i> Add Room</button>
                            <button class="btn-card-option"><i class="fas fa-download"></i> Export</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Room Number</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result_rooms->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><span class="room-badge"><?php echo htmlspecialchars($row['room_number']); ?></span></td>
                                        <td>
                                            <span class="status-badge <?php echo strtolower($row['status']); ?>">
                                                <?php echo htmlspecialchars($row['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-action edit"><i class="fas fa-edit"></i></button>
                                                <button class="btn-action delete"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Navigation functionality
            const dashboardBtn = document.getElementById('dashboard-btn');
            const renterDetailsBtn = document.getElementById('renter-details-btn');
            const roomDetailsBtn = document.getElementById('room-details-btn');
            
            const dashboardOverview = document.getElementById('dashboard-overview');
            const renterDetails = document.getElementById('renter-details');
            const roomDetails = document.getElementById('room-details');
            
            // Sidebar toggle
            const hamburgerMenu = document.querySelector('.hamburger-menu');
            const adminDashboard = document.querySelector('.admin-dashboard');
            
            hamburgerMenu.addEventListener('click', function() {
                adminDashboard.classList.toggle('sidebar-collapsed');
            });
            
            // Update active menu class
            const menuItems = document.querySelectorAll('.nav-item');
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    menuItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            dashboardBtn.addEventListener('click', function() {
                dashboardOverview.style.display = 'block';
                renterDetails.style.display = 'none';
                roomDetails.style.display = 'none';
            });

            renterDetailsBtn.addEventListener('click', function() {
                dashboardOverview.style.display = 'none';
                renterDetails.style.display = 'block';
                roomDetails.style.display = 'none';
            });

            roomDetailsBtn.addEventListener('click', function() {
                dashboardOverview.style.display = 'none';
                renterDetails.style.display = 'none';
                roomDetails.style.display = 'block';
            });
        });
    </script>
</body>
</html>