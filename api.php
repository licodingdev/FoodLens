<?php
header('Content-Type: application/json');
require_once 'config/db.php';
require_once 'classes/Auth.php';

// Session başlat
session_start();
$_SESSION['api_errors'] = []; // Hata array'ini başlat

// API Anahtarı
$OPENROUTER_API_KEY = "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";

try {
    // Görsel yükleme kontrolü
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Görsel yükleme hatası: ' . ($_FILES['image']['error'] ?? 'Dosya yok'));
    }

    // Görsel tipini kontrol et
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $fileType = $_FILES['image']['type'];
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception('Geçersiz dosya tipi. Sadece JPG ve PNG desteklenir.');
    }

    // Cookie ve auth kontrolü
    if (!isset($_COOKIE['user_id']) || !isset($_COOKIE['auth_token'])) {
        throw new Exception('Kullanıcı girişi gerekli');
    }

    // Veritabanı bağlantısı
    $database = new Database();
    $db = $database->getConnection();
    if (!$db) {
        throw new Exception('Veritabanı bağlantısı kurulamadı');
    }

    // Auth token kontrolü
    $auth = new Auth($db);
    if (!$auth->checkAuth()) {
        throw new Exception('Geçersiz oturum');
    }

    // Dosyayı kaydet
    $uploadDir = 'uploads/foods/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
    $uploadPath = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        throw new Exception('Dosya yüklenemedi');
    }

    // URL oluştur
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $imageUrl = $protocol . $_SERVER['HTTP_HOST'] . 
                dirname($_SERVER['REQUEST_URI']) . '/' . $uploadPath;

    // API isteği için data
    $data = [
        "model" => "openai/gpt-4-vision-preview",
        "messages" => [
            [
                "role" => "system",
                "content" => $systemPrompt
            ],
            [
                "role" => "user",
                "content" => [
                    [
                        "type" => "text",
                        "text" => "Bu görseldeki yemeği analiz et ve SADECE belirtilen JSON formatında yanıt ver."
                    ],
                    [
                        "type" => "image_url",
                        "image_url" => [
                            "url" => $imageUrl
                        ]
                    ]
                ]
            ]
        ]
    ];

    // cURL isteği
    $ch = curl_init('https://openrouter.ai/api/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $OPENROUTER_API_KEY,
            'HTTP-Referer: http://localhost:8080'  // Referer eklendi
        ]
    ]);

    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        throw new Exception('cURL Error: ' . curl_error($ch));
    }
    
    curl_close($ch);

    // API yanıtını parse et
    $result = json_decode($response, true);
    
    if (!isset($result['choices'][0]['message']['content'])) {
        throw new Exception('API yanıtı beklenen formatta değil');
    }

    // AI'dan gelen yanıtı al ve JSON'a çevir
    $aiContent = $result['choices'][0]['message']['content'];
    $analysisData = json_decode($aiContent, true);

    if (!$analysisData) {
        throw new Exception('AI yanıtı JSON formatında değil');
    }

    // Malzemeleri JSON'a çevir
    $ingredients = json_encode($analysisData['ingredients'] ?? [], JSON_UNESCAPED_UNICODE);

    // Veritabanına kaydet
    $sql = "INSERT INTO food_analyses (
        user_id, 
        image_path, 
        food_name, 
        portion_amount, 
        portion_count, 
        calories, 
        protein, 
        carbs, 
        fat, 
        ingredients, 
        confidence_score
    ) VALUES (
        :user_id,
        :image_path,
        :food_name,
        :portion_amount,
        :portion_count,
        :calories,
        :protein,
        :carbs,
        :fat,
        :ingredients,
        :confidence_score
    )";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        'user_id' => $_COOKIE['user_id'],
        'image_path' => $uploadPath,
        'food_name' => $analysisData['food_name'] ?? '',
        'portion_amount' => $analysisData['portion']['amount'] ?? '',
        'portion_count' => $analysisData['portion']['count'] ?? 1,
        'calories' => intval($analysisData['nutrition']['calories'] ?? 0),
        'protein' => floatval($analysisData['nutrition']['protein'] ?? 0),
        'carbs' => floatval($analysisData['nutrition']['carbs'] ?? 0),
        'fat' => floatval($analysisData['nutrition']['fat'] ?? 0),
        'ingredients' => $ingredients,
        'confidence_score' => intval($analysisData['confidence_score'] ?? 0)
    ]);

    $analysisId = $db->lastInsertId();

    // Başarılı yanıt
    echo json_encode([
        'success' => true,
        'message' => 'Analiz başarıyla tamamlandı ve kaydedildi',
        'data' => $analysisData,
        'analysis_id' => $analysisId,
        'debug' => [
            'image_url' => $imageUrl,
            'raw_response' => $response
        ]
    ]);

} catch (Exception $e) {
    // Hatayı session'a kaydet
    $_SESSION['api_errors'][] = [
        'time' => date('Y-m-d H:i:s'),
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ];

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}