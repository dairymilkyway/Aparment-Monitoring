<div id="maintenance-section" class="section">
    <h2>Manage Maintenance Requests</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Description</th>
                <th>Status</th>
                <th>Date Resolved</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($maintenance_requests as $request): ?>
            <tr>
                <td><?php echo htmlspecialchars($request['id']); ?></td>
                <td><?php echo htmlspecialchars($request['description']); ?></td>
                <td><?php echo htmlspecialchars($request['status']); ?></td>
                <td><?php echo htmlspecialchars($request['date_resolved']); ?></td>
                <td>
                    <?php if ($request['status'] != 'resolved'): ?>
                    <form method="post" action="utils/resolve_maintenance.php">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($request['id']); ?>">
                        <button type="submit">Resolved</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>