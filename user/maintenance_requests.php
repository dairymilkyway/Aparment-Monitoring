<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}
include "../includes/db.php";
include "../includes/navbar.php";

// Fetch maintenance requests for the logged-in user
$query = "SELECT mr.*, mr.room_number 
          FROM maintenance_requests mr
          WHERE mr.tenant_id IN (SELECT id FROM tenants WHERE user_id = ?)
          ORDER BY mr.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$maintenance_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success_data = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['description'])) {
        // Handle maintenance request
        $description = $_POST['description'];
        $reason = $_POST['reason'];
        $room_number = $_POST['room_number'];
        
        $query = "INSERT INTO maintenance_requests (tenant_id, description, reason, status, room_number) VALUES (?, ?, ?, 'Pending', ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$_SESSION['user_id'], $description, $reason, $room_number]);
        
        // Store success data for modal
        $success_data = [
            'type' => 'maintenance',
            'description' => substr($description, 0, 100) . (strlen($description) > 100 ? '...' : ''),
            'reason' => $reason,
            'room_number' => $room_number
        ];
    }
}

// Helper function to get icon based on reason
function getReasonIcon($reason) {
    switch ($reason) {
        case 'Plumbing Issue':
            return 'fa-faucet';
        case 'Electrical Issue':
            return 'fa-bolt';
        case 'Heating Issue':
            return 'fa-temperature-high';
        case 'Appliance Issue':
            return 'fa-tv';
        default:
            return 'fa-wrench';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Requests</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/userdashboard.css">
    <link rel="stylesheet" href="../css/modal.css">
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        function closeSuccessModal() {
            document.getElementById('success-modal').classList.remove('active');
            window.location.href = 'maintenance_requests.php';
        }
        
        // Show success modal on page load if there's success data
        window.onload = function() {
            <?php if ($success_data): ?>
            document.getElementById('success-modal').classList.add('active');
            <?php endif; ?>
            
            // Initialize filter
            document.getElementById('filter-all').classList.add('active');
        }
        
        function filterTickets(status, element) {
            // Update active filter button
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            element.classList.add('active');
            
            // Filter tickets
            const tickets = document.querySelectorAll('.ticket');
            tickets.forEach(ticket => {
                if (status === 'all' || ticket.dataset.status.toLowerCase() === status) {
                    ticket.style.display = 'block';
                } else {
                    ticket.style.display = 'none';
                }
            });
        }
        
        function toggleDetails(id) {
            const details = document.getElementById('details-' + id);
            if (details.style.maxHeight) {
                details.style.maxHeight = null;
                document.getElementById('expand-' + id).classList.remove('fa-chevron-up');
                document.getElementById('expand-' + id).classList.add('fa-chevron-down');
            } else {
                details.style.maxHeight = details.scrollHeight + 'px';
                document.getElementById('expand-' + id).classList.remove('fa-chevron-down');
                document.getElementById('expand-' + id).classList.add('fa-chevron-up');
            }
        }
    </script>
</head>
<body>
    <div class="container">
        
        <div class="filter-container">
            <div class="filter-label">Filter by:</div>
            <div class="filter-buttons">
                <button class="filter-btn" id="filter-all" onclick="filterTickets('all', this)">All</button>
                <button class="filter-btn" id="filter-pending" onclick="filterTickets('pending', this)">Pending</button>
                <button class="filter-btn" id="filter-inprogress" onclick="filterTickets('in progress', this)">In Progress</button>
                <button class="filter-btn" id="filter-resolved" onclick="filterTickets('resolved', this)">Resolved</button>
            </div>
        </div>
        
        <div class="ticket-list">
            <?php if (count($maintenance_requests) > 0): ?>
                <?php foreach ($maintenance_requests as $key => $request): ?>
                    <div class="ticket" data-status="<?php echo strtolower($request['status']); ?>">
                        <div class="ticket-icon">
                            <div class="icon-wrapper <?php echo strtolower($request['status']); ?>">
                                <i class="fas <?php echo getReasonIcon($request['reason']); ?>"></i>
                            </div>
                        </div>
                        <div class="ticket-content">
                            <div class="ticket-header">
                                <div class="ticket-title">
                                    <h3><?php echo htmlspecialchars($request['reason']); ?></h3>
                                </div>
                                <div class="ticket-status-wrapper">
                                    <span class="ticket-status <?php echo strtolower($request['status']); ?>">
                                        <?php echo htmlspecialchars($request['status']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="ticket-summary">
                                <?php echo htmlspecialchars(substr($request['description'], 0, 90) . (strlen($request['description']) > 90 ? '...' : '')); ?>
                            </div>
                            <div class="ticket-room-number">
                                <strong>Room:</strong> <?php echo htmlspecialchars($request['room_number']); ?>
                            </div>
                            
                            <div class="ticket-footer">
                                <div class="ticket-meta">
                                    <span><i class="far fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($request['created_at'])); ?></span>
                                    <?php if ($request['status'] == 'Resolved'): ?>
                                        <span class="resolved-date"><i class="fas fa-check-circle"></i> <?php echo date('M d, Y', strtotime($request['date_resolved'])); ?></span>
                                    <?php endif; ?>
                                </div>
                                <button class="expand-btn" onclick="toggleDetails('<?php echo $request['id']; ?>')">
                                    <i class="fas fa-chevron-down" id="expand-<?php echo $request['id']; ?>"></i>
                                </button>
                            </div>
                            
                            <div id="details-<?php echo $request['id']; ?>" class="ticket-details">
                                <div class="details-section">
                                    <h4>Full Description</h4>
                                    <p><?php echo nl2br(htmlspecialchars($request['description'])); ?></p>
                                </div>
                                
                                <?php if (!empty($request['admin_response'])): ?>
                                <div class="details-section response-section">
                                    <h4>Staff Response</h4>
                                    <div class="staff-response">
                                        <p><?php echo nl2br(htmlspecialchars($request['admin_response'])); ?></p>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <div class="timeline-marker submitted"></div>
                                        <div class="timeline-content">
                                            <h4>Submitted</h4>
                                            <p><?php echo date('M d, Y - h:i A', strtotime($request['created_at'])); ?></p>
                                        </div>
                                    </div>
                                    
                                    <?php if ($request['status'] != 'pending'): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-marker inprogress"></div>
                                        <div class="timeline-content">
                                            <h4>In Progress</h4>
                                            <p><?php echo date('M d, Y', strtotime($request['updated_at'] ?? $request['created_at'])); ?></p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($request['status'] == 'resolved'): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-marker resolved"></div>
                                        <div class="timeline-content">
                                            <h4>Resolved</h4>
                                            <p><?php echo date('M d, Y - h:i A', strtotime($request['date_resolved'])); ?></p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-requests">
                    <div class="no-requests-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3>No maintenance requests</h3>
                    <p>You haven't submitted any maintenance requests yet.</p>
                    <a href="dashboard.php" class="btn-primary">Submit a Request</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
