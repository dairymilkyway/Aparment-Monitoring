<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}
include "../../includes/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_number = $_POST['room_number'];
    $price = $_POST['price'];
    $status = 'available'; // Set status to available by default
    
    try {
        // Check if room_number already exists
        $check_query = "SELECT COUNT(*) FROM rooms WHERE room_number = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->execute([$room_number]);
        $room_exists = $check_stmt->fetchColumn();
        
        if ($room_exists > 0) {
            $_SESSION['error_message'] = "Room number already exists.";
            header("Location: ../dashboard.php");
            exit();
        }
        
        // Insert new room
        $query = "INSERT INTO rooms (room_number, status, price) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$room_number, $status, $price]);
        
        $_SESSION['success_message'] = "Room added successfully.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error adding room: " . $e->getMessage();
    }
    
    header("Location: ../dashboard.php");
    exit();
} else {
    header("Location: ../dashboard.php");
    exit();
}
?>