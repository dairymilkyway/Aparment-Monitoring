<?php
session_start();
include "../includes/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $roomId = $data['room_id'];
    $userId = $_SESSION['user_id'];
    $name = $data['name'];
    $dueDate = $data['due_date'];
    $dateOccupied = $data['date_occupied'];
    $numberOfPax = $data['number_of_pax'];

    try {
        // Assign the room to the user
        $insert_query = "INSERT INTO renters (user_id, room_id, name, due_date, date_occupied, number_of_pax)
                         VALUES (?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->execute([$userId, $roomId, $name, $dueDate, $dateOccupied, $numberOfPax]);

        // Update the room status to 'occupied'
        $update_query = "UPDATE rooms SET status = 'occupied' WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->execute([$roomId]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>