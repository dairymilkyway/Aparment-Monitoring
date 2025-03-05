<div id="rooms-section" class="section">
    <h2>Manage Rooms</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Room Number</th>
                <th>Status</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rooms as $room): ?>
            <tr>
                <td><?php echo htmlspecialchars($room['id']); ?></td>
                <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                <td><?php echo htmlspecialchars($room['status']); ?></td>
                <td><?php echo htmlspecialchars($room['price']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>