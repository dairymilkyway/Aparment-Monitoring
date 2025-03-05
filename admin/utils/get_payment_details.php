<?php
include "../../includes/db.php";

if (isset($_GET['payment_id'])) {
    $payment_id = $_GET['payment_id'];

    // Fetch payment details
    $query = "SELECT p.*, t.name, t.contact FROM payments p JOIN tenants t ON p.tenant_id = t.id WHERE p.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$payment_id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($payment) {
        // Set amount to be paid to 0 if status is paid
        if ($payment['status'] == 'paid') {
            $payment['amount'] = 0;
        }
        echo json_encode($payment);
    } else {
        echo json_encode(['error' => 'Payment not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>