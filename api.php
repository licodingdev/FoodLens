<?php
header('Content-Type: application/json');

// API Anahtarı
$OPENROUTER_API_KEY = "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";

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

try {
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

    // System prompt'u daha spesifik hale getirelim
    $systemPrompt = "Sen bir yemek analiz uzmanısın. Görüntüdeki yemeği analiz edip SADECE aşağıdaki JSON formatında yanıt vermelisin. 
    Başka bir açıklama veya metin EKLEME, SADECE JSON döndür:

    {
        \"food_name\": \"[yemek adı]\",
        \"portion\": {
            \"amount\": \"[miktar] gr/ml\",
            \"count\": [sayı]
        },
        \"nutrition\": {
            \"calories\": [kalori sayısı],
            \"protein\": [protein miktarı],
            \"carbs\": [karbonhidrat miktarı],
            \"fat\": [yağ miktarı]
        },
        \"ingredients\": [\"malzeme1\", \"malzeme2\", ...],
        \"confidence_score\": [0-100 arası sayı]
    }";

    // API isteği için data
    $data = [
        "model" => "openai/gpt-4o-mini",
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
            'Authorization: Bearer ' . $OPENROUTER_API_KEY
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

    // Veritabanına kaydet
    require_once 'config/db.php';
    $database = new Database();
    $db = $database->getConnection();

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

    // Session'dan user_id'yi al
    session_start();
    $userId = $_SESSION['user_id'] ?? throw new Exception('Kullanıcı girişi gerekli');

    // Malzemeleri JSON'a çevir
    $ingredients = json_encode($analysisData['ingredients'] ?? [], JSON_UNESCAPED_UNICODE);

    $stmt->execute([
        'user_id' => $userId,
        'image_path' => $uploadPath,
        'food_name' => $analysisData['food_name'] ?? '',
        'portion_amount' => $analysisData['portion']['amount'] ?? '',
        'portion_count' => $analysisData['portion']['count'] ?? 1,
        'calories' => $analysisData['nutrition']['calories'] ?? 0,
        'protein' => $analysisData['nutrition']['protein'] ?? 0,
        'carbs' => $analysisData['nutrition']['carbs'] ?? 0,
        'fat' => $analysisData['nutrition']['fat'] ?? 0,
        'ingredients' => $ingredients,
        'confidence_score' => $analysisData['confidence_score'] ?? 0
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
    error_log('Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}