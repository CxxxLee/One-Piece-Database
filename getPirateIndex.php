<?php
$file = 'mycollection.json';

$index = json_decode(file_get_contents('php://input'), true)['index'] ?? null;
$items = json_decode(file_get_contents($file), true);

if ($index !== null && isset($items[$index])) {
    header('Content-Type: application/json');
    echo json_encode($items[$index]);
} else {
    header('Content-Type: application/json', true, 400);
    echo json_encode(['error' => 'Invalid index']);
}

?>