<?php
include('dbconnect.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $gameType = $_GET['gameType']; // Assuming you pass game type (badminton, tennis, squash) as a parameter

    $sql = "SELECT * FROM game WHERE title = '$gameType'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $games = array();

        while($row = $result->fetch_assoc()) {
            $games[] = $row;
        }

        echo json_encode($games);
    } else {
        echo "No games found";
    }
}

$conn->close();
?>
