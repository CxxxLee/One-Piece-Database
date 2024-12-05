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

// Check if the 'data' is set
if ($data && isset($data['data'])) {
    // Create connection to the database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare the SQL statement for inserting or updating pirates
    $sql = "INSERT INTO Pirates (`Name`, Bounty, Position, Affiliation, `Devil fruit`, img) 
            VALUES (?, ?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            `Name` = VALUES(`Name`), 
            Bounty = VALUES(Bounty), 
            Position = VALUES(Position), 
            Affiliation = VALUES(Affiliation), 
            `Devil fruit` = VALUES(`Devil fruit`), 
            img = VALUES(img)";

    if ($stmt = $conn->prepare($sql)) {
        // Loop through the pirate data
        foreach ($data['data'] as $pirate) {
            $name = $pirate['Name'];
            $bounty = $pirate['Bounty'];
            $position = $pirate['Position'];
            $affiliation = $pirate['Affiliation'];
            $devilFruit = isset($pirate['Devil fruit']) && $pirate['Devil fruit'] ? 1 : 0;
            $img = $pirate['img'];

            // Bind parameters and execute the statement
            $stmt->bind_param("sssiss", $name, $bounty, $position, $affiliation, $devilFruit, $img);

            if (!$stmt->execute()) {
                echo "Error saving pirate: " . $stmt->error . "<br>";
            }
        }

        echo 'Data successfully saved.';
        
        // Close the prepared statement
        $stmt->close();
    } else {
        echo "Failed to prepare SQL statement: " . $conn->error;
    }

    // Close the connection
    $conn->close();
} else {
    echo 'No data found to save.';
}

?>
