<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}
include "../includes/db.php";
include "../includes/navbar.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenant_id = $_SESSION['user_id'];
    $description = $_POST['description'];

    $query = "INSERT INTO maintenance_requests (tenant_id, description, status) VALUES (?, ?, 'Pending')";
    $stmt = $conn->prepare($query);
    $stmt->execute([$tenant_id, $description]);
    $success_message = "Maintenance request submitted successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Maintenance Request</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Submit Maintenance Request</h1>
        <?php if (isset($success_message)): ?>
        <div class="success-message">
            <?php echo $success_message; ?>
        </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="input-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <button type="submit">Submit Request</button>
        </form>
    </div>
</body>
</html>