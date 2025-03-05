<div id="maintenance-section" class="section">
    <h2>Handle Maintenance Requests</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tenant ID</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($maintenance_requests as $request): ?>
            <tr>
                <td><?php echo htmlspecialchars($request['id']); ?></td>
                <td><?php echo htmlspecialchars($request['tenant_id']); ?></td>
                <td><?php echo htmlspecialchars($request['description']); ?></td>
                <td><?php echo htmlspecialchars($request['status']); ?></td>
                <td>
                    <form method="POST" action="update_request.php">
                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                        <select name="status">
                            <option value="Pending" <?php if ($request['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                            <option value="In Progress" <?php if ($request['status'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                            <option value="Completed" <?php if ($request['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>