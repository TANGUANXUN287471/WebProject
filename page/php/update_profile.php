<?php
include 'dbconnect.php';

// Get user ID, new phone number, and new email from the POST request
$userID = $_POST['user_id'];
$newPhoneNo = $_POST['phoneNo'];
$newEmail = $_POST['email'];

// Validate the input if needed

// Prepare the update statement
$updateQuery = "UPDATE users
                SET phoneNo='$newPhoneNo', email='$newEmail'
                WHERE userID=$userID";

// Execute the update operation
if ($conn->query($updateQuery) === TRUE) {
    $response['success'] = true;
    $response['message'] = "User profile updated successfully";
    $response['data'] = [
        'phoneNo' => $newPhoneNo,
        'email' => $newEmail
    ];
} else {
    $response['success'] = false;
    $response['message'] = "Update failed: " . $conn->error;
}

// Return a JSON response
header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>
