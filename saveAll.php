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

    // Loop through each pirate and insert or update in the database
    foreach ($data['data'] as $pirate) {
        $name = $pirate['Name'];
        $bounty = $pirate['Bounty'];
        $position = $pirate['Position'];
        $affiliation = $pirate['Affiliation'];  // Ensure you're using the correct key name
        $devilFruit = isset($pirate['Devil fruit']) && $pirate['Devil fruit'] ? 1 : 0; // Convert to integer
        $img = $pirate['img'];
        $id = $pirate['id'];

        // Check if pirate already exists in the table based on the name
        $checkStmt = $conn->prepare("SELECT id FROM Pirates WHERE `Name` = ?");
        $checkStmt->bind_param("s", $name);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows === 0) {
            // Pirate doesn't exist, so insert it
            $insertStmt = $conn->prepare("INSERT INTO Pirates (`Name`, Bounty, Position, affiliation, `Devil fruit`, img) VALUES (?, ?, ?, ?, ?, ?)");
            $insertStmt->bind_param("sissis", $name, $bounty, $position, $affiliation, $devilFruit, $img);

            if ($insertStmt->execute()) {
                echo "Inserted: $name<br>";
            } else {
                $response['errors'][] = "Error inserting $name: " . $insertStmt->error;
            }
            $insertStmt->close();
        } else {
            // Pirate exists, so update the record
            $updateStmt = $conn->prepare("UPDATE Pirates SET `Name` = ?, Bounty = ?, Position = ?, affiliation = ?, `Devil fruit` = ?, img = ? WHERE id = ?");
            $updateStmt->bind_param("ssssssi", $name, $bounty, $position, $affiliation, $devilFruit, $img, $id);
            

            if ($updateStmt->execute()) {
                echo "Updated: $name<br>";
            } else {
                $response['errors'][] = "Error updating $name: " . $updateStmt->error;
            }
            $updateStmt->close();
        }

        $checkStmt->close(); // Close the check statement after use
    }

    // Close the connection after all pirates are processed
    $conn->close();

    // Indicate success if there were no errors
    if (empty($response['errors'])) {
        $response['success'] = true;
        $response['message'] = 'Data successfully saved.';
    } else {
        $response['message'] = 'Some errors occurred.';
    }
} else {
    $response['message'] = 'No data found to save.';
}

// Return the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
