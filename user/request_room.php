<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}
include "../includes/db.php";
include "../includes/navbar.php";

// Fetch all rooms
$query = "SELECT * FROM rooms";
$stmt = $conn->prepare($query);
$stmt->execute();
$all_rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success_data = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['room_id'])) {
        // Handle room rental request
        $room_id = $_POST['room_id'];
        $name = $_POST['name'];
        $contact = $_POST['contact'];
        $pax = $_POST['pax'];
        $mode_of_payment = $_POST['mode_of_payment'];
        $user_id = $_SESSION['user_id'];
        
        // Get room info for success message
        $room_query = "SELECT room_number, price FROM rooms WHERE id = ?";
        $room_stmt = $conn->prepare($room_query);
        $room_stmt->execute([$room_id]);
        $room_info = $room_stmt->fetch(PDO::FETCH_ASSOC);
        
        $query = "INSERT INTO room_requests (user_id, room_id, name, contact, pax, mode_of_payment, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($query);
        $stmt->execute([$user_id, $room_id, $name, $contact, $pax, $mode_of_payment]);
        
        // Update room status to pending
        $update_room_query = "UPDATE rooms SET status = 'pending' WHERE id = ?";
        $update_room_stmt = $conn->prepare($update_room_query);
        $update_room_stmt->execute([$room_id]);
        
        // Store success data for modal
        $success_data = [
            'type' => 'room',
            'room_number' => $room_info['room_number'],
            'room_price' => $room_info['price'],
            'name' => $name,
            'contact' => $contact,
            'pax' => $pax,
            'mode_of_payment' => $mode_of_payment
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request a Room</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/userdashboard.css">
    <link rel="stylesheet" href="../css/modal.css">
    <script>
        function selectRoom(roomId, roomNumber, roomPrice) {
            // Open the modal
            document.getElementById('room-request-modal').classList.add('active');
            
            // Update the room information in the modal
            document.getElementById('selected-room-id').value = roomId;
            document.getElementById('modal-room-number').textContent = roomNumber;
            document.getElementById('preview-room-number').textContent = roomNumber;
            document.getElementById('room-price').textContent = roomPrice;
        }
        
        function closeModal() {
            document.getElementById('room-request-modal').classList.remove('active');
        }
        
        function closeSuccessModal() {
            document.getElementById('success-modal').classList.remove('active');
        }
        
        // Close modal if user clicks outside of it
        window.onclick = function(event) {
            var modal = document.getElementById('room-request-modal');
            var successModal = document.getElementById('success-modal');
            
            if (event.target == modal) {
                closeModal();
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
        <h1>Request a Room</h1>
        <p>Click on an available room to select it for your request.</p>
        
        <div class="room-grid">
            <?php foreach ($all_rooms as $room): ?>
                <div id="room-<?php echo $room['id']; ?>" 
                     class="room-card <?php echo $room['status'] == 'available' ? 'available' : ($room['status'] == 'pending' ? 'pending' : 'occupied'); ?>"
                     <?php if ($room['status'] == 'available'): ?>
                     onclick="selectRoom(<?php echo $room['id']; ?>, '<?php echo htmlspecialchars($room['room_number']); ?>', '<?php echo htmlspecialchars($room['price']); ?>')"
                     <?php endif; ?>>
                    <div class="room-number"><?php echo htmlspecialchars($room['room_number']); ?></div>
                    <?php if (isset($room['price']) && $room['price'] > 0): ?>
                    <div class="room-price">₱<?php echo htmlspecialchars($room['price']); ?></div>
                    <?php endif; ?>
                    <div class="room-status"><?php echo htmlspecialchars($room['status']); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Include Room Request Modal -->
        <?php include "room_request_modal.php"; ?>
        
        <!-- Include Success Modal -->
        <?php include "success_modal.php"; ?>
    </div>
</body>
</html>