<!-- filepath: admin/dashboard.php -->
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
include "../includes/db.php";
include "fetch.php";

// Calculate statistics
$total_rooms = count($rooms);
$occupied_rooms = array_reduce($rooms, function($count, $room) {
    return $count + ($room['status'] == 'occupied' ? 1 : 0);
}, 0);
$total_tenants = count($tenants);
$pending_maintenance = array_reduce($maintenance_requests, function($count, $request) {
    return $count + ($request['status'] == 'Pending' ? 1 : 0);
}, 0);
$pending_requests = array_reduce($room_requests, function($count, $request) {
    return $count + ($request['status'] == 'pending' ? 1 : 0);
}, 0);
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
    <link rel="stylesheet" href="../css/responsive.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show the selected section
            document.getElementById(sectionId).classList.add('active');
            
            // Update active state in sidebar
            document.querySelectorAll('.sidebar-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Find the link that triggered this and add active class
            document.querySelector(`.sidebar-link[data-section="${sectionId}"]`).classList.add('active');
        }
        
        function toggleDropdown(id) {
            var dropdownContent = document.getElementById(id);
            if (dropdownContent.style.display === "block") {
                dropdownContent.style.display = "none";
            } else {
                dropdownContent.style.display = "block";
            }
        }
        
        function toggleMobileSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
        }
        
        // Show the dashboard section by default
        window.onload = function() {
            showSection('dashboard-section');
            
            // Initialize DataTables for tables outside of dashboard widgets
            $('.section:not(#dashboard-section) table').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
            });
            
            // Simple tables inside dashboard widgets don't need pagination
            $('#dashboard-section .dashboard-widget table').each(function(){
                $(this).wrap('<div class="table-responsive"></div>');
            });
        }
    </script>
</head>
<body>
    
    <div class="admin-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-building"></i>
                <h2>Admin Dashboard</h2>
            </div>
            
            <ul>
                <li>
                    <a href="#" class="sidebar-link active" data-section="dashboard-section" onclick="showSection('dashboard-section')">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="sidebar-link" data-section="tenants-section" onclick="showSection('tenants-section')">
                        <i class="fas fa-users"></i>
                        <span>Tenants</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="sidebar-link" data-section="payments-section" onclick="showSection('payments-section')">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Payments</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="sidebar-link" data-section="maintenance-section" onclick="showSection('maintenance-section')">
                        <i class="fas fa-tools"></i>
                        <span>Maintenance</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="sidebar-link" data-section="rooms-section" onclick="showSection('rooms-section')">
                        <i class="fas fa-door-open"></i>
                        <span>Rooms</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="sidebar-link" data-section="room-requests-section" onclick="showSection('room-requests-section')">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Room Requests</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="sidebar-link" data-section="users-section" onclick="showSection('users-section')">
                        <i class="fas fa-user-cog"></i>
                        <span>Users</span>
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <a href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
        
        <div class="content">
            
            
            <!-- Dashboard Overview Section -->
            <div id="dashboard-section" class="section active">
                <div class="content-header">
                    <h2 class="content-title">Dashboard Overview</h2>
                    <div class="date"><?php echo date('F d, Y'); ?></div>
                </div>
                
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon stat-rooms">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div class="stat-info">
                            <h3 class="stat-value"><?php echo $total_rooms; ?></h3>
                            <p class="stat-label">Total Rooms</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon stat-tenants">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3 class="stat-value"><?php echo $total_tenants; ?></h3>
                            <p class="stat-label">Active Tenants</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon stat-maintenance">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div class="stat-info">
                            <h3 class="stat-value"><?php echo $pending_maintenance; ?></h3>
                            <p class="stat-label">Pending Maintenance</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon stat-payments">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="stat-info">
                            <h3 class="stat-value"><?php echo $pending_requests; ?></h3>
                            <p class="stat-label">Room Requests</p>
                        </div>
                    </div>
                </div>
                
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <h3 class="widget-title">Recent Room Requests</h3>
                        <a href="#" class="widget-action" onclick="showSection('room-requests-section')">View All</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Room</th>
                                <th>Name</th>
                                <th>Pax</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Get the 5 most recent room requests
                            $recent_requests = array_slice($room_requests, 0, 5);
                            foreach ($recent_requests as $request): 
                                // Find the room number for this request
                                $room_number = '';
                                foreach ($rooms as $room) {
                                    if ($room['id'] == $request['room_id']) {
                                        $room_number = $room['room_number'];
                                        break;
                                    }
                                }
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($room_number); ?></td>
                                <td><?php echo htmlspecialchars($request['name']); ?></td>
                                <td><?php echo htmlspecialchars($request['pax']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower($request['status']); ?>">
                                        <?php echo htmlspecialchars($request['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($request['request_date'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <h3 class="widget-title">Recent Maintenance Requests</h3>
                        <a href="#" class="widget-action" onclick="showSection('maintenance-section')">View All</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Room Id</th>
                                <th>Reason</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Date Requested</th>
                                <th>Date Resolved</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
// Get the 5 most recent maintenance requests
$recent_maintenance = array_slice($maintenance_requests, 0, 5);
foreach ($recent_maintenance as $request): ?>
    <tr>
        <!-- ID Column -->
        <td><?php echo htmlspecialchars($request['id']); ?></td>

        <!-- Room ID Column -->
        <td><?php 
            echo isset($request['room_number']) ? htmlspecialchars($request['room_number']) : '—'; 
        ?></td>

        <!-- Reason Column (Truncated with ellipsis if necessary) -->
        <td><?php 
            $reason = isset($request['reason']) ? $request['reason'] : '—';
            echo htmlspecialchars(substr($reason, 0, 50)) . (strlen($reason) > 50 ? '...' : ''); 
        ?></td>

        <!-- Description Column (Truncated with ellipsis if necessary) -->
        <td class="maintenance-description">
            <?php 
            $description = isset($request['description']) ? $request['description'] : '—';
            echo htmlspecialchars(substr($description, 0, 50)) . (strlen($description) > 50 ? '...' : ''); 
            ?>
        </td>

        <!-- Status Column -->
        <td>
            <span class="maintenance-status <?php echo strtolower(isset($request['status']) ? $request['status'] : 'unknown'); ?>">
                <?php echo htmlspecialchars(isset($request['status']) ? $request['status'] : 'Unknown'); ?>
            </span>
        </td>

        <!-- Created At Column -->
        <td>
            <?php 
            echo isset($request['created_at']) ? date('M d, Y', strtotime($request['created_at'])) : '—'; 
            ?>
        </td>

        <!-- Date Resolved Column -->
        <td>
            <?php 
            echo isset($request['date_resolved']) ? date('M d, Y', strtotime($request['date_resolved'])) : '—'; 
            ?>
        </td>
    </tr>
<?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
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
    
    <!-- Make tables responsive -->
    <script>
        // Wrap all tables with a responsive div
        $(document).ready(function() {
            $('table').each(function() {
                if (!$(this).parent().hasClass('responsive-table')) {
                    $(this).wrap('<div class="responsive-table"></div>');
                }
            });
        });
    </script>
</body>
</html>