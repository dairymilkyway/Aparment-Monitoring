<?php
include "../includes/db.php";

// Fetch all tenants, ensuring no duplicates
$query = "SELECT DISTINCT t.id, t.name, t.apartment, t.contact, t.deleted_at 
          FROM tenants t 
          LEFT JOIN payments p ON t.id = p.tenant_id 
          ORDER BY t.deleted_at DESC, t.name ASC";
$stmt = $conn->prepare($query);
$stmt->execute();
$tenants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="tenants-section" class="section">
    <div class="content-header">
        <h2 class="content-title">
            <i class="fas fa-users"></i> Manage Tenants
        </h2>
    </div>

    <!-- Current Tenants Section -->
    <h3>Current Tenants</h3>
    <div class="table-responsive">
        <table id="current-tenants-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Room</th>
                    <th>Contact Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tenants as $tenant): ?>
                    <?php if (is_null($tenant['deleted_at'])): // Show only current tenants ?>
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
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Previous Tenants Section -->
    <h3>Previous Tenants</h3>
    <div class="table-responsive">
        <table id="previous-tenants-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Room</th>
                    <th>Contact Number</th>
                    <th>Deleted At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tenants as $tenant): ?>
                    <?php if (!is_null($tenant['deleted_at'])): // Show only previous tenants ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tenant['id']); ?></td>
                            <td><?php echo htmlspecialchars($tenant['name']); ?></td>
                            <td><?php echo htmlspecialchars($tenant['apartment']); ?></td>
                            <td><?php echo htmlspecialchars($tenant['contact']); ?></td>
                            <td><?php echo htmlspecialchars($tenant['deleted_at']); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>