<!-- filepath: c:\xampp\htdocs\apartment-monitoring\admin\payment_history.php -->
<div id="payment-history-section" class="section">
    <div class="content-header">
        <h2 class="content-title">
            <i class="fas fa-history"></i> Tenant Payment History
        </h2>
    </div>
    
    <table class="payment-history-table">
        <thead>
            <tr>
                <th>Tenant Name</th>
                <th>Amount Paid</th>
                <th>Date of Payment</th>
                <th>Move-Out Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT * FROM payment_history ORDER BY date_of_payment DESC";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $payment_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($payment_history as $history): ?>
                <tr>
                    <td><?php echo htmlspecialchars($history['tenant_name']); ?></td>
                    <td>₱<?php echo number_format($history['amount_paid'], 2); ?></td>
                    <td><?php echo date('M d, Y - h:i A', strtotime($history['date_of_payment'])); ?></td>
                    <td><?php echo $history['move_out_date'] ? date('M d, Y', strtotime($history['move_out_date'])) : '—'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>