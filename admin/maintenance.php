<!-- filepath: /c:/xampp/htdocs/apartment-monitoring/admin/maintenance.php -->
<div id="maintenance-section" class="section maintenance-section">
    <div class="maintenance-header">
        <h2 class="maintenance-title"><i class="fas fa-tools"></i> Manage Maintenance Requests</h2>
    </div>
    
    <?php if (empty($maintenance_requests)): ?>
    <div class="maintenance-empty">
        <i class="fas fa-tools"></i>
        <h3>No maintenance requests found</h3>
        <p>There are currently no maintenance requests to display.</p>
    </div>
    <?php else: ?>
    <table class="maintenance-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Room Number</th> <!-- New column header -->
                <th>Description</th>
                <th>Status</th>
                <th>Date Requested</th>
                <th>Date Resolved</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($maintenance_requests as $request): ?>
            <tr>
                <td><?php echo htmlspecialchars($request['id']); ?></td>
                <td><?php echo htmlspecialchars($request['room_number']); ?></td> <!-- New column data -->
                <td class="maintenance-description"><?php echo htmlspecialchars($request['description']); ?></td>
                <td>
                    <span class="maintenance-status <?php echo strtolower($request['status']); ?>">
                        <?php echo htmlspecialchars($request['status']); ?>
                    </span>
                </td>
                <td><?php echo isset($request['created_at']) ? htmlspecialchars($request['created_at']) : '—'; ?></td>
                <td><?php echo htmlspecialchars($request['date_resolved'] ?: '—'); ?></td>
                <td>
                    <div class="action-buttons">
                        
                    <?php if ($request['status'] != 'resolved'): ?>
                        <form method="post" action="utils/resolve_maintenance.php" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($request['id']); ?>">
                            <input type="hidden" name="date_resolved" value="<?php echo date('Y-m-d H:i:s'); ?>"> <!-- Hidden field for date_resolved -->
                            <button type="submit" class="maintenance-resolve-btn" title="Mark Resolved">
                                <i class="fas fa-check"></i> Resolve
                            </button>
                        </form>
                        <?php else: ?>
                        <button class="action-btn" title="Resolved" disabled>
                            <i class="fas fa-check-circle"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <?php endif; ?>
</div>

<!-- Link to CSS file -->
<link rel="stylesheet" href="./css/maintenance.css">