<?php

// Database credentials
$servername = "localhost";
$username = "chen";
$password = "password1234";
$dbname = "PiratesDataBase";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS PiratesDataBase";
if ($conn->query($sql) === TRUE) {
    // Database created or already exists
} else {
    die("Error ensuring database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// Create Pirates table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS Pirates (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `Name` VARCHAR(30) NOT NULL UNIQUE,
    Bounty VARCHAR(30) NOT NULL,
    Position VARCHAR(50),
    Affiliation VARCHAR(50),
    `Devil fruit` BOOLEAN,
    img VARCHAR(255),
    UNIQUE (Name)  -- Ensures Name is unique
)";
if ($conn->query($sql) !== TRUE) {
    die("Error creating table: " . $conn->error);
}

// Check if the table is empty and reset the AUTO_INCREMENT if needed
$result = $conn->query("SELECT COUNT(*) AS count FROM Pirates");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    // Reset the AUTO_INCREMENT to 1
    $conn->query("ALTER TABLE Pirates AUTO_INCREMENT = 1");

    // Load JSON data and insert into the database
    $jsonFile = 'mycollection.json'; // Path to your JSON file
    if (!file_exists($jsonFile)) {
        die("JSON file not found.");
    }

    $jsonData = file_get_contents($jsonFile);
    $pirates = json_decode($jsonData, true); // Convert JSON to associative array

    if ($pirates === null) {
        die("Failed to decode JSON: " . json_last_error_msg());
    }

    // Prepare the SQL statement for insertion
    $stmt = $conn->prepare("INSERT INTO Pirates (`Name`, Bounty, Position, affiliation, `Devil fruit`, img) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Loop through each pirate and insert into the database only if it doesn't exist
    foreach ($pirates as $pirate) {
        $name = $pirate['Name'];
        $bounty = $pirate['Bounty'];
        $position = $pirate['Position'];
        $affiliation = $pirate['affiliation'];
        $devilFruit = isset($pirate['Devil fruit']) && $pirate['Devil fruit'] ? 1 : 0; // Convert to integer
        $img = $pirate['img'];

        // Check if pirate already exists in the table based on the name
        $checkStmt = $conn->prepare("SELECT id FROM Pirates WHERE `Name` = ?");
        $checkStmt->bind_param("s", $name);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows === 0) {
            // Pirate doesn't exist, so insert it
            $stmt->bind_param("sissis", $name, $bounty, $position, $affiliation, $devilFruit, $img);
            if (!$stmt->execute()) {
                echo "Error inserting $name: " . $stmt->error . "<br>";
            }
        }
        $checkStmt->close();
    }

    $stmt->close();
}

// Fetch all pirates data from the database
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
