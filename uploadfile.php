<?php

// You should have file_uploads = On in C:\xampp\php\php.ini (if you have xampp)

$target_dir = "uploads/"; // directory where the file will be uploaded
$target_file = $target_dir . basename($_FILES["fileup"]["name"]);

$uploadOk = 1;
$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

// Check if the file is an image or not
if (isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileup"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }
}

// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}

// Check file size (max size = 500KB)
if ($_FILES["fileup"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}

// Only allow certain file formats (JPG, PNG, JPEG)
if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png") {
    echo "Sorry, only JPG, JPEG, and PNG files are allowed.";
    $uploadOk = 0;
}

// If there were no errors, move the file
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
} else {
    if (move_uploaded_file($_FILES["fileup"]["tmp_name"], $target_file)) {
        // Image uploaded successfully, return image path
        echo json_encode(["success" => true, "imagePath" => $target_file]);
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>
