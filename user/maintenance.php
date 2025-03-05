<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}
include "../includes/db.php";
include "../includes/navbar.php";

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
    <title>Submit Maintenance Request</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/userdashboard.css">
    <link rel="stylesheet" href="../css/modal.css">
    <script>
        function closeSuccessModal() {
            document.getElementById('success-modal').classList.remove('active');
            window.location.href = 'dashboard.php';
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
        <h1>Submit Maintenance Request</h1>
        
        <form method="POST" action="">
            <div class="input-group">
                <label for="room_number">Room Number</label>
                <input type="text" id="room_number" name="room_number" value="<?php echo isset($_GET['room_number']) ? htmlspecialchars($_GET['room_number']) : ''; ?>" readonly>
            </div>
            <div class="input-group">
                <label for="reason">Reason</label>
                <select id="reason" name="reason" required>
                    <option value="Plumbing Issue">Plumbing Issue</option>
                    <option value="Electrical Issue">Electrical Issue</option>
                    <option value="Heating Issue">Heating Issue</option>
                    <option value="Appliance Issue">Appliance Issue</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="input-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <button type="submit" class="btn-primary">Submit Request</button>
        </form>
        
        <!-- Include Success Modal -->
        <?php if ($success_data): ?>
        <div id="success-modal" class="modal-overlay active">
            <div class="modal">
                <button type="button" class="modal-close" onclick="closeSuccessModal()">&times;</button>
                <div class="modal-header">
                    <h2 class="modal-title">Maintenance Request Submitted</h2>
                </div>
                <div class="modal-body">
                    <p><strong>Room Number:</strong> <?php echo htmlspecialchars($success_data['room_number']); ?></p>
                    <p><strong>Reason:</strong> <?php echo htmlspecialchars($success_data['reason']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($success_data['description']); ?></p>
                    <button type="button" class="btn-primary" onclick="closeSuccessModal()">Done</button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>