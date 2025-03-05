<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}
include "../includes/db.php";
include "../includes/navbar.php";

// Fetch maintenance requests for the logged-in user
$query = "SELECT * FROM maintenance_requests WHERE tenant_id = ?";
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
    <title>Maintenance Requests</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/userdashboard.css">
    <link rel="stylesheet" href="../css/modal.css">
    <style>
        .ticket {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
        }
        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .ticket-header h3 {
            margin: 0;
            font-size: 1.2rem;
        }
        .ticket-status {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9rem;
            color: white;
        }
        .ticket-status.pending {
            background-color: #ffc107;
        }
        .ticket-status.resolved {
            background-color: #28a745;
        }
        .ticket-body {
            margin-bottom: 10px;
        }
        .ticket-footer {
            font-size: 0.9rem;
            color: #666;
        }
    </style>
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
        }
    </script>
</head>
<body>
    <div class="container">
        
        <h2>Submitted Requests</h2>
        <?php if (count($maintenance_requests) > 0): ?>
            <?php foreach ($maintenance_requests as $request): ?>
                <div class="ticket">
                    <div class="ticket-header">
                        <h3><?php echo htmlspecialchars($request['reason']); ?></h3>
                        <span class="ticket-status <?php echo strtolower($request['status']); ?>"><?php echo htmlspecialchars($request['status']); ?></span>
                    </div>
                    <div class="ticket-body">
                        <p><?php echo htmlspecialchars($request['description']); ?></p>
                    </div>
                    <div class="ticket-footer">
                        <p><strong>Submitted:</strong> <?php echo htmlspecialchars($request['created_at']); ?></p>
                        <?php if ($request['status'] == 'resolved'): ?>
                            <p><strong>Resolved:</strong> <?php echo htmlspecialchars($request['date_resolved']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No maintenance requests submitted yet.</p>
        <?php endif; ?>
        
    </div>
</body>
</html>