<?php
global $conn;
include 'dbconnect.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users from the database
$sql = "SELECT * FROM booking"; // Replace 'users' with your actual table name
$result = $conn->query($sql);

$booking = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $booking[] = $row;
    }
}

// Close connection
$conn->close();

// Return booking as JSON
header('Content-Type: application/json');
echo json_encode($booking);
?>
