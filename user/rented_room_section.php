<?php if ($rented_room): ?>
    <h2>Your Rented Room</h2>
    <div class="room-grid">
        <div class="room-card occupied">
            <div class="room-number"><?php echo htmlspecialchars($rented_room['room_number']); ?></div>
            <div class="room-status">Occupied</div>
        </div>
    </div>
    
    <h2>Submit Maintenance Request</h2>
    <form method="POST" action="">
        <div class="input-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" required placeholder="Describe your maintenance request..."></textarea>
        </div>
        <button type="submit">Submit Request</button>
    </form>
<?php endif; ?>