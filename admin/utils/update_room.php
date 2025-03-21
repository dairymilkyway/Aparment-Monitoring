<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}
include "../../includes/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = $_POST['room_id'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    
    try {
        // Update room with description
        $query = "UPDATE rooms SET description = ?, price = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$description, $price, $room_id]);
        
        $_SESSION['success_message'] = "Room updated successfully.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error updating room: " . $e->getMessage();
    }
    
    header("Location: ../dashboard.php");
    exit();
} else {
    header("Location: ../dashboard.php");
    exit();
}
?>
