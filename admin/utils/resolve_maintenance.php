<?php
include "../../includes/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    $query = "UPDATE maintenance_requests SET status = 'resolved', date_resolved = NOW() WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Maintenance request resolved successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to resolve maintenance request.";
    }

    header("Location: ../dashboard.php");
    exit();
}
?>