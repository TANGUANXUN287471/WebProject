<?php
// Assuming you have a database connection established
include('dbconnect.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get sportName and bookingDate from the query parameters
$sportName = $_GET['sportName'];
$bookingDate = $_GET['bookingDate'];

// Perform a query to get the schedule for the selected sport and date
$query = "SELECT timeslot.startTime, timeslot.endTime,
                booking.courtID, booking.bookDate, users.name AS bookedBy
          FROM booking
          LEFT JOIN timeslot ON booking.slotID = timeslot.slotID
          LEFT JOIN users ON booking.userID = users.userID
          WHERE booking.courtID IN (
                SELECT courtID
                FROM court
                WHERE sportName = '$sportName'
          ) AND booking.bookDate = '$bookingDate'";

$result = $conn->query($query);

// Check if the query was successful
if ($result) {
    $schedule = array();

    // Fetch data from the result set
    while ($row = $result->fetch_assoc()) {
        $schedule[] = $row;
    }

    // Return the schedule as JSON
    echo json_encode($schedule);
} else {
    // Return an error message if the query fails
    echo json_encode(array("error" => "Error fetching schedule"));
}

// Close the database connection
$conn->close();
?>

