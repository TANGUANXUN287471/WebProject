<?php
// Include the database connection file
global $conn;
include 'dbconnect.php';

// Retrieve form data
$matricNo = $_POST['matricNo'];
$password = $_POST['password'];

// Query the database
$sql = "SELECT userID, matricNo, passwordHash FROM users WHERE matricNo = '$matricNo'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $dbMatricNo = $row['matricNo'];
    $hashed_password = $row['passwordHash'];

    // Verify password
    if (password_verify($password, $hashed_password)) {
        // Password is correct

        // Retrieve the userID based on matricNo
        $loggedInUserID = $row['userID'];

        // Start the session and set the user ID
        session_start();
        $_SESSION['loggedInUserID'] = $loggedInUserID;

        // Check if matricNo is equal to 1
        if ($dbMatricNo == 1) {
            // Redirect to admin.html
            header("Location: ../admin.html");
        } else {
            // Redirect to index.html with the userID as a parameter
            header("Location: ../index.html?userID=$loggedInUserID");
        }
    } else {
        // Password is incorrect
        echo "Invalid password.";
    }
} else {
    // User not found
    echo "Invalid matriculation number.";
}

// Close database connection (you can omit this if you want to reuse the connection in other parts of your script)
$conn->close();
?>
