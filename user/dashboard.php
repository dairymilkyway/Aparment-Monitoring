<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}
include "../includes/db.php";
include "../includes/navbar.php";

// Fetch rented room details
$rented_room_query = "SELECT r.room_number, p.date_approved, p.due_date, p.amount 
                      FROM rooms r 
                      JOIN payments p ON r.id = p.tenant_id 
                      JOIN room_requests rr ON r.id = rr.room_id 
                      WHERE rr.user_id = ? AND rr.status = 'approved'";
$rented_room_stmt = $conn->prepare($rented_room_query);
$rented_room_stmt->execute([$_SESSION['user_id']]);
$rented_room = $rented_room_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch pending room requests
$pending_requests_query = "SELECT rr.*, r.room_number, r.price 
                           FROM room_requests rr 
                           JOIN rooms r ON rr.room_id = r.id 
                           WHERE rr.user_id = ? AND rr.status = 'pending'";
$pending_requests_stmt = $conn->prepare($pending_requests_query);
$pending_requests_stmt->execute([$_SESSION['user_id']]);
$pending_requests = $pending_requests_stmt->fetchAll(PDO::FETCH_ASSOC);

$success_data = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['description'])) {
        // Handle maintenance request
        $description = $_POST['description'];
        $reason = $_POST['reason'];
        $room_number = $_POST['room_number'];
        $query = "INSERT INTO maintenance_requests (tenant_id, description, reason, status) VALUES (?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($query);
        $stmt->execute([$_SESSION['user_id'], $description, $reason]);
        
        // Store success data for modal
        $success_data = [
            'type' => 'maintenance',
            'description' => substr($description, 0, 100) . (strlen($description) > 100 ? '...' : ''),
            'reason' => $reason,
            'room_number' => $room_number
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/userdashboard.css">
    <link rel="stylesheet" href="../css/modal.css">
    <script>
        function showRentedRoomDetails() {
            // Open the modal
            document.getElementById('rented-room-details-modal').classList.add('active');
            
            // Update the room information in the modal
            document.getElementById('modal-rented-room-number').textContent = "<?php echo htmlspecialchars($rented_room['room_number']); ?>";
            document.getElementById('modal-date-approved').textContent = "<?php echo htmlspecialchars($rented_room['date_approved']); ?>";
            document.getElementById('modal-due-date').textContent = "<?php echo htmlspecialchars($rented_room['due_date']); ?>";
            document.getElementById('modal-amount').textContent = "<?php echo htmlspecialchars($rented_room['amount']); ?>";
            document.getElementById('room-number').value = "<?php echo htmlspecialchars($rented_room['room_number']); ?>";
        }
        
        function closeRentedRoomDetailsModal() {
            document.getElementById('rented-room-details-modal').classList.remove('active');
        }
        
        function closeSuccessModal() {
            document.getElementById('success-modal').classList.remove('active');
        }
        
        // Close modal if user clicks outside of it
        window.onclick = function(event) {
            var rentedRoomModal = document.getElementById('rented-room-details-modal');
            var successModal = document.getElementById('success-modal');
            
            if (event.target == rentedRoomModal) {
                closeRentedRoomDetailsModal();
            }
            
            if (event.target == successModal) {
                closeSuccessModal();
            }
        }
        
        // Show success modal on page load if there's success data
        window.onload = function() {
            <?php if ($success_data): ?>
            document.getElementById('success-modal').classList.add('active');
            <?php endif; ?>
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>User Dashboard</h1>

        <?php if ($rented_room): ?>
            <h2>Your Rented Room</h2>
            <div class="room-grid">
                <div class="room-card rented" onclick="showRentedRoomDetails()">
                    <div class="room-number"><?php echo htmlspecialchars($rented_room['room_number']); ?></div>
                    <div class="room-status">Occupied</div>
                </div>
            </div>
        <?php endif; ?>

        <h2>Pending Room Requests</h2>
        <?php if (count($pending_requests) > 0): ?>
            <div class="room-grid">
                <?php foreach ($pending_requests as $request): ?>
                    <div class="room-card pending-request">
                        <div class="room-number"><?php echo htmlspecialchars($request['room_number']); ?></div>
                        <div class="room-price">₱<?php echo htmlspecialchars($request['price']); ?></div>
                        <div class="room-status">Pending</div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No Pending Request for Room</p>
        <?php endif; ?>
        
        <!-- Include Success Modal -->
        <?php include "success_modal.php"; ?>
        
        <!-- Include Rented Room Details Modal -->
        <div id="rented-room-details-modal" class="modal-overlay">
            <div class="modal">
                <button type="button" class="modal-close" onclick="closeRentedRoomDetailsModal()">&times;</button>
                <div class="modal-header">
                    <h2 class="modal-title">Rented Room Details</h2>
                </div>
                <div class="modal-body">
                    <p><strong>Room Number:</strong> <span id="modal-rented-room-number"></span></p>
                    <p><strong>Date Approved:</strong> <span id="modal-date-approved"></span></p>
                    <p><strong>Due Date:</strong> <span id="modal-due-date"></span></p>
                    <p><strong>Amount to be Paid:</strong> ₱<span id="modal-amount"></span></p>
                    <form method="GET" action="maintenance.php">
                        <input type="hidden" id="room-number" name="room_number" value="">
                        <button type="submit" class="btn-primary">Submit Maintenance Request</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>