<?php

// Database connection
$servername = "localhost";
$username = "chen";
$password = "password1234";
$dbname = "PiratesDataBase";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the new pirate data from the request
$data = json_decode(file_get_contents('php://input'), true);
$newPirate = $data['newItem'];

// Prepare the SQL statement to insert the pirate
$stmt = $conn->prepare("INSERT INTO Pirates (`Name`, Bounty, Position, Affiliation, `Devil fruit`, img) VALUES (?, ?, ?, ?, ?, ?)");
if ($stmt === false) {
    http_response_code(500); // Internal server error
    echo json_encode(['error' => 'Database error: ' . $conn->error]);
    return;
}

$devilFruit = isset($newPirate['Devil fruit']) ? (int)$newPirate['Devil fruit'] : 0; // Convert boolean to integer

$stmt->bind_param("sissis", $newPirate['Name'], $newPirate['Bounty'], $newPirate['Position'], $newPirate['Affiliation'], $devilFruit, $newPirate['img']);

// Execute the query
if ($stmt->execute()) {
    $newPirate['id'] = $stmt->insert_id; // Get the newly generated ID
    header('Content-Type: application/json');
    echo json_encode($newPirate);
} else {
    http_response_code(500); // Internal server error
    echo json_encode(['error' => 'Failed to insert pirate: ' . $stmt->error]);
}

$stmt->close();
$conn->close();

?>