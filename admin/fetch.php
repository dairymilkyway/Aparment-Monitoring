<?php
include "../includes/db.php";

// Fetch total payments
$query = "SELECT SUM(amount) AS total_payments FROM payments";
$stmt = $conn->prepare($query);
$stmt->execute();
$total_payments = $stmt->fetch(PDO::FETCH_ASSOC)['total_payments'];

// Fetch pending payments
$query = "SELECT SUM(amount) AS pending_payments FROM payments WHERE status = 'not paid'";
$stmt = $conn->prepare($query);
$stmt->execute();
$pending_payments = $stmt->fetch(PDO::FETCH_ASSOC)['pending_payments'];

// Fetch paid payments
$query = "SELECT SUM(amount) AS paid_payments FROM payments WHERE status = 'paid'";
$stmt = $conn->prepare($query);
$stmt->execute();
$paid_payments = $stmt->fetch(PDO::FETCH_ASSOC)['paid_payments'];

// Fetch tenant information
$query = "SELECT t.*, rr.contact, t.deleted_at FROM tenants t 
          LEFT JOIN room_requests rr ON t.apartment = rr.room_id";
$stmt = $conn->prepare($query);
$stmt->execute();
$tenants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch rent payment information
$query = "SELECT DISTINCT p.id, 
                 p.amount, 
                 p.status, 
                 t.name, 
                 t.contact, 
                 p.due_date,  -- Get the latest due_date from payments
                 rr.date_approved,
                 r.room_number  -- Get the room_number from rooms
          FROM payments p
          JOIN tenants t ON p.tenant_id = t.id
          JOIN room_requests rr ON t.apartment = rr.room_id
          JOIN rooms r ON rr.room_id = r.id  -- Join rooms to get room_number
          WHERE p.status != 'finalized' 
          AND p.due_date = (
              SELECT MAX(due_date) 
              FROM payments 
              WHERE tenant_id = p.tenant_id
          )
          ORDER BY p.due_date ASC";  // Order by latest due_date for all tenants

$stmt = $conn->prepare($query);
$stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Fetch maintenance requests
$query = "SELECT * FROM maintenance_requests";
$stmt = $conn->prepare($query);
$stmt->execute();
$maintenance_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user information
$query = "SELECT * FROM users";
$stmt = $conn->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch room information
$query = "SELECT * FROM rooms";
$stmt = $conn->prepare($query);
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch room request information
$query = "SELECT * FROM room_requests";
$stmt = $conn->prepare($query);
$stmt->execute();
$room_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>