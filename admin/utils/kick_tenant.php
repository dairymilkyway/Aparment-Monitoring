<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}
include "../../includes/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenant_id = $_POST['tenant_id'];
    $room_id = $_POST['room_id'];
    
    try {
        // Start transaction
        $conn->beginTransaction();

        // Delete maintenance requests related to the tenant
        $query = "DELETE FROM maintenance_requests WHERE tenant_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$tenant_id]);

        // Delete payments related to the tenant
        $query = "DELETE FROM payments WHERE tenant_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$tenant_id]);

        // Delete room request
        $query = "DELETE FROM room_requests WHERE room_id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$room_id, $tenant_id]);

        // Delete tenant
        $query = "DELETE FROM tenants WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$tenant_id]);

        // Update room status to available
        $query = "UPDATE rooms SET status = 'available' WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$room_id]);

$query = "UPDATE payment_history SET move_out_date = NOW() WHERE tenant_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$tenant_id]);


        // Commit transaction
        $conn->commit();

        $_SESSION['success_message'] = "Tenant kicked successfully.";
    } catch (PDOException $e) {
        // Rollback transaction
        $conn->rollBack();
        $_SESSION['error_message'] = "Error kicking tenant: " . $e->getMessage();
    }
    
    header("Location: ../dashboard.php");
    exit();
} else {
    header("Location: ../dashboard.php");
    exit();
}
?>