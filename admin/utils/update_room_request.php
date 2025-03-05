<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}
include "../../includes/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];

    // Fetch the room request details
    $query = "SELECT rr.*, r.price FROM room_requests rr JOIN rooms r ON rr.room_id = r.id WHERE rr.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$request_id]);
    $room_request = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($room_request) {
        // Update the room request status
        $query = "UPDATE room_requests SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$status, $request_id]);

        if ($status == 'Approved') {
            // Insert the renter into the tenants table
            $query = "INSERT INTO tenants (name, apartment, contact) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$room_request['name'], $room_request['room_id'], '']);

            // Get the tenant ID
            $tenant_id = $conn->lastInsertId();

            // Update the room status to occupied
            $query = "UPDATE rooms SET status = 'occupied' WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$room_request['room_id']]);

            // Insert the tenant payment
            $date_approved = date('Y-m-d');
            $due_date = date('Y-m-d', strtotime('+1 month'));
            $query = "INSERT INTO payments (tenant_id, amount, date_approved, due_date, status) VALUES (?, ?, ?, ?, 'not paid')";
            $stmt = $conn->prepare($query);
            $stmt->execute([$tenant_id, $room_request['price'], $date_approved, $due_date]);
        }
    }

    header("Location: ../dashboard.php");
    exit();
} else {
    header("Location: ../dashboard.php");
    exit();
}
?>