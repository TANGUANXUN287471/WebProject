global$conn; <!-- view_reservations.php -->

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reservation Table</title>
  <link rel="stylesheet" href="path/to/bootstrap.min.css"> <!-- Include Bootstrap CSS -->
  <style>
    body {
      padding: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: center;
    }

    th {
      background-color: #f2f2f2;
    }

    td.available {
      background-color: #00FF00 !important; /* Green for available */
    }

    td.reserved {
      background-color: #FF0000 !important; /* Red for reserved */
    }
  </style>
</head>

<body>

<?php
include 'dbconnect.php';

// Retrieve form data
$viewDate = $_POST['view_date'];
$sportType = $_POST['sport_type'];

// Query the database
$sql = "SELECT t.startTime, c.courtNo, u.matricNo
            FROM timeslot t
            CROSS JOIN court c
            LEFT JOIN booking b ON t.slotID = b.slotID AND c.courtID = b.courtID AND b.bookDate = '$viewDate'
            LEFT JOIN users u ON b.userID = u.userID
            WHERE c.sportName = '$sportType'
            ORDER BY c.courtNo, t.startTime";

$result = $conn->query($sql);

if ($result) {
    $startTimes = array();
    $reservationData = array();

    // Collect reservation data
    while ($row = $result->fetch_assoc()) {
        $courtNo = $row['courtNo'];
        $startTime = $row['startTime'];
        $matricNo = empty($row['matricNo']) ? 'Available' : $row['matricNo'];
        $reservationData[$courtNo][$startTime] = $matricNo;
        $startTimes[] = $startTime;
    }

    echo '<table class="table table-bordered">
        <thead>
            <tr>
                <th>Court</th>';

    // Generate headers with startTime
    $startTimes = array_unique($startTimes);
    foreach ($startTimes as $startTime) {
        echo '<th>' . $startTime . '</th>'; // Display startTime as headers
    }

    echo '</tr>
        </thead>
        <tbody>';

    // Generate rows with court numbers and reservations
    for ($i = 1; $i <= 8; $i++) {
        echo '<tr>';
        echo '<td>Court ' . $i . '</td>'; // Display courtNo in the first column

        foreach ($startTimes as $startTime) {
            echo '<td';

            // Check if a reservation exists for the specific time, court, and date
            if (isset($reservationData[$i][$startTime]) && $reservationData[$i][$startTime] !== 'Available') {
                echo ' class="reserved">' . $reservationData[$i][$startTime];
            } else {
                echo ' class="available">Available';
            }

            echo '</td>';
        }

        echo '</tr>';
    }

    echo '</tbody>
    </table>';
} else {
    echo 'Error: ' . $conn->error;
}

$conn->close();
?>
