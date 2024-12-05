<?php

// Database credentials
$servername = "localhost";
$username = "chen";
$password = "password1234";
$dbname = "PiratesDataBase";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch all pirates data
$sql = "SELECT * FROM Pirates";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch all rows as an associative array
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    // Return the data in JSON format
    echo json_encode($items);
} else {
    echo json_encode(['message' => 'No pirates found.']);
}

// Close the connection
$conn->close();

?>
