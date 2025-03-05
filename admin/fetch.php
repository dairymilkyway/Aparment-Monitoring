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
$query = "SELECT t.*, rr.contact FROM tenants t LEFT JOIN room_requests rr ON t.apartment = rr.room_id";
$stmt = $conn->prepare($query);
$stmt->execute();
$tenants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch rent payment information
$query = "SELECT p.*, t.name, t.contact FROM payments p JOIN tenants t ON p.tenant_id = t.id";
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