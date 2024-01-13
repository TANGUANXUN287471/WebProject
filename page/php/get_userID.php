<?php
include 'dbconnect.php';

// Check if the book ID is provided in the request
if (isset($_GET['bookID'])) {
    $bookID = $_GET['bookID'];

    // Query to retrieve userID based on bookID
    $sql = "SELECT userID FROM booking WHERE bookID = ?";
    
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bookID);
    $stmt->execute();
    
    $result = $stmt->get_result();

    // Fetch the result as an associative array
    $data = $result->fetch_assoc();

    // Close the statement
    $stmt->close();

    // Return the data as JSON
    header('Content-Type: application/json');
    echo json_encode($data);
} else {
    // If book ID is not provided, return an error
    echo json_encode(['error' => 'Book ID not provided']);
}

// Close the database connection
$conn->close();
?>
