<?php
include 'dbconnect.php';

// Fixed userID for this scenario
$userID = 1;

// Retrieve form data
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

// Assuming you have a way to obtain the booking date
$bookDate = $_POST['view_date'];

// Prepare and execute the SQL INSERT statement
try {
    $conn->begin_transaction();  // Start a transaction to ensure data consistency

    foreach ($selectedSlots as $slotID) {
        foreach ($selectedCourts as $courtID) {
            $createDate = date('Y-m-d H:i:s');  // Get the current date and time

            $sql = "INSERT INTO booking (userID, courtID, slotID, bookDate, createDate)
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iiiss', $userID, $courtID, $slotID, $bookDate, $createDate);
            $stmt->execute();
            $stmt->close();
        }
    }

    $conn->commit();  // Commit the transaction if all queries are successful

    $response = array(
        'status' => 'success',
        'message' => 'Booking successfully added.'
    );
    echo json_encode($response);
} catch (Exception $e) {
    $conn->rollback();  // Rollback the transaction if an error occurs
    $response = array(
        'status' => 'error',
        'message' => 'Error adding booking to the database.',
        'error_details' => $e->getMessage()
    );
    echo json_encode($response);
} finally {
    $conn->close();
}
?>