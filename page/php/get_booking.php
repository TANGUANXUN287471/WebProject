<?php
include('dbconnect.php');

// Get bookID from the URL
$bookID = $_GET['bookID'];

// Prepare and execute SQL query
$sql = "SELECT * FROM booking
        JOIN court ON booking.courtID = court.courtID
        JOIN timeslot ON booking.slotID = timeslot.slotID
        WHERE bookID = $bookID";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch data as an associative array
    $row = $result->fetch_assoc();

    // Debug: Output the date before formatting
    $row['originalDate'] = $row['bookDate'];

    // Format the date to 'yyyy-MM-dd' format
    $row['bookDate'] = date('Y-m-d', strtotime($row['bookDate']));

    // Output data as JSON
    header('Content-Type: application/json');
    echo json_encode($row);
} else {
    // Return an empty JSON object if no matching record is found
    echo json_encode([]);
}

// Close database connection
$conn->close();
?>
