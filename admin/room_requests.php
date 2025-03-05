<?php
include "fetch.php";
?>

<div id="room-requests-section" class="section">
    <h2>Manage Room Requests</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Room ID</th>
                <th>User ID</th>
                <th>Name</th>
                <th>Pax</th>
                <th>Mode of Payment</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($room_requests as $request): ?>
            <tr>
                <td><?php echo htmlspecialchars($request['id']); ?></td>
                <td><?php echo htmlspecialchars($request['room_id']); ?></td>
                <td><?php echo htmlspecialchars($request['user_id']); ?></td>
                <td><?php echo htmlspecialchars($request['name']); ?></td>
                <td><?php echo htmlspecialchars($request['pax']); ?></td>
                <td><?php echo htmlspecialchars($request['mode_of_payment']); ?></td>
                <td><?php echo htmlspecialchars($request['status']); ?></td>
                <td>
                <?php if ($request['status'] != 'approved' && $request['status'] != 'rejected'): ?>
                    <form method="POST" action="utils/update_room_request.php" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                        <input type="hidden" name="status" value="Approved">
                        <button type="submit" class="btn-primary">Approve</button>
                    </form>
                    <form method="POST" action="utils/update_room_request.php" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                        <input type="hidden" name="status" value="Rejected">
                        <button type="submit" class="btn-danger">Rejected</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>