<div id="room-request-modal" class="modal-overlay">
    <div class="modal">
        <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
        <div class="modal-header">
            <h2 class="modal-title">Request Room <span id="modal-room-number"></span></h2>
        </div>
        <div class="modal-body">
            <div class="room-preview">
                <div class="room-preview-card available">
                    <div class="room-number" id="preview-room-number"></div>
                </div>
                <div class="room-preview-info">
                    <div class="room-preview-status">Available</div>
                    <div class="room-preview-description"><span id="room-description"></span></div>
                    <div class="room-preview-price">Price: ₱<span id="room-price"></span></div>
                </div>
            </div>
            
            <form method="POST" action="">
                <input type="hidden" id="selected-room-id" name="room_id" required>
                <div class="input-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="input-group">
                    <label for="contact">Contact</label>
                    <input type="text" id="contact" name="contact" required>
                </div>
                <div class="input-group">
                    <label for="pax">Number of Person (Pax)</label>
                    <select id="pax" name="pax" required>
                        <option value="1">1 Pax</option>
                        <option value="2">2 Pax</option>
                        <option value="3">3 Pax</option>
                        <option value="4">4 Pax</option>
                        <option value="5">5 Pax</option>
                        <option value="6">6 Pax</option>
                    </select>
                </div>
                <div class="input-group">
                    <label for="mode_of_payment">Mode of Payment</label>
                    <select id="mode_of_payment" name="mode_of_payment" required>
                        <option value="Cash">Cash</option>
                        <option value="Online">Online</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Submit Request</button>
                <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>
</div>