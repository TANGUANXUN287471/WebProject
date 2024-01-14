<?php
// Include your database connection file if not already included
include 'dbconnect.php';

echo "Connected to the database successfully!";

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if userID is provided
if (isset($_POST['userID'])) {
    $userID = $_POST['userID'];

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Begin the transaction for atomic operations
    $conn->begin_transaction();

    try {
        // Check if there are bookings made by the user
        $stmtCheckBookings = $conn->prepare("SELECT COUNT(*) as bookingCount FROM `booking` WHERE `userID` = ?");
        $stmtCheckBookings->bind_param("i", $userID);
        $stmtCheckBookings->execute();
        $result = $stmtCheckBookings->get_result();
        $bookingCount = $result->fetch_assoc()['bookingCount'];

        // Close the statement
        $stmtCheckBookings->close();

        if ($bookingCount > 0) {
            // Delete bookings associated with the user from 'booking' table
            $stmtDeleteBookings = $conn->prepare("DELETE FROM `booking` WHERE `userID` = ?");
            $stmtDeleteBookings->bind_param("i", $userID); // Assuming userID is an integer, adjust accordingly

            // Execute the delete query for bookings
            $stmtDeleteBookings->execute();

            // Check if any rows were affected (bookings deleted successfully)
            if ($stmtDeleteBookings->affected_rows > 0) {
                $stmtDeleteBookings->close();
            } else {
                // Rollback the transaction if bookings delete failed
                $conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Failed to delete bookings.']);
                exit();
            }
        }

        // Delete user from 'users' table
        $stmtDeleteUser = $conn->prepare("DELETE FROM `users` WHERE `userID` = ?");
        $stmtDeleteUser->bind_param("i", $userID); // Assuming userID is an integer, adjust accordingly

        // Execute the delete query for user
        $stmtDeleteUser->execute();

        // Check if any rows were affected (user deleted successfully)
        if ($stmtDeleteUser->affected_rows > 0) {
            // Commit the transaction if both deletes were successful
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'User and associated bookings deleted successfully.']);
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
