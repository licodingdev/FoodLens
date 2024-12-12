<?php
session_start();
require_once '../config/db.php';
require_once '../classes/Auth.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();
    $auth = new Auth($db);

    $data = json_decode(file_get_contents("php://input"), true);

    if(!$data) {
        throw new Exception('Invalid input data');
    }

    $result = $auth->login($data['username'], $data['password']);
    
    echo json_encode($result);

} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 