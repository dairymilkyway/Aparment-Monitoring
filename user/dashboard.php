<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
}

$user_id = $_SESSION['user_id'];

// Fetch all rooms from the database
$query = "SELECT * FROM rooms";
$stmt = $conn->query($query);
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch the renter's details if they exist
$renter_query = "SELECT r.id AS room_id, r.room_number, rt.name, rt.due_date, rt.date_occupied, rt.number_of_pax 
                 FROM renters rt
                 JOIN rooms r ON rt.room_id = r.id
                 WHERE rt.user_id = ?";
$renter_stmt = $conn->prepare($renter_query);
$renter_stmt->execute([$user_id]);
$renter = $renter_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- Link to the external CSS files -->
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/userdashboard.css">
    <!-- Add just the centering styles -->
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        
        .main-container {
            flex: 1;
            width: 100%;
            max-width: 1350px;
            margin: 20px auto;
            padding: 0 15px;
            display: flex;
            justify-content: center;
        }
        
        .container {
            width: 100%;
            text-align: center;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .room-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, 100px);
            gap: 20px;
            margin-top: 20px;
            justify-content: center;
            width: 100%;
        }
        
        .room-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto;
        }
        
        button {
            margin: 10px auto;
            display: block;
        }
        
        h1, h2 {
            width: 100%;
            text-align: center;
        }
    </style>
</head>
<body>
<?php include "../includes/navbar.php"; ?>

