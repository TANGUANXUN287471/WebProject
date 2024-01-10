<?php
include('dbconnect.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $gameID = $_GET['gameID']; // Assuming you pass game ID as a parameter

    $sql = "SELECT * FROM game WHERE gameID = '$gameID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $game = $result->fetch_assoc();
        echo json_encode($game);
    } else {
        echo "Game not found";
    }
}

$conn->close();
?>
