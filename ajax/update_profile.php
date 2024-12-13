<?php
require_once '../functions.php';
require_once '../config/db.php';
require_once '../classes/Auth.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

if(!$auth->checkAuth()) {
    echo json_encode(['success' => false, 'message' => 'Oturum açmanız gerekiyor']);
    exit;
}

$userId = $_COOKIE['user_id'];
$userQuery = $db->prepare("SELECT * FROM users WHERE id = ?");
$userQuery->execute([$userId]);
$user = $userQuery->fetch(PDO::FETCH_ASSOC);

try {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $dailyCalorieGoal = (int)($_POST['daily_calorie_goal'] ?? 2000);
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    
    // Username kontrolü
    if ($username !== $user['username']) {
        $checkUsername = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $checkUsername->execute([$username, $userId]);
        if ($checkUsername->fetch()) {
            throw new Exception('Bu kullanıcı adı zaten kullanımda');
        }
    }
    
    // Email kontrolü
    if ($email !== $user['email']) {
        $checkEmail = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $checkEmail->execute([$email, $userId]);
        if ($checkEmail->fetch()) {
            throw new Exception('Bu email adresi zaten kullanımda');
        }
    }
    
    // Şifre değişikliği kontrolü
    if ($newPassword) {
        if (!password_verify($currentPassword, $user['password'])) {
            throw new Exception('Mevcut şifreniz hatalı');
        }
        if (strlen($newPassword) < 6) {
            throw new Exception('Yeni şifreniz en az 6 karakter olmalıdır');
        }
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    }
    
    // Güncelleme sorgusu
    $updateQuery = $db->prepare("
        UPDATE users 
        SET username = ?,
            email = ?,
            daily_calorie_goal = ?
            " . ($newPassword ? ", password = ?" : "") . "
        WHERE id = ?
    ");
    
    $params = [$username, $email, $dailyCalorieGoal];
    if ($newPassword) {
        $params[] = $hashedPassword;
    }
    $params[] = $userId;
    
    $updateQuery->execute($params);
    
    echo json_encode([
        'success' => true,
        'message' => 'Profil bilgileriniz güncellendi'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}