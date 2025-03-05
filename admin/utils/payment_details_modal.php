<div id="payment-details-modal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title">Payment Details</h2>
            <button type="button" class="modal-close" onclick="closePaymentDetailsModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>
                <strong>Tenant ID:</strong> 
                <span id="modal-tenant-id"></span>
            </p>
            <p>
                <strong>Name:</strong> 
                <span id="modal-tenant-name"></span>
            </p>
            <p>
                <strong>Contact:</strong> 
                <span id="modal-tenant-contact"></span>
            </p>
            <p>
                <strong>Amount to be Paid:</strong> 
                <span>₱<span id="modal-amount"></span></span>
            </p>
            <p>
                <strong>Date Approved:</strong> 
                <span id="modal-date-approved"></span>
            </p>
            <p>
                <strong>Due Date:</strong> 
                <span id="modal-due-date"></span>
            </p>
            <p>
                <strong>Status:</strong> 
                <span id="modal-status"></span>
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-close" onclick="closePaymentDetailsModal()">
                Close
            </button>
        </div>
    </div>
</div>