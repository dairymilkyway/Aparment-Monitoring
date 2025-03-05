<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}
include "../../includes/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_id = $_POST['payment_id'];
    $status = 'paid';

    // Update the payment status
    $query = "UPDATE payments SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$status, $payment_id]);

    // Reload the current page
    header("Location: ../dashboard.php");
    exit();
} else {
    // Reload the current page if accessed via GET
    header("Location: ../dashboard.php");
    exit();
}
?>