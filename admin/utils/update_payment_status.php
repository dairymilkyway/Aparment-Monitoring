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

    try {
        // Start transaction
        $conn->beginTransaction();

        // Get the current payment details, including tenant name
        $query = "SELECT p.*, t.name AS tenant_name, p.due_date 
                  FROM payments p 
                  JOIN tenants t ON p.tenant_id = t.id
                  WHERE p.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$payment_id]);
        $currentPayment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$currentPayment) {
            throw new Exception("Payment not found");
        }

        // Mark the current payment as paid
        $query = "UPDATE payments SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$status, $payment_id]);

        // Calculate the new due date (add 1 month to current due date)
        $currentDueDate = new DateTime($currentPayment['due_date']);
        $newDueDate = $currentDueDate->modify('+1 month')->format('Y-m-d');

        // Insert a new payment for the next month with status 'not paid'
        $query = "INSERT INTO payments (tenant_id, amount, status, due_date) 
                  VALUES (?, ?, 'not paid', ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $currentPayment['tenant_id'], 
            $currentPayment['amount'], 
            $newDueDate
        ]);

        // Insert the confirmed payment into the payment_history table
        $query = "INSERT INTO payment_history (tenant_id, tenant_name, amount_paid, date_of_payment) 
                  VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $currentPayment['tenant_id'], 
            $currentPayment['tenant_name'], 
            $currentPayment['amount']
        ]);

        // Commit the transaction
        $conn->commit();
        
        // Redirect back to dashboard
        header("Location: ../dashboard.php");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
        exit();
    }
} else {
    // Reload the current page if accessed via GET
    header("Location: ../dashboard.php");
    exit();
}
?>
