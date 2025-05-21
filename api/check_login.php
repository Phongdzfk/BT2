<?php
session_start();
header('Content-Type: application/json');

$response = [
    'loggedin' => isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true,
    'username' => isset($_SESSION['username']) ? $_SESSION['username'] : ''
];

echo json_encode($response);
?> 