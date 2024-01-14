<?php
include 'dbconnect.php';

// Retrieve form data
$sportType = $_POST['sport_type'];



// Query the database to get available time slots
$sqlTimeSlots = "SELECT * FROM timeslot";
$resultTimeSlots = $conn->query($sqlTimeSlots);

// Query the database to get available court numbers based on the selected sport type
$sqlCourts = "SELECT * FROM court WHERE sportName = '$sportType'";
$resultCourts = $conn->query($sqlCourts);

// Check if both queries were successful
if ($resultTimeSlots && $resultCourts) {
    // Fetch the results into arrays
    $timeSlots = $resultTimeSlots->fetch_all(MYSQLI_ASSOC);
    $courts = $resultCourts->fetch_all(MYSQLI_ASSOC);

    // Prepare the response
    $response = array(
        'status' => 'success',
        'timeSlots' => $timeSlots,
        'courts' => $courts
    );

    // Send the response as JSON
    echo json_encode($response);
} else {
    // If there was an error in either query
    $response = array(
        'status' => 'error',
        'message' => 'Error fetching data from the database'
    );

    // Send the response as JSON
    echo json_encode($response);
}



$conn->close();
?>