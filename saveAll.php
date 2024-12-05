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

// Get the updated pirate data from the request
$data = json_decode(file_get_contents('php://input'), true);
$updatedPirates = $data['updatedItems']; // Array of updated pirates

// Prepare the SQL statement to update a pirate
$stmt = $conn->prepare("UPDATE Pirates SET `Name` = ?, Bounty = ?, Position = ?, Affiliation = ?, 
                        `Devil fruit` = ?, img = ? WHERE id = ?");
if ($stmt === false) {
    http_response_code(500); // Internal server error
    echo json_encode(['error' => 'Database error: ' . $conn->error]);
    return;
}

// Initialize variables for logging
$successCount = 0;
$errorCount = 0;
$executedQueries = []; // Array to store executed queries

foreach ($updatedPirates as $updatedPirate) {
    $devilFruit = isset($updatedPirate['Devil fruit']) ? (int)$updatedPirate['Devil fruit'] : 0; // Convert boolean to integer
    $stmt->bind_param(
        "sissisi",
        $updatedPirate['Name'],
        $updatedPirate['Bounty'],
        $updatedPirate['Position'],
        $updatedPirate['Affiliation'],
        $devilFruit,
        $updatedPirate['img'],
        $updatedPirate['id']
    );

    // Generate query for logging
    $executedQueries[] = sprintf(
        "UPDATE Pirates SET `Name` = '%s', Bounty = %d, Position = '%s', Affiliation = '%s', 
         `Devil fruit` = %d, img = '%s' WHERE id = %d;",
        $updatedPirate['Name'],
        $updatedPirate['Bounty'],
        $updatedPirate['Position'],
        $updatedPirate['Affiliation'],
        $devilFruit,
        $updatedPirate['img'],
        $updatedPirate['id']
    );

    if ($stmt->execute()) {
        $successCount++;
    } else {
        $errorCount++;
    }
}

$stmt->close();
$conn->close();

// Return the result, including the queries
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'updated' => $successCount,
    'errors' => $errorCount,
    'queries' => $executedQueries // Include the executed queries in the response
]);
?>
