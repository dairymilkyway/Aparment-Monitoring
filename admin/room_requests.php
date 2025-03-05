<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
include "../includes/db.php";

// Fetch room requests
$query = "SELECT rr.id, rr.status, rr.request_date, u.username, r.room_number 
          FROM room_requests rr 
          JOIN users u ON rr.user_id = u.id 
          JOIN rooms r ON rr.room_id = r.id";
$stmt = $conn->prepare($query);
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];
    $room_id = $_POST['room_id'];

    $query = "UPDATE room_requests SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$status, $request_id]);

    if ($status == 'approved') {
        $query = "UPDATE rooms SET status = 'occupied' WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$room_id]);
    }

    header("Location: room_requests.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Requests</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Room Requests</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Room Number</th>
                    <th>Status</th>
                    <th>Request Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                <tr>
                    <td><?php echo htmlspecialchars($request['id']); ?></td>
                    <td><?php echo htmlspecialchars($request['username']); ?></td>
                    <td><?php echo htmlspecialchars($request['room_number']); ?></td>
                    <td><?php echo htmlspecialchars($request['status']); ?></td>
                    <td><?php echo htmlspecialchars($request['request_date']); ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                            <input type="hidden" name="room_id" value="<?php echo $request['room_id']; ?>">
                            <select name="status">
                                <option value="pending" <?php if ($request['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                <option value="approved" <?php if ($request['status'] == 'approved') echo 'selected'; ?>>Approved</option>
                                <option value="rejected" <?php if ($request['status'] == 'rejected') echo 'selected'; ?>>Rejected</option>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>