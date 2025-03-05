<div id="success-modal" class="modal-overlay">
    <div class="modal success-modal">
        <button type="button" class="modal-close" onclick="closeSuccessModal()">&times;</button>
        <div class="modal-header">
            <h2 class="modal-title">Request Submitted!</h2>
        </div>
        <div class="modal-body">
            <div class="success-icon">✓</div>
            
            <?php if ($success_data && $success_data['type'] == 'room'): ?>
                <p class="success-message-text">Your room request has been submitted successfully</p>
                <div class="success-details">
                    <p><strong>Room Number:</strong> <?php echo htmlspecialchars($success_data['room_number']); ?></p>
                    <p><strong>Price:</strong> ₱<?php echo htmlspecialchars($success_data['room_price']); ?></p>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($success_data['name']); ?></p>
                    <p><strong>Number of Persons:</strong> <?php echo htmlspecialchars($success_data['pax']); ?> Pax</p>
                    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($success_data['mode_of_payment']); ?></p>
                </div>
                <p>Your request will be reviewed by the administrator. You will be notified when your request is approved.</p>
            <?php elseif ($success_data && $success_data['type'] == 'maintenance'): ?>
                <p class="success-message-text">Your maintenance request has been submitted successfully</p>
                <div class="success-details">
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($success_data['description']); ?></p>
                </div>
                <p>Our maintenance team will address your concern as soon as possible.</p>
            <?php endif; ?>
            
            <button type="button" class="btn-primary" onclick="closeSuccessModal(); window.location.href='dashboard.php'">Continue</button>
        </div>
    </div>
</div>