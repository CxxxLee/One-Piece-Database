<?php
// Database credentials
$servername = "localhost";
$username = "chen";
$password = "password1234";
$dbname = "PiratesDataBase";

// Gets raw data from the request
$input = file_get_contents('php://input');

// Decodes JSON data
$data = json_decode($input, true);

// Prepare a response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => [],
];

// Check if the 'data' is set
if ($data && isset($data['data'])) {
    // Create connection to the database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        $response['message'] = "Connection failed: " . $conn->connect_error;
        echo json_encode($response); // Output the error as JSON
        exit;
    }

    // Begin transaction for better performance
    $conn->begin_transaction();

    // Prepare update statement
    $updateStmt = $conn->prepare("UPDATE Pirates SET `Name` = ?, Bounty = ?, Position = ?, affiliation = ?, `Devil fruit` = ?, img = ? WHERE id = ?");
    $updateStmt->bind_param("ssssssi", $name, $bounty, $position, $affiliation, $devilFruit, $img, $id);

    foreach ($data['data'] as $pirate) {
        $name = $pirate['Name'];
        $bounty = $pirate['Bounty'];
        $position = $pirate['Position'];
        $affiliation = $pirate['Affiliation'];  // Ensure you're using the correct key name
        $devilFruit = isset($pirate['Devil fruit']) && $pirate['Devil fruit'] ? 1 : 0; // Convert to integer
        $img = $pirate['img'];
        $id = $pirate['id'];

        // Execute the update statement
        if (!$updateStmt->execute()) {
            $response['errors'][] = "Error updating $name (ID $id): " . $updateStmt->error;
        }
    }

    // Close the update statement
    $updateStmt->close();

    // Commit transaction if no errors
    if (empty($response['errors'])) {
        $conn->commit();
        $response['success'] = true;
        $response['message'] = 'Data successfully saved.';
    } else {
        $conn->rollback(); // Rollback transaction if errors occurred
        $response['message'] = 'Some errors occurred during saving.';
    }

    // Close the database connection
    $conn->close();
} else {
    $response['message'] = 'No data found to save.';
}

// Return the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
