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
    
    // AI'dan gelen yanıtı al
    $aiContent = $result['choices'][0]['message']['content'];
    
    // AI yanıtını JSON'a çevir
    $aiResponse = json_decode($aiContent, true);
    
    // Debug için yanıtı görelim
    echo json_encode([
        'success' => true,
        'debug' => [
            'original_response' => $result,
            'ai_content' => $aiContent,
            'parsed_response' => $aiResponse
        ],
        'data' => $aiResponse
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}