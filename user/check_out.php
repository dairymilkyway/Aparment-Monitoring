<?php
session_start();
include "../includes/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $data['user_id'];

    try {
        // Find the room rented by the user
        $query = "SELECT room_id FROM renters WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$userId]);
        $renter = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($renter) {
            $roomId = $renter['room_id'];

            // Remove the renter from the renters table
            $delete_query = "DELETE FROM renters WHERE user_id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->execute([$userId]);

            // Update the room status to 'available'
            $update_query = "UPDATE rooms SET status = 'available' WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->execute([$roomId]);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No rented room found.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>