<div id="rooms-section" class="section">
    <h2>Manage Rooms</h2>
    
    <!-- Add Room Button -->
    <button class="btn-primary" onclick="openAddRoomModal()">
        <i class="fas fa-plus"></i> Add New Room
    </button>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Room Number</th>
                <th>Status</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rooms as $room): ?>
            <tr>
                <td><?php echo htmlspecialchars($room['id']); ?></td>
                <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                <td><?php echo htmlspecialchars($room['status']); ?></td>
                <td>₱<?php echo htmlspecialchars($room['price']); ?></td>
                <td>
                    <button class="btn-primary" onclick="openEditRoomModal(<?php echo $room['id']; ?>, '<?php echo htmlspecialchars($room['price']); ?>')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn-danger" onclick="confirmDeleteRoom(<?php echo $room['id']; ?>, '<?php echo htmlspecialchars($room['room_number']); ?>')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Add Room Modal -->
    <div id="add-room-modal" class="modal-overlay">
        <div class="modal">
            <button type="button" class="modal-close" onclick="closeAddRoomModal()">&times;</button>
            <div class="modal-header">
                <h2 class="modal-title">Add New Room</h2>
            </div>
            <div class="modal-body">
                <form method="POST" action="utils/add_room.php">
                    <div class="input-group">
                        <label for="room_number">Room Number</label>
                        <input type="text" id="room_number" name="room_number" required>
                    </div>
                    <div class="input-group">
                        <label for="price">Price (₱)</label>
                        <input type="number" id="price" name="price" min="0" step="0.01" required>
                    </div>
                    <button type="submit" class="btn-primary">Add Room</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Room Modal -->
    <div id="edit-room-modal" class="modal-overlay">
        <div class="modal">
            <button type="button" class="modal-close" onclick="closeEditRoomModal()">&times;</button>
            <div class="modal-header">
                <h2 class="modal-title">Edit Room</h2>
            </div>
            <div class="modal-body">
                <form method="POST" action="utils/update_room.php">
                    <input type="hidden" id="edit_room_id" name="room_id">
                    <div class="input-group">
                        <label for="edit_price">Price (₱)</label>
                        <input type="number" id="edit_price" name="price" min="0" step="0.01" required>
                    </div>
                    <button type="submit" class="btn-primary">Update Room</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="delete-room-modal" class="modal-overlay">
        <div class="modal">
            <button type="button" class="modal-close" onclick="closeDeleteRoomModal()">&times;</button>
            <div class="modal-header">
                <h2 class="modal-title">Confirm Delete</h2>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete Room <span id="delete_room_number"></span>?</p>
                <div class="modal-actions">
                    <form method="POST" action="utils/delete_room.php">
                        <input type="hidden" id="delete_room_id" name="room_id">
                        <button type="submit" class="btn-danger">Delete</button>
                        <button type="button" class="btn-secondary" onclick="closeDeleteRoomModal()">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function openAddRoomModal() {
            document.getElementById('add-room-modal').classList.add('active');
        }
        
        function closeAddRoomModal() {
            document.getElementById('add-room-modal').classList.remove('active');
        }
        
        function openEditRoomModal(id, price) {
            document.getElementById('edit_room_id').value = id;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit-room-modal').classList.add('active');
        }
        
        function closeEditRoomModal() {
            document.getElementById('edit-room-modal').classList.remove('active');
        }
        
        function confirmDeleteRoom(id, roomNumber) {
            document.getElementById('delete_room_id').value = id;
            document.getElementById('delete_room_number').textContent = roomNumber;
            document.getElementById('delete-room-modal').classList.add('active');
        }
        
        function closeDeleteRoomModal() {
            document.getElementById('delete-room-modal').classList.remove('active');
        }
        
        // Close modal if user clicks outside of it
        window.onclick = function(event) {
            var addModal = document.getElementById('add-room-modal');
            var editModal = document.getElementById('edit-room-modal');
            var deleteModal = document.getElementById('delete-room-modal');
            
            if (event.target == addModal) {
                closeAddRoomModal();
            }
            
            if (event.target == editModal) {
                closeEditRoomModal();
            }
            
            if (event.target == deleteModal) {
                closeDeleteRoomModal();
            }
        }
    </script>
</div>