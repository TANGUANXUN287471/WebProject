<?php
include 'dbconnect.php';

// Check if the user ID is provided in the request
if (isset($_GET['userID'])) {
    $userID = $_GET['userID'];

    // Query to retrieve booking data for the user
    $sql = "SELECT b.bookID, c.sportName, c.courtNo, b.bookDate, t.startTime, t.endTime
            FROM booking b
            JOIN court c ON b.courtID = c.courtID
            JOIN timeslot t ON b.slotID = t.slotID
            WHERE b.userID = ?
            ORDER BY b.createDate DESC";
    
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    
    $result = $stmt->get_result();

    // Fetch results as an associative array
    $bookings = $result->fetch_all(MYSQLI_ASSOC);

    // Add a new key 'bookingNo' to store the booking number
    foreach ($bookings as $key => $booking) {
        $bookings[$key]['bookingNo'] = $key + 1;
    }

    // Close the statement
    $stmt->close();

    // Return the data as JSON
    header('Content-Type: application/json');
    echo json_encode($bookings);
} else {
    // If user ID is not provided, return an error
    echo json_encode(['error' => 'User ID not provided']);
}

// Close the database connection
$conn->close();
?>
