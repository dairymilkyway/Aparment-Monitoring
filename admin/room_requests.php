<?php include "fetch.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Room Requests</title>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="./css/room_requests.css">

    <!-- Font Awesome (for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <div id="room-requests-section" class="section">
        <div class="rooms-header">
            <h2 class="rooms-title"><i class="fas fa-clipboard-list"></i> Manage Room Requests</h2>
        </div>

        <table id="room-requests-table" class="room-requests-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Room</th>
                    <th>Pax</th>
                    <th>Payment Mode</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
    <?php foreach ($room_requests as $request): ?>
        <?php if (strtolower($request['status']) == 'moved out') continue; // Skip 'moved out' rows ?>
        <tr>
            <td><?php echo htmlspecialchars($request['id']); ?></td>
            <td>
                <div class="room-request-user">
                    <div class="room-request-user-avatar">
                        <?php echo strtoupper(substr(htmlspecialchars($request['name']), 0, 1)); ?>
                    </div>
                    <div>
                        <div><?php echo htmlspecialchars($request['name']); ?></div>
                        <small>ID: <?php echo htmlspecialchars($request['user_id']); ?></small>
                    </div>
                </div>
            </td>
            <td class="room-request-room"><?php echo htmlspecialchars($request['room_id']); ?></td>
            <td><?php echo htmlspecialchars($request['pax']); ?></td>
            <td><?php echo htmlspecialchars($request['mode_of_payment']); ?></td>
            <td>
                <?php 
                $statusClass = '';
                switch(strtolower($request['status'])) {
                    case 'approved':
                        $statusClass = 'approved';
                        break;
                    case 'rejected':
                        $statusClass = 'rejected';
                        break;
                    default:
                        $statusClass = 'pending';
                }
                ?>
                <span class="room-request-status <?php echo $statusClass; ?>">
                    <?php echo htmlspecialchars($request['status']); ?>
                </span>
            </td>
            <td class="room-request-actions">
                <?php if ($request['status'] != 'approved' && $request['status'] != 'rejected'): ?>
                    <form method="POST" action="utils/update_room_request.php" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                        <input type="hidden" name="status" value="Approved">
                        <button type="submit" class="room-request-btn approve">Approve</button>
                    </form>
                    <form method="POST" action="utils/update_room_request.php" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                        <input type="hidden" name="status" value="Rejected">
                        <button type="submit" class="room-request-btn reject">Reject</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

        </table>
    </div>

   
</body>
</html>