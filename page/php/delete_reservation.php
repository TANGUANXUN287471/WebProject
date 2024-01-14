<?php
include 'dbconnect.php';

// Retrieve form data
$viewDate = $_POST['view_date'];
$sportType = $_POST['sport_type'];
$selectedSlots = json_decode($_POST['selectedSlots']);
$selectedCourts = json_decode($_POST['selectedCourts']);

// Check if at least one time slot and one court number is selected
if (empty($selectedSlots) || empty($selectedCourts)) {
    $response = array(
        'status' => 'error',
        'message' => 'Please select at least one time slot and one court number.'
    );
    echo json_encode($response);
    exit;
}

// Start the transaction
$conn->begin_transaction();

try {
    // Iterate through selected slots and courts and delete corresponding reservations
    foreach ($selectedSlots as $slotID) {
        foreach ($selectedCourts as $courtID) {
            // Delete reservation based on courtID, slotID, and viewDate
            $sqlDeleteReservation = "DELETE FROM booking WHERE courtID = ? AND slotID = ? AND bookDate = ?";
            $stmtDeleteReservation = $conn->prepare($sqlDeleteReservation);
            $stmtDeleteReservation->bind_param("iis", $courtID, $slotID, $viewDate);

            if (!$stmtDeleteReservation->execute()) {
                throw new Exception("Error deleting reservation: " . $stmtDeleteReservation->error);
            }

            // Check if any rows were affected
            if ($stmtDeleteReservation->affected_rows == 0) {
                throw new Exception("No matching reservation found for deletion.");
            }

            $stmtDeleteReservation->close();
        }
    }

    // If all deletions are successful, commit the transaction
    $conn->commit();

    $response = array(
        'status' => 'success',
        'message' => 'Reservations successfully deleted.'
    );
    echo json_encode($response);
} catch (Exception $e) {
    // If an error occurs, rollback the transaction
    $conn->rollback();

    $response = array(
        'status' => 'error',
        'message' => 'Error deleting reservation from the database.',
        'error_details' => $e->getMessage()
    );
    echo json_encode($response);
}

$conn->close();
?>