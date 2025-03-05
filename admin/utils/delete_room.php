<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}
include "../../includes/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = $_POST['room_id'];
    
    try {
        // Check if room is associated with any tenant or request
        $check_query = "SELECT COUNT(*) FROM room_requests WHERE room_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->execute([$room_id]);
        $has_requests = $check_stmt->fetchColumn();
        
        if ($has_requests > 0) {
            $_SESSION['error_message'] = "Cannot delete room as it has associated requests.";
            header("Location: ../dashboard.php");
            exit();
        }
        
        // Delete room
        $query = "DELETE FROM rooms WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$room_id]);
        
        $_SESSION['success_message'] = "Room deleted successfully.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error deleting room: " . $e->getMessage();
    }
    
    header("Location: ../dashboard.php");
    exit();
} else {
    header("Location: ../dashboard.php");
    exit();
}
?>