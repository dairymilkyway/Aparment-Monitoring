<div id="payments-section" class="section">
    <h2>Track Rent Payments</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tenant ID</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $payment): ?>
            <tr>
                <td><?php echo htmlspecialchars($payment['id']); ?></td>
                <td><?php echo htmlspecialchars($payment['tenant_id']); ?></td>
                <td><?php echo htmlspecialchars($payment['amount']); ?></td>
                <td><?php echo htmlspecialchars($payment['date']); ?></td>
                <td><?php echo htmlspecialchars($payment['status']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>