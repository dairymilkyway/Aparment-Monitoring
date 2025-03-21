<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}
include "../includes/db.php";
include "../includes/navbar.php";

// Fetch rented rooms
$rented_room_query = "SELECT r.room_number, rr.date_approved, rr.due_date, r.price, r.id 
                       FROM rooms r 
                       JOIN payments p ON r.id = p.tenant_id 
                       JOIN room_requests rr ON r.id = rr.room_id 
                       WHERE rr.user_id = ? AND rr.status = 'approved'";
$rented_room_stmt = $conn->prepare($rented_room_query);
$rented_room_stmt->execute([$_SESSION['user_id']]);
$rented_rooms = $rented_room_stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all records

// Fetch pending room requests
$pending_requests_query = "SELECT rr.*, r.room_number, r.price 
                           FROM room_requests rr 
                           JOIN rooms r ON rr.room_id = r.id 
                           WHERE rr.user_id = ? AND rr.status = 'pending'";
$pending_requests_stmt = $conn->prepare($pending_requests_query);
$pending_requests_stmt->execute([$_SESSION['user_id']]);
$pending_requests = $pending_requests_stmt->fetchAll(PDO::FETCH_ASSOC);

$success_data = null;

// Handle maintenance request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['description'])) {
    $description = $_POST['description'];
    $reason = $_POST['reason'];
    $room_number = $_POST['room_number'];
    $query = "INSERT INTO maintenance_requests (tenant_id, description, reason, status) VALUES (?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id'], $description, $reason]);

    $success_data = [
        'type' => 'maintenance',
        'description' => substr($description, 0, 100) . (strlen($description) > 100 ? '...' : ''),
        'reason' => $reason,
        'room_number' => $room_number
    ];
}

// ✅ Handle move-out request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['room_id']) && !empty($_POST['room_id'])) {
    $room_id = $_POST['room_id'];

    try {
        // ✅ 1. Find the tenant_id based on the room_id
        $query = "SELECT id FROM tenants WHERE apartment = ? AND deleted_at IS NULL LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->execute([$room_id]);
        $tenant = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($tenant) {
            $tenant_id = $tenant['id'];
    
            // ✅ 2. Soft delete tenant by updating deleted_at
            $query = "UPDATE tenants SET deleted_at = NOW() WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$tenant_id]);
    
            // ✅ 3. Mark room as available
            $query = "UPDATE rooms SET status = 'available' WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$room_id]);
    
            // ✅ 4. Update room request status to 'moved out'
            $query = "UPDATE room_requests SET status = 'moved out' WHERE room_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$room_id]);
    
            // ✅ 5. Record move-out date in payment history
            $query = "UPDATE payment_history 
                      SET move_out_date = NOW() 
                      WHERE tenant_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$tenant_id]);
    
            // ✅ 6. Mark payments as finalized
            $query = "UPDATE payments SET status = 'finalized' WHERE tenant_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$tenant_id]);
    
            // ✅ 7. Delete payments with status 'not paid'
            $query = "DELETE FROM payments WHERE tenant_id = ? AND status = 'not paid'";
            $stmt = $conn->prepare($query);
            $stmt->execute([$tenant_id]);
    
            // ✅ 8. Delete maintenance requests related to the tenant
            $query = "DELETE FROM maintenance_requests WHERE tenant_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$tenant_id]);
    
            $success_data = [
                'type' => 'moveout',
                'room_number' => $room_id
            ];
        } else {
            $success_data = [
                'type' => 'error',
                'message' => 'Error: No tenant found for this room.'
            ];
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
    header("Location: ./dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../css/responsive.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/userdashboard.css">
    <link rel="stylesheet" href="../css/modal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
const rentedRooms = <?php echo json_encode($rented_rooms); ?>;

function showRentedRoomDetails(roomIndex) {
    const rentedRoom = rentedRooms[roomIndex];

    document.getElementById('modal-rented-room-number').textContent = rentedRoom.room_number;
    document.getElementById('modal-date-approved').textContent = rentedRoom.date_approved;
    document.getElementById('modal-due-date').textContent = rentedRoom.due_date;
    document.getElementById('modal-amount').textContent = `₱${rentedRoom.price}`;

    // Pass room_id for move-out logic
    document.getElementById('room-number').value = rentedRoom.room_number;
    document.getElementById('room-id').value = rentedRoom.id;

    document.getElementById('rented-room-details-modal').classList.add('active');
}

function closeRentedRoomDetailsModal() {
    document.getElementById('rented-room-details-modal').classList.remove('active');
}

window.onclick = function (event) {
    const rentedRoomModal = document.getElementById('rented-room-details-modal');
    const successModal = document.getElementById('success-modal');

    if (event.target === rentedRoomModal) closeRentedRoomDetailsModal();
    if (event.target === successModal) closeSuccessModal();
};

window.onload = function () {
    <?php if ($success_data): ?>
    if ("<?php echo $success_data['type']; ?>" === "moveout") {
        alert("✅ Room <?php echo $success_data['room_number']; ?> moved out successfully.");
    } else if ("<?php echo $success_data['type']; ?>" === "error") {
        alert("❌ <?php echo $success_data['message']; ?>");
    }
    <?php endif; ?>
};
</script>
</head>
<body>
    <div class="container">
        <h1>User Dashboard</h1>

        <!-- Rented Rooms Section -->
        <section class="dashboard-section">
            <div class="section-header">
                <h2><i class="fas fa-home"></i> Your Rented Room</h2>
            </div>
            <?php if (count($rented_rooms) > 0): ?>
                <div class="room-grid">
                    <?php foreach ($rented_rooms as $index => $rented_room): ?>
                        <div class="room-card rented" onclick="showRentedRoomDetails(<?php echo $index; ?>)">
                            <div class="room-number"><?php echo htmlspecialchars($rented_room['room_number']); ?></div>
                            <div class="room-price">₱<?php echo htmlspecialchars($rented_room['price']); ?>/mo</div>
                            <div class="room-status">Occupied</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>You haven't rented any rooms yet.</p>
            <?php endif; ?>
        </section>

        <!-- Pending Requests Section -->
        <section class="dashboard-section">
            <div class="section-header">
                <h2><i class="fas fa-clock"></i> Pending Room Requests</h2>
            </div>
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
                <p>No pending requests for rooms.</p>
            <?php endif; ?>
        </section>

        <!-- Success Modal -->
        <?php include "success_modal.php"; ?>

        <!-- Rented Room Details Modal -->
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
                    <p><strong>Monthly Amount:</strong> <span id="modal-amount"></span></p>

                    <!-- Maintenance Request Form -->
                    <form method="GET" action="maintenance.php">
                        <input type="hidden" id="room-number" name="room_number" value="">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-wrench"></i>&nbsp; Submit Maintenance Request
                        </button>
                    </form>

                    <!-- Move Out Button Form -->
                    <center>
                        <form method="POST">
                            <input type="hidden" id="room-id" name="room_id" value="">
                            <button type="submit" class="btn-danger mt-3">
                                <i class="fas fa-sign-out-alt"></i>&nbsp; Move Out
                            </button>
                        </form>
                    </center>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