<div class="main-container">
    <div class="container">
        <h1>Welcome, <?php echo isset($renter['name']) ? htmlspecialchars($renter['name']) : 'User'; ?></h1>

        <!-- Show Rented Room Button -->
        <button id="show-rented-room" <?php echo $renter ? '' : 'disabled'; ?>>Show Rented Room</button>

        <h2>Available Rooms</h2>
        <div class="room-grid">
            <?php foreach ($rooms as $room): ?>
                <div class="room-icon 
                    <?php 
                        if ($room['status'] === 'available') {
                            echo 'available';
                        } elseif ($room['status'] === 'occupied' && $renter && $room['id'] == $renter['room_id']) {
                            echo 'occupied';
                        } else {
                            echo 'disabled';
                        }
                    ?>"
                     data-room-id="<?php echo $room['id']; ?>"
                     data-room-number="<?php echo htmlspecialchars($room['room_number']); ?>"
                     data-room-status="<?php echo htmlspecialchars($room['status']); ?>">
                    <?php echo htmlspecialchars($room['room_number']); ?>
                    <span class="tooltip">
                        <?php 
                            if ($room['status'] === 'available') {
                                echo 'Available';
                            } elseif ($room['status'] === 'occupied' && $renter && $room['id'] == $renter['room_id']) {
                                echo 'Your Room';
                            } else {
                                echo 'Occupied';
                            }
                        ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

    <!-- Assign Room Modal -->
    <div id="assign-room-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Assign Room</h2>
            <form id="assign-form" method="POST">
                <input type="hidden" id="room-id" name="room_id">
                <label>Name:</label>
                <input type="text" id="name" name="name" required>
                <label>Due Date:</label>
                <input type="date" id="due-date" name="due_date" required>
                <label>Date Occupied:</label>
                <input type="date" id="date-occupied" name="date_occupied" required>
                <label>Number of Pax:</label>
                <input type="number" id="number-of-pax" name="number_of_pax" min="1" required>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>

    <!-- Show Rented Room Modal -->
    <div id="rented-room-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Your Rented Room</h2>
            <?php if ($renter): ?>
                <p>Room Number: <?php echo htmlspecialchars($renter['room_number']); ?></p>
                <p>Due Date: <?php echo htmlspecialchars($renter['due_date']); ?></p>
                <p>Date Occupied: <?php echo htmlspecialchars($renter['date_occupied']); ?></p>
                <p>Number of Pax: <?php echo htmlspecialchars($renter['number_of_pax']); ?></p>
            <?php else: ?>
                <p>You have not rented any room yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Check Out Modal -->
    <div id="check-out-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Check Out</h2>
            <p>Are you sure you want to check out of your room?</p>
            <button id="confirm-check-out">Yes, Check Out</button>
            <button class="close">Cancel</button>
        </div>
    </div>

    <!-- Success/Error Modal -->
    <div id="message-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="message-title"></h2>
            <p id="message-body"></p>
        </div>
    </div>

    <!-- JavaScript for interactivity -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const assignRoomModal = document.getElementById('assign-room-modal');
            const rentedRoomModal = document.getElementById('rented-room-modal');
            const checkOutModal = document.getElementById('check-out-modal');
            const messageModal = document.getElementById('message-modal');

            const closeModalButtons = document.querySelectorAll('.close');
            const showRentedRoomButton = document.getElementById('show-rented-room');
            const confirmCheckOutButton = document.getElementById('confirm-check-out');
            const roomIcons = document.querySelectorAll('.room-icon');

            // Close any modal when clicking the close button or outside the modal
            closeModalButtons.forEach(button => {
                button.addEventListener('click', () => {
                    assignRoomModal.style.display = 'none';
                    rentedRoomModal.style.display = 'none';
                    checkOutModal.style.display = 'none';
                    messageModal.style.display = 'none';
                });
            });

            window.addEventListener('click', (event) => {
                if (event.target === assignRoomModal) assignRoomModal.style.display = 'none';
                if (event.target === rentedRoomModal) rentedRoomModal.style.display = 'none';
                if (event.target === checkOutModal) checkOutModal.style.display = 'none';
                if (event.target === messageModal) messageModal.style.display = 'none';
            });

            // Show Rented Room Modal
            showRentedRoomButton.addEventListener('click', () => {
                rentedRoomModal.style.display = 'block';
            });

            // Open Assign Room Modal for available rooms
            roomIcons.forEach(icon => {
                icon.addEventListener('click', () => {
                    const roomId = icon.getAttribute('data-room-id');
                    const roomStatus = icon.getAttribute('data-room-status');

                    if (roomStatus === 'available') {
                        document.getElementById('room-id').value = roomId;
                        assignRoomModal.style.display = 'block';
                    } else if (roomStatus === 'occupied' && icon.classList.contains('occupied')) {
                        checkOutModal.style.display = 'block';
                    }
                });
            });

            // Handle Assign Room Form Submission
            document.getElementById('assign-form').addEventListener('submit', (event) => {
                event.preventDefault();

                const formData = new FormData(event.target);
                const data = Object.fromEntries(formData.entries());

                fetch('assign_room.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        document.getElementById('message-title').textContent = 'Success!';
                        document.getElementById('message-body').textContent = 'Room has been assigned successfully.';
                        messageModal.style.display = 'block';
                        setTimeout(() => location.reload(), 2000); // Reload after 2 seconds
                    } else {
                        document.getElementById('message-title').textContent = 'Error!';
                        document.getElementById('message-body').textContent = result.message || 'Failed to assign the room.';
                        messageModal.style.display = 'block';
                    }
                })
                .catch(error => console.error('Error:', error));
            });

            // Handle Check Out Confirmation
            confirmCheckOutButton.addEventListener('click', () => {
                fetch('check_out.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ user_id: <?php echo json_encode($user_id); ?> })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        document.getElementById('message-title').textContent = 'Success!';
                        document.getElementById('message-body').textContent = 'You have successfully checked out.';
                        messageModal.style.display = 'block';
                        setTimeout(() => location.reload(), 2000); // Reload after 2 seconds
                    } else {
                        document.getElementById('message-title').textContent = 'Error!';
                        document.getElementById('message-body').textContent = result.message || 'Failed to check out.';
                        messageModal.style.display = 'block';
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    </script>
</body>
</html>