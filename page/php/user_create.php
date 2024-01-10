<?php
// Include the database connection file
global $conn;
include '../php/dbconnect.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Retrieve form data
  $name = $_POST['user_name'];
  $matricNum = $_POST['user_matricNum'];
  $phone = $_POST['user_phone'];
  $email = $_POST['user_email'];
  $password = $_POST['user_password'];
  $confirmPassword = $_POST['confirm_password'];

  // Validate password confirmation
  if ($password !== $confirmPassword) {
    echo "Error: Password and confirm password do not match.";
    exit();
  }

  // Hash the password
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  // Insert user data into the database
  $sql = "INSERT INTO users (matricNo, name, email, phoneNo, passwordHash, regDateTime)
            VALUES ('$matricNum', '$name', '$email', '$phone', '$hashedPassword', NOW())";

  if ($conn->query($sql) === TRUE) {
    // Registration successful
    echo "Registration successful!";

    // Redirect to login_page.html
    header("Location: ../login_page.html");
    exit(); // Ensure that no further output is sent after the redirect header
  } else {
    // Registration failed
    $errorMessage = "Error: Unable to register. Please try again later.";
    error_log($sql . "\n" . $conn->error, 0); // Log the detailed error
    echo $errorMessage;
  }
}

// Close database connection
$conn->close();
?>
