
<?php
// Include the database connection file
include 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $booking_date = $_POST['booking-date'];
    $booking_time = $_POST['booking-time'];
    $sport_type = $_POST['sport-type'];
    $court_number = $_POST['court-number'];
    $bookID = $_POST['bookID']; // Add this line to get bookID

    


    // Get courtID based on the selected sport type and court number
    $sql_court = "SELECT courtID FROM court WHERE sportName = ? AND courtNo = ?";
    $stmt_court = $conn->prepare($sql_court);
    $stmt_court->bind_param("si", $sport_type, $court_number);
    $stmt_court->execute();
    $result_court = $stmt_court->get_result();

    if ($result_court->num_rows > 0) {
        $row_court = $result_court->fetch_assoc();
        $court_id = $row_court['courtID'];

        // Get slotID based on the selected start time
        $sql_slot = "SELECT slotID FROM timeslot WHERE startTime = ?";
        $stmt_slot = $conn->prepare($sql_slot);
        $stmt_slot->bind_param("s", $booking_time);
        $stmt_slot->execute();
        $result_slot = $stmt_slot->get_result();

        if ($result_slot->num_rows > 0) {
            $row_slot = $result_slot->fetch_assoc();
            $slot_id = $row_slot['slotID'];

            /* Add debugging statements
            echo "1 Court ID: " . $court_id . "<br>";
            echo "Slot ID: " . $slot_id . "<br>";
            echo "Booking Date: " . $booking_date . "<br>";
            echo "Book ID: " . $bookID . "<br>";*/

            // Perform the update query
            $query = "UPDATE booking SET courtID = ?, slotID = ?, bookDate = ? WHERE bookID = ?";

            //echo "SQL Query: " . $query . "<br>";

            $stmt_update = $conn->prepare($query);
            $stmt_update->bind_param("iisi", $court_id, $slot_id, $booking_date, $bookID);

            if ($stmt_update->execute()) {
                echo "Edit successful!！";
            } else {
                echo "Error updating booking: " . $stmt_update->error;
            }
        } else {
            echo "Error: Unable to determine slotID.";
        }
    } else {
        echo "Error: Unable to determine courtID.";
    }
} else {
    echo "Invalid request method";
}

// Close the database connection
$conn->close();
?>
