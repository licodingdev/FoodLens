<?php
header('Content-Type: application/json');

// API Anahtarı
$OPENROUTER_API_KEY = "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";

try {
    // Hata raporlamayı aktif et
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // Dosya kontrolü daha detaylı yapılsın
    if (!isset($_FILES['image'])) {
        throw new Exception('Dosya gönderilmedi');
    }

    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = array(
            UPLOAD_ERR_INI_SIZE => 'Dosya boyutu PHP limitini aşıyor',
            UPLOAD_ERR_FORM_SIZE => 'Dosya boyutu form limitini aşıyor',
            UPLOAD_ERR_PARTIAL => 'Dosya kısmen yüklendi',
            UPLOAD_ERR_NO_FILE => 'Dosya yüklenmedi',
            UPLOAD_ERR_NO_TMP_DIR => 'Geçici klasör bulunamadı',
            UPLOAD_ERR_CANT_WRITE => 'Dosya yazılamadı',
            UPLOAD_ERR_EXTENSION => 'Dosya yükleme PHP tarafından durduruldu',
        );
        throw new Exception('Yükleme hatası: ' . 
            ($uploadErrors[$_FILES['image']['error']] ?? 'Bilinmeyen hata'));
    }

    // Dosya boyutu kontrolü
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    if ($_FILES['image']['size'] > $maxFileSize) {
        throw new Exception('Dosya boyutu çok büyük (max: 5MB)');
    }

    // MIME type kontrolü
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
    finfo_close($finfo);

    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception('Geçersiz dosya tipi. Sadece JPG ve PNG desteklenir.');
    }

    // Base64 dönüşümü
    $imageData = file_get_contents($_FILES['image']['tmp_name']);
    if ($imageData === false) {
        throw new Exception('Görsel okunamadı');
    }
    $base64Image = base64_encode($imageData);

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

    // Base64 yerine URL oluştur
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $imageUrl = $protocol . $_SERVER['HTTP_HOST'] . 
                dirname($_SERVER['REQUEST_URI']) . '/uploads/foods/' . $fileName;

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
                        "text" => "Bu görseldeki yemeği analiz et ve SADECE belirtilen JSON formatında yanıt ver. Başka açıklama ekleme."
                    ],
                    [
                        "type" => "image_url",
                        "image_url" => [
                            "url" => $imageUrl  // Base64 yerine URL kullan
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

    // API yanıtı kontrolü
    if ($response === false) {
        throw new Exception('API yanıtı alınamadı: ' . curl_error($ch));
    }

    $result = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('API yanıtı JSON formatında değil: ' . json_last_error_msg());
    }

    if (!isset($result['choices'][0]['message']['content'])) {
        throw new Exception('API yanıtı beklenen formatta değil');
    }

    // Debug bilgisi ekle
    $debug = [
        'original_response' => $response,
        'parsed_response' => $result,
        'mime_type' => $mimeType,
        'file_size' => $_FILES['image']['size']
    ];

    // AI analizi yap
    $aiContent = $result['choices'][0]['message']['content'];
    $analysisData = json_decode($aiContent, true);

    // Analizi veritabanına kaydet
    require_once 'classes/FoodAnalysis.php';
    $analysis = new FoodAnalysis($db);
    
    $saveResult = $analysis->saveAnalysis(
        $_COOKIE['user_id'], 
        $_FILES['image'],
        $analysisData
    );

    if(!$saveResult['success']) {
        throw new Exception($saveResult['message']);
    }

    // Başarılı yanıt
    echo json_encode([
        'success' => true,
        'message' => 'Analiz başarıyla tamamlandı',
        'data' => $analysisData,
        'analysis_id' => $saveResult['id'],
        'debug' => $debug  // Debug bilgisini ekle
    ]);

} catch (Exception $e) {
    http_response_code(500);
    error_log('API Error: ' . $e->getMessage());  // Hata logla
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file_info' => isset($_FILES['image']) ? [
            'name' => $_FILES['image']['name'],
            'type' => $_FILES['image']['type'],
            'size' => $_FILES['image']['size'],
            'error' => $_FILES['image']['error']
        ] : 'No file data'
    ]);
}