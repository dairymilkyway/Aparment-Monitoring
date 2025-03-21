<!-- filepath: c:\xampp\htdocs\apartment-monitoring\admin\payment_history.php -->
<div id="payment-history-section" class="section">
    <div class="content-header">
        <h2 class="content-title">
            <i class="fas fa-history"></i> Tenant Payment History & Tracking
        </h2>
    </div>
    <table class="payment-history-table">
        <thead>
            <tr>
                <th>Tenant Name</th>
                <th>Total Amount Paid</th>
                <th>Last Payment Date</th>
                <th>Move-Out Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch aggregated payment history grouped by tenant with room_number
            $query = "SELECT 
                        ph.tenant_id,
                        ph.tenant_name, 
                        t.apartment AS room_number,  -- Get room number
                        SUM(ph.amount_paid) AS total_amount_paid, 
                        MAX(ph.date_of_payment) AS last_payment_date, 
                        MAX(ph.move_out_date) AS move_out_date
                      FROM payment_history ph
                      JOIN tenants t ON ph.tenant_id = t.id  -- Join with tenants table
                      GROUP BY ph.tenant_id, ph.tenant_name, t.apartment
                      ORDER BY last_payment_date DESC";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $payment_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($payment_history as $history): ?>
                <tr>
                    <td><?php echo htmlspecialchars($history['tenant_name']); ?></td>
                    <td>₱<?php echo number_format($history['total_amount_paid'], 2); ?></td>
                    <td>
                        <button 
                            class="btn btn-primary btn-view-details" 
                            onclick="showPaymentHistoryModal(<?php echo $history['tenant_id']; ?>, '<?php echo htmlspecialchars($history['tenant_name']); ?>', '<?php echo htmlspecialchars($history['room_number']); ?>')">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                    </td>
                    <td><?php echo $history['move_out_date'] ? date('M d, Y', strtotime($history['move_out_date'])) : '—'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<!-- Modal Structure -->
<div id="payment-history-modal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title">Last Payment Details: <span id="modal-tenant-name"></span></h2>
            <button type="button" class="modal-close" onclick="closePaymentHistoryModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="tenant-info">
                <div class="label">Tenant Name:</div>
                <h3 id="modal-tenant-name-body"></h3>
            </div>
            <div class="tenant-info">
                <div class="label">Room Number:</div>
                <h4 id="modal-room-number"></h4>
            </div>
            <div class="payment-summary">
                <div class="total-paid">
                    <h3>₱<span id="modal-total-amount-paid"></span></h3>
                    <div class="label">Total Amount Paid</div>
                </div>
                <div class="last-payment">
                    <h4 id="modal-last-payment-date"></h4>
                    <div class="label">Last Payment</div>
                </div>
            </div>
            
            <div class="payment-history-list">
                <h4>Payment History</h4>
                <ul id="modal-payment-history"></ul>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-close" onclick="closePaymentHistoryModal()">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Fetch Payment History Based on Tenant ID -->
<?php
// Fetch all payment records for each tenant to store in a JavaScript variable
$all_payments_query = "SELECT 
                        tenant_id, 
                        amount_paid, 
                        date_of_payment 
                      FROM payment_history 
                      ORDER BY date_of_payment DESC";
$payments_stmt = $conn->prepare($all_payments_query);
$payments_stmt->execute();
$all_payments = $payments_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<script>
    // Store all payment history in JS object
    const allPayments = <?php echo json_encode($all_payments); ?>;
    const itemsPerPage = 5; // Number of items per page
    let currentPage = 1;
    let currentTenantPayments = [];

    // Show payment history modal
    function showPaymentHistoryModal(tenantId, tenantName, roomNumber) {
        // Reset pagination
        currentPage = 1;
        
        // Get the payment history data for this tenant
        const tenant = <?php echo json_encode($payment_history); ?>.find(history => history.tenant_id == tenantId);
    
        // Update tenant name and room number
        document.getElementById('modal-tenant-name').textContent = tenantName;
        document.getElementById('modal-tenant-name-body').textContent = tenantName;
        document.getElementById('modal-room-number').textContent = roomNumber;

        // Update total amount paid and last payment date
        document.getElementById('modal-total-amount-paid').textContent = parseFloat(tenant.total_amount_paid).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('modal-last-payment-date').textContent = new Date(tenant.last_payment_date).toLocaleString('en-US', {
            month: 'short',
            day: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });

        // Filter payments for selected tenant
        currentTenantPayments = allPayments.filter(payment => payment.tenant_id == tenantId);
        
        // Render the first page
        renderPaymentPage();

        // Show the modal
        document.getElementById('payment-history-modal').classList.add('active');
    }

    // Render paginated payment history
    function renderPaymentPage() {
        let paymentList = document.getElementById('modal-payment-history');
        paymentList.innerHTML = ''; // Clear previous list
        
        const totalPages = Math.ceil(currentTenantPayments.length / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, currentTenantPayments.length);
        
        if (currentTenantPayments.length > 0) {
            // Add the payment items for current page
            for (let i = startIndex; i < endIndex; i++) {
                const payment = currentTenantPayments[i];
                const listItem = document.createElement('li');
                const paymentDate = new Date(payment.date_of_payment);
                
                listItem.innerHTML = `
                    <div class="payment-item">
                        <span class="payment-amount">₱${parseFloat(payment.amount_paid).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                        <span class="payment-date">${paymentDate.toLocaleString('en-US', {
                            month: 'short',
                            day: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        })}</span>
                    </div>
                `;
                paymentList.appendChild(listItem);
            }
            
            // Create pagination controls
            const paginationControls = document.createElement('div');
            paginationControls.className = 'pagination-controls';
            paginationControls.innerHTML = `
                <button ${currentPage === 1 ? 'disabled' : ''} class="page-btn" id="prev-page">
                    <i class="fas fa-chevron-left"></i> Previous
                </button>
                <span class="page-info">Page ${currentPage} of ${totalPages}</span>
                <button ${currentPage === totalPages ? 'disabled' : ''} class="page-btn" id="next-page">
                    Next <i class="fas fa-chevron-right"></i>
                </button>
            `;
            paymentList.appendChild(paginationControls);
            
            // Add event listeners to pagination buttons
            document.getElementById('prev-page')?.addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderPaymentPage();
                }
            });
            
            document.getElementById('next-page')?.addEventListener('click', () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderPaymentPage();
                }
            });
        } else {
            paymentList.innerHTML = '<li>No payment records found.</li>';
        }
    }

    // Close modal
    function closePaymentHistoryModal() {
        document.getElementById('payment-history-modal').classList.remove('active');
    }

    // Close modal if user clicks outside of it
    window.addEventListener('click', function(event) {
        let modal = document.getElementById('payment-history-modal');
        if (event.target == modal) {
            closePaymentHistoryModal();
        }
    });
</script>
<!-- Add this line near the top of your payment_history.php file -->
<link rel="stylesheet" href="./css/payment_history.css">
