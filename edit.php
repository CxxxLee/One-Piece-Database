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

if (isset($_POST['id']) && isset($_POST['newItem'])) {
    $id = $_POST['id'];
    $newItem = $_POST['newItem'];

    // Prepare the SQL statement to update the pirate by ID
    $stmt = $conn->prepare("UPDATE Pirates SET `Name` = ?, Bounty = ?, Position = ?, affiliation = ?, `Devil fruit` = ?, img = ? WHERE id = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters to the SQL query
    $stmt->bind_param("ssssis", $newItem['Name'], $newItem['Bounty'], $newItem['Position'], $newItem['affiliation'], $newItem['Devil fruit'], $newItem['img'], $id);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Pirate data updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update pirate data.']);
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();

?>
