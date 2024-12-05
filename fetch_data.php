<?php

// Database credentials
$servername = "localhost";
$username = "chen";
$password = "password1234";
$dbname = "PiratesDataBase";

// connects  to MySQL and creates the database if it doesn't exist
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS PiratesDataBase";
if ($conn->query($sql) === TRUE) {
    echo "Database ensured successfully.<br>";
} else {
    die("Error ensuring database: " . $conn->error);
}

$conn->close();


// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Check if the table exists, if not, create it
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
    $conn->query("ALTER TABLE Pirates AUTO_INCREMENT = 1");
}

// Load JSON data
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

        if ($stmt->execute()) {
            echo "Inserted: $name<br>";
        } else {
            echo "Error inserting $name: " . $stmt->error . "<br>";
        }
    } else {
        echo "Pirate '$name' already exists in the database.<br>";
    }

    // Close the check statement
    $checkStmt->close();
}

// Close the statement and connection
$stmt->close();
$conn->close();

?>
