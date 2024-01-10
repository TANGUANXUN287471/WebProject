<?php
// Include the database connection file
global $conn;
include 'dbconnect.php'; // Update this with your actual connection file

// Retrieve parameters from POST request
$sport = $_POST['sport'];
$courtNumber = $_POST['courtNumber'];
$date = $_POST['date'];

// Get courtID based on the selected sport type and court number
$sql_court = "SELECT courtID FROM court WHERE sportName = '$sport' AND courtNo = $courtNumber";
$result_court = $conn->query($sql_court);

if ($result_court->num_rows > 0) {
  $row_court = $result_court->fetch_assoc();
  $courtID = $row_court['courtID'];

  // Get all timeslots for the selected date
  $sql_timeslots = "SELECT slotID, startTime, endTime FROM timeslot";
  $result_timeslots = $conn->query($sql_timeslots);

  if ($result_timeslots->num_rows > 0) {
    $timeslots = array();

    while ($row_timeslot = $result_timeslots->fetch_assoc()) {
      $slotID = $row_timeslot['slotID'];
      $startTime = $row_timeslot['startTime'];
      $endTime = $row_timeslot['endTime'];

      // Check if the slot is occupied
      $sql_availability = "SELECT * FROM booking WHERE courtID = $courtID AND slotID = $slotID AND bookDate = '$date'";
      $result_availability = $conn->query($sql_availability);

      $timeslots[] = array(
        'startTime' => $startTime,
        'endTime' => $endTime,
        'available' => ($result_availability->num_rows === 0)
      );
    }

    // Return the availability status
    header('Content-Type: application/json');
    echo json_encode($timeslots);
  } else {
    echo "Error: Unable to retrieve timeslots.";
  }
} else {
  echo "Error: Unable to determine courtID.";
}

// Close the database connection
$conn->close();
?>
