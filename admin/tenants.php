<div id="tenants-section" class="section">
    <h2>Manage Tenants</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Apartment</th>
                <th>Contact</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tenants as $tenant): ?>
            <tr>
                <td><?php echo htmlspecialchars($tenant['id']); ?></td>
                <td><?php echo htmlspecialchars($tenant['name']); ?></td>
                <td><?php echo htmlspecialchars($tenant['apartment']); ?></td>
                <td><?php echo htmlspecialchars($tenant['contact']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>