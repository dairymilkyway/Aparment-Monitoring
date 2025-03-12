<?php include "fetch.php"; ?>

<div id="payments-section" class="section">
    <div class="payments-header">
        <h2 class="content-title">
            <i class="fas fa-money-bill-wave"></i> Track Rent Payments
        </h2>
    </div>
    
    <div class="payments-summary">
        <div class="summary-card total-payments">
            <div class="summary-icon"><i class="fas fa-chart-line"></i></div>
            <div class="summary-details">
                <h3>Total Payments</h3>
                <p class="summary-value">₱<?php echo number_format($total_payments, 2); ?></p>
            </div>
        </div>
        <div class="summary-card pending-payments">
            <div class="summary-icon"><i class="fas fa-clock"></i></div>
            <div class="summary-details">
                <h3>Pending</h3>
                <p class="summary-value">₱<?php echo number_format($pending_payments, 2); ?></p>
            </div>
        </div>
        <div class="summary-card paid-payments">
            <div class="summary-icon"><i class="fas fa-check-circle"></i></div>
            <div class="summary-details">
                <h3>Paid</h3>
                <p class="summary-value">₱<?php echo number_format($paid_payments, 2); ?></p>
            </div>
        </div>
    </div>
    
    <table class="payments-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tenant</th>
                <th>Contact Number</th> <!-- New column header -->
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
                <td class="tenant-name"><?php echo htmlspecialchars($payment['name']); ?></td>
                <td class="contact-number"><?php echo htmlspecialchars($payment['contact']); ?></td> <!-- New column data -->
                <td class="payment-amount"><?php echo htmlspecialchars($payment['amount']); ?></td>
                <td><?php echo htmlspecialchars($payment['date_approved']); ?></td>
                <td class="payment-due <?php echo (strtotime($payment['due_date']) < time()) ? 'overdue' : ''; ?>"><?php echo htmlspecialchars($payment['due_date']); ?></td>
                <td>
                    <span class="payment-status <?php echo ($payment['status'] == 'paid') ? 'paid' : 'not-paid'; ?>">
                        <?php echo htmlspecialchars($payment['status']); ?>
                    </span>
                </td>
                <td class="payment-actions">
                    <?php if ($payment['status'] == 'not paid'): ?>
                    <form method="POST" action="utils/update_payment_status.php" style="display:inline;">
                        <input type="hidden" name="payment_id" value="<?php echo htmlspecialchars($payment['id']); ?>">
                        <button type="submit" class="payment-action-btn btn-mark-paid">
                            <i class="fas fa-check"></i> Mark Paid
                        </button>
                    </form>
                    <?php endif; ?>
                    <button type="button" class="payment-action-btn btn-view-details" onclick="showPaymentDetails(<?php echo $payment['id']; ?>)">
                        <i class="fas fa-eye"></i> View
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Include Payment Details Modal -->
<?php include "utils/payment_details_modal.php"; ?>

<!-- Link to CSS file -->
<link rel="stylesheet" href="./css/payments.css">

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