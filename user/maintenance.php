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
        $description = $_POST['description'];
        $reason = $_POST['reason'];
        $room_number = $_POST['room_number'];
        $user_id = $_SESSION['user_id'];


        // Validate if the user is a tenant
        $query = "SELECT id FROM tenants WHERE user_id = ? AND deleted_at IS NULL LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->execute([$user_id]);
        $tenant = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tenant) {
            $tenant_id = $tenant['id'];

            // Insert the maintenance request
            $query = "INSERT INTO maintenance_requests (tenant_id, description, reason, room_number, status, created_at) 
                      VALUES (?, ?, ?, ?, 'Pending', NOW())";
            $stmt = $conn->prepare($query);
            $stmt->execute([$tenant_id, $description, $reason, $room_number]);

            // Store success data for modal
            $success_data = [
                'type' => 'maintenance',
                'description' => substr($description, 0, 100) . (strlen($description) > 100 ? '...' : ''),
                'reason' => $reason,
                'room_number' => $room_number
            ];
        } else {
            $_SESSION['error_message'] = "Invalid tenant ID.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Maintenance Request</title>
    <link rel="stylesheet" href="../css/responsive.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/userdashboard.css">
    <link rel="stylesheet" href="../css/modal.css">
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            <div class="form-header">
                <h2>Maintenance Request Form</h2>
                <p>Please provide details about the issue you're experiencing</p>
            </div>
            
            <div class="input-group" style="--animation-order: 1">
                <label for="room_number">Room Number</label>
                <input type="text" id="room_number" name="room_number" value="<?php echo isset($_GET['room_number']) ? htmlspecialchars($_GET['room_number']) : ''; ?>" readonly>
            </div>
            
            <div class="input-group" style="--animation-order: 2">
                <label>Issue Type</label>
                <div class="issue-type-buttons">
                    <input type="hidden" name="reason" id="reason-input" value="" required>
                    
                    <button type="button" class="issue-button" data-value="Plumbing Issue">
                        <i class="fa-solid fa-faucet"></i>
                        <span>Plumbing</span>
                    </button>
                    <button type="button" class="issue-button" data-value="Electrical Issue">
                        <i class="fa-solid fa-bolt"></i>
                        <span>Electrical</span>
                    </button>
                    <button type="button" class="issue-button" data-value="Heating Issue">
                        <i class="fa-solid fa-temperature-high"></i>
                        <span>Heating</span>
                    </button>
                    <button type="button" class="issue-button" data-value="Appliance Issue">
                        <i class="fa-solid fa-tv"></i>
                        <span>Appliance</span>
                    </button>
                    <button type="button" class="issue-button" data-value="Other">
                        <i class="fa-solid fa-question-circle"></i>
                        <span>Other</span>
                    </button>
                </div>
                <div class="error-message" id="reason-error" style="display: none;">Please select an issue type</div>
            </div>
            
            <div class="input-group" style="--animation-order: 3">
                <label for="description">Description of the Issue</label>
                <textarea id="description" name="description" placeholder="Please provide detailed information about the issue..." required></textarea>
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

<script>
    // Add this to your existing JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        const issueButtons = document.querySelectorAll('.issue-button');
        const reasonInput = document.getElementById('reason-input');
        const reasonError = document.getElementById('reason-error');
        
        // Handle button clicks
        issueButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove selected class from all buttons
                issueButtons.forEach(btn => btn.classList.remove('selected'));
                
                // Add selected class to clicked button
                this.classList.add('selected');
                
                // Set the value in the hidden input
                reasonInput.value = this.dataset.value;
                
                // Hide error message if shown
                reasonError.style.display = 'none';
            });
        });
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!reasonInput.value) {
                e.preventDefault();
                reasonError.style.display = 'block';
                
                // Scroll to the error message
                reasonError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
</script>