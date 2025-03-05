<?php
include "fetch.php";
?>

<div id="payments-section" class="section">
    <h2>Track Rent Payments</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tenant ID</th>
                <th>Amount</th>
                <th>Date Approved</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $payment): ?>
            <tr>
                <td><?php echo htmlspecialchars($payment['id']); ?></td>
                <td><?php echo htmlspecialchars($payment['tenant_id']); ?></td>
                <td><?php echo htmlspecialchars($payment['amount']); ?></td>
                <td><?php echo htmlspecialchars($payment['date_approved']); ?></td>
                <td><?php echo htmlspecialchars($payment['due_date']); ?></td>
                <td><?php echo htmlspecialchars($payment['status']); ?></td>
                <td>
                <?php if ($payment['status'] == 'not paid'): ?>
                    <form method="POST" action="utils/update_payment_status.php" style="display:inline;">
                        <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                        <button type="submit" class="btn-primary">Mark as Paid</button>
                    </form>
                    <?php endif; ?>
                    <button type="button" class="btn-primary" onclick="showPaymentDetails(<?php echo $payment['id']; ?>)">👁️</button>
                   
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Include Payment Details Modal -->
<?php include "utils/payment_details_modal.php"; ?>

<script>
    function showPaymentDetails(paymentId) {
        // Fetch payment details using AJAX
        fetch(`utils/get_payment_details.php?payment_id=${paymentId}`)
            .then(response => response.json())
            .then(data => {
                // Populate modal with payment details
                document.getElementById('modal-tenant-id').textContent = data.tenant_id;
                document.getElementById('modal-tenant-name').textContent = data.name;
                document.getElementById('modal-tenant-contact').textContent = data.contact;
                document.getElementById('modal-amount').textContent = data.amount;
                document.getElementById('modal-date-approved').textContent = data.date_approved;
                document.getElementById('modal-due-date').textContent = data.due_date;
                document.getElementById('modal-status').textContent = data.status;
                
                // Show the modal
                document.getElementById('payment-details-modal').classList.add('active');
            });
    }

    function closePaymentDetailsModal() {
        document.getElementById('payment-details-modal').classList.remove('active');
    }
</script>