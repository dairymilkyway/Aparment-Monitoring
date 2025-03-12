<!-- filepath: /c:/xampp/htdocs/apartment-monitoring/admin/rooms.php -->
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
                <th>User ID</th>
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
                <td><?php echo htmlspecialchars($room['user_id']); ?></td>
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
    <div id="add-room-modal" class="room-modal-overlay">
        <div class="room-modal">
            <button type="button" class="room-modal-close" onclick="closeAddRoomModal()">&times;</button>
            <div class="room-modal-header">
                <h2 class="room-modal-title">Add New Room</h2>
            </div>
            <div class="room-modal-body">
                <form method="POST" action="utils/add_room.php">
                    <div class="room-input-group">
                        <label for="room_number">Room Number</label>
                        <input type="text" id="room_number" name="room_number" required>
                    </div>
                    <div class="room-input-group">
                        <label for="price">Price (₱)</label>
                        <input type="number" id="price" name="price" min="0" step="0.01" required>
                    </div>
                    <div class="room-modal-actions">
                        <button type="submit" class="room-btn-primary">Add Room</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Room Modal -->
    <div id="edit-room-modal" class="room-modal-overlay">
        <div class="room-modal">
            <button type="button" class="room-modal-close" onclick="closeEditRoomModal()">&times;</button>
            <div class="room-modal-header">
                <h2 class="room-modal-title">Edit Room</h2>
            </div>
            <div class="room-modal-body">
                <form method="POST" action="utils/update_room.php">
                    <input type="hidden" id="edit_room_id" name="room_id">
                    <div class="room-input-group">
                        <label for="edit_price">Price (₱)</label>
                        <input type="number" id="edit_price" name="price" min="0" step="0.01" required>
                    </div>
                    <div class="room-modal-actions">
                        <button type="submit" class="room-btn-primary">Update Room</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="delete-room-modal" class="room-modal-overlay">
        <div class="room-modal">
            <button type="button" class="room-modal-close" onclick="closeDeleteRoomModal()">&times;</button>
            <div class="room-modal-header">
                <h2 class="room-modal-title">Confirm Delete</h2>
            </div>
            <div class="room-modal-body">
                <p>Are you sure you want to delete Room <span id="delete_room_number"></span>?</p>
                <form method="POST" action="utils/delete_room.php">
                    <input type="hidden" id="delete_room_id" name="room_id">
                    <div class="room-modal-actions">
                        <button type="submit" class="room-btn-danger">Delete</button>
                        <button type="button" class="room-btn-secondary" onclick="closeDeleteRoomModal()">Cancel</button>
                    </div>
                </form>
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

<link rel="stylesheet" href="./css/rooms.css">