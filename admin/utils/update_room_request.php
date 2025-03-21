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

    // Start transaction
    $conn->beginTransaction();

    // Fetch the room request details
    $query = "SELECT rr.*, r.price FROM room_requests rr JOIN rooms r ON rr.room_id = r.id WHERE rr.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$request_id]);
    $room_request = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($room_request) {
        // Update the room request status and set date_approved if approved
        $query = "UPDATE room_requests SET status = ?, date_approved = NOW() WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$status, $request_id]);

        if ($status == 'Approved') {
            // Insert the renter into the tenants table with user_id
            $query = "INSERT INTO tenants (user_id, name, apartment, contact) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$room_request['user_id'], $room_request['name'], $room_request['room_id'], $room_request['contact']]);
            
            // Get the tenant ID
            $tenant_id = $conn->lastInsertId();
            
            // Update the room status to occupied
            $query = "UPDATE rooms SET status = 'occupied' WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$room_request['room_id']]);
            
            // Insert the tenant payment with due_date in payments table
            $query = "INSERT INTO payments (tenant_id, amount, status, due_date) VALUES (?, ?, 'not paid', DATE_ADD(NOW(), INTERVAL 1 MONTH))";
            $stmt = $conn->prepare($query);
            $stmt->execute([$tenant_id, $room_request['price']]);

            // Insert into payment history
            $query = "INSERT INTO payment_history (tenant_id, tenant_name, amount_paid, date_of_payment) 
                      VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                $tenant_id,
                $room_request['name'],
                $room_request['price']
            ]);
        } elseif ($status == 'Rejected') {
            // Update the room status to available if the request is rejected
            $query = "UPDATE rooms SET status = 'available' WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$room_request['room_id']]);
        }
    }

    // Commit transaction
    $conn->commit();

    header("Location: ../dashboard.php");
    exit();
}
?>
