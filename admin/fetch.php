<?php
include "../includes/db.php";

// Fetch tenant information
$query = "SELECT * FROM tenants";
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