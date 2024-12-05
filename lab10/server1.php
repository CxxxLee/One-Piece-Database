<?php
header('Content-Type: application/json');

// Check if the JSON array is received in POST request
if (!isset($_POST['jsonArray'])) {
    echo json_encode(["error" => "No data received"]);
    exit;
}

// Decode JSON array received from the client
$array = json_decode($_POST['jsonArray'], true);

if (!is_array($array) || empty($array)) {
    echo json_encode(["error" => "Invalid array"]);
    exit;
}

// Functions to calculate required values
function calculate_average($array) {
    return array_sum($array) / count($array);
}

function calculate_median($array) {
    sort($array);
    $count = count($array);
    $middle = floor($count / 2);
    return ($count % 2) ? $array[$middle] : ($array[$middle - 1] + $array[$middle]) / 2;
}

function calculate_std_dev($array) {
    $mean = calculate_average($array);
    $variance = array_reduce($array, fn($carry, $item) => $carry + pow($item - $mean, 2), 0) / count($array);
    return sqrt($variance);
}

function calculate_min_max($array) {
    return ["min" => min($array), "max" => max($array)];
}

// Perform calculations
$average = calculate_average($array);
$median = calculate_median($array);
$std_dev = calculate_std_dev($array);
$min_max = calculate_min_max($array);

// Prepare response as JSON
$response = [
    "average" => $average,
    "median" => $median,
    "std_dev" => $std_dev,
    "min" => $min_max["min"],
    "max" => $min_max["max"]
];

echo json_encode($response);
?>
