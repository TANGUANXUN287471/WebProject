<?php
// Include your database connection file
include('db_connection.php');

// Function to sanitize user input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $userID = sanitizeInput($_POST["userID"]); // Assuming you have a way to get the user ID
    $title = sanitizeInput($_POST["title"]);
    $playerLimit = sanitizeInput($_POST["playerLimit"]);
    $type = sanitizeInput($_POST["type"]);
    $bookID = sanitizeInput($_POST["bookID"]);

    // Additional validation if needed

    // Insert into the database
    $sql = "INSERT INTO games (userID, title, player_limit, type, bookID) VALUES ('$userID', '$title', '$playerLimit', '$type', '$bookID')";

    if ($conn->query($sql) === TRUE) {
        // Game creation successful
        $response = array("status" => "success", "message" => "Game created successfully");
        echo json_encode($response);
    } else {
        // Error in game creation
        $response = array("status" => "error", "message" => "Error creating game: " . $conn->error);
        echo json_encode($response);
    }

    // Close the database connection
    $conn->close();
} else {
    // Invalid request method
    $response = array("status" => "error", "message" => "Invalid request method");
    echo json_encode($response);
}
?>
