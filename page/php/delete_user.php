<?php
// Include your database connection file if not already included
include 'dbconnect.php';

// Check if userID is provided
if (isset($_POST['userID'])) {
    $userID = $_POST['userID'];

    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "dramranc_28");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Begin the transaction for atomic operations
    $conn->begin_transaction();

    try {
        // Delete user from 'users' table
        $stmtDeleteUser = $conn->prepare("DELETE FROM `users` WHERE `userID` = ?");
        $stmtDeleteUser->bind_param("i", $userID); // Assuming userID is an integer, adjust accordingly

        // Execute the delete query
        $stmtDeleteUser->execute();

        // Check if any rows were affected (user deleted successfully)
        if ($stmtDeleteUser->affected_rows > 0) {
            // Delete bookings associated with the user from 'booking' table
            $stmtDeleteBookings = $conn->prepare("DELETE FROM `booking` WHERE `userID` = ?");
            $stmtDeleteBookings->bind_param("i", $userID); // Assuming userID is an integer, adjust accordingly

            // Execute the delete query for bookings
            $stmtDeleteBookings->execute();

            // Check if any rows were affected (bookings deleted successfully)
            if ($stmtDeleteBookings->affected_rows > 0) {
                // Commit the transaction if both deletes were successful
                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'User and associated bookings deleted successfully.']);
            } else {
                // Rollback the transaction if bookings delete failed
                $conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Failed to delete bookings.']);
            }

            $stmtDeleteBookings->close();
        } else {
            // Rollback the transaction if user delete failed
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Failed to delete user.']);
        }

        $stmtDeleteUser->close();
    } catch (Exception $e) {
        // Rollback the transaction if any exception occurred
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
    }

    // Close the database connection
    $conn->close();
} else {
    // Handle the case where userID is not provided
    echo json_encode(['success' => false, 'message' => 'UserID not provided.']);
}
?>
