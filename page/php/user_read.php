<?php
// Include the database connection file
include 'dbconnect.php'; // Update this with your actual connection file

// Check if user_id is set in the POST data
if (isset($_POST['user_id'])) {
    // Retrieve user_id from POST
    $user_id = $_POST['user_id'];

    // SQL query to fetch user information based on user_id
    $sql = "SELECT * FROM users WHERE userID = $user_id";
    $result = $conn->query($sql);

    // Check if the query was successful
    if ($result) {
        // Check if user exists
        if ($result->num_rows > 0) {
            // Fetch user data
            $row = $result->fetch_assoc();

            // Set content type to JSON
            header('Content-Type: application/json');

            // Output user information as JSON
            echo json_encode($row);
        } else {
            echo 'User not found';
        }
    } else {
        echo 'Error: ' . $conn->error;
    }
} else {
    echo 'UserID not provided in the POST data';
}

// Close the database connection
$conn->close();
?>
