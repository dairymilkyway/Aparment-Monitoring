<div id="tenants-section" class="section">
    <div class="content-header">
        <h2 class="content-title">
            <i class="fas fa-users"></i> Manage Tenants
        </h2>
    </div>
    
    <div class="table-responsive">
        <table id="tenants-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Room</th>
                    <th>Contact Number</th>
                    <th>Actions</th> <!-- Add Actions column -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tenants as $tenant): ?>
                <tr>
                    <td><?php echo htmlspecialchars($tenant['id']); ?></td>
                    <td><?php echo htmlspecialchars($tenant['name']); ?></td>
                    <td><?php echo htmlspecialchars($tenant['apartment']); ?></td>
                    <td><?php echo htmlspecialchars($tenant['contact']); ?></td>
                    <td>
                        <form method="POST" action="utils/kick_tenant.php" style="display:inline;">
                            <input type="hidden" name="tenant_id" value="<?php echo $tenant['id']; ?>">
                            <input type="hidden" name="room_id" value="<?php echo $tenant['apartment']; ?>">
                            <button type="submit" class="btn-delete">Kick</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>