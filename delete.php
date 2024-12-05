<?php

// Database credentials
$servername = "localhost";
$username = "chen";
$password = "password1234";
$dbname = "PiratesDataBase";

// Read the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'])) {
    $id = $data['id'];  // Get the ID from the decoded JSON

    // Create connection to the database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL query to delete the pirate by ID
    $sql = "DELETE FROM Pirates WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Bind the ID parameter
        $stmt->bind_param("i", $id);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete pirate']);
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare query']);
    }

    // Close the connection
    $conn->close();
} else {
    // Return a JSON response indicating failure if ID is not provided
    echo json_encode(['success' => false, 'message' => 'No ID provided']);
}

?>
