<?php

// Include the database connection file
global $conn;
include 'dbconnect.php'; // Update this with your actual connection file

// Retrieve form data
$booking_date = $_POST['date'];
$booking_time = $_POST['time'];
$sport_type = $_POST['sport'];
$court_number = $_POST['court'];

// Get slotID based on the selected start time
$sql_slot = "SELECT slotID FROM timeslot WHERE startTime = '$booking_time'";
$result_slot = $conn->query($sql_slot);

if ($result_slot->num_rows > 0) {
  $row_slot = $result_slot->fetch_assoc();
  $slot_id = $row_slot['slotID'];

  // Check if the slot is already booked
  $sql_check_availability = "SELECT COUNT(*) as count FROM booking
                             WHERE courtID IN (SELECT courtID FROM court WHERE sportName = '$sport_type' AND courtNo = $court_number)
                             AND slotID = $slot_id
                             AND bookDate = '$booking_date'";
  $result_availability = $conn->query($sql_check_availability);

  if ($result_availability->num_rows > 0) {
    $row_availability = $result_availability->fetch_assoc();
    $count = $row_availability['count'];

    if ($count == 0) {
      // Slot is available
      echo json_encode(['available' => true]);
    } else {
      // Slot is already booked
      echo json_encode(['available' => false]);
    }
  } else {
    echo json_encode(['error' => 'Error checking availability.']);
  }
} else {
  echo json_encode(['error' => 'Error determining slotID.']);
}

// Close the database connection
$conn->close();

?>
