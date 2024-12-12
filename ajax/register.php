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

    // Basit validasyon
    if(strlen($data['password']) < 6) {
        throw new Exception('Şifre en az 6 karakter olmalıdır.');
    }

    if($data['password'] !== $data['password_confirm']) {
        throw new Exception('Şifreler eşleşmiyor.');
    }

    $result = $auth->register([
        'username' => $data['username'],
        'email' => $data['email'],
        'password' => $data['password'],
        'full_name' => $data['full_name']
    ]);
    
    echo json_encode($result);

} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}