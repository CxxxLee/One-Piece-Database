<?php
// Ensure JSON response format
header('Content-Type: application/json');

try {
    $target_dir = "img/uploads/"; // Adjust the directory as needed
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true); // Create directory if it doesn't exist
    }

    // Check if the file was uploaded
    if (isset($_FILES['fileup'])) {
        $file = $_FILES['fileup'];
        $target_file = $target_dir . basename($file["name"]);

        // Handle upload errors
        if ($file["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload error: " . $file["error"]);
        }

        // Move the file to the target directory
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            echo json_encode([
                "success" => true,
                "imagePath" => $target_file
            ]);
        } else {
            throw new Exception("Failed to move uploaded file.");
        }
    } else {
        throw new Exception("No file uploaded.");
    }
} catch (Exception $e) {
    // Return an error response
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>
