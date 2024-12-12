<?php
class FoodAnalysis {
    private $conn;
    private $table_name = "food_analyses";
    private $upload_path = "uploads/foods/";

    public function __construct($db) {
        $this->conn = $db;
        
        // Upload klasörünü oluştur
        if (!file_exists($this->upload_path)) {
            mkdir($this->upload_path, 0777, true);
        }
    }

    public function saveAnalysis($userId, $imageFile, $analysisData) {
        try {
            // Görsel kaydetme
            $fileName = uniqid() . '_' . time() . '.' . pathinfo($imageFile['name'], PATHINFO_EXTENSION);
            $filePath = $this->upload_path . $fileName;
            
            if (!move_uploaded_file($imageFile['tmp_name'], $filePath)) {
                throw new Exception('Görsel yüklenemedi');
            }

            // Veritabanına kaydetme
            $query = "INSERT INTO " . $this->table_name . "
                    (user_id, image_path, food_name, portion_amount, portion_count, 
                     calories, protein, carbs, fat, ingredients, confidence_score)
                    VALUES 
                    (:user_id, :image_path, :food_name, :portion_amount, :portion_count,
                     :calories, :protein, :carbs, :fat, :ingredients, :confidence_score)";

            $stmt = $this->conn->prepare($query);

            // Ingredients array'ini JSON'a çevir
            $ingredients = json_encode($analysisData['ingredients'], JSON_UNESCAPED_UNICODE);

            // Bind values
            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":image_path", $filePath);
            $stmt->bindParam(":food_name", $analysisData['food_name']);
            $stmt->bindParam(":portion_amount", $analysisData['portion']['amount']);
            $stmt->bindParam(":portion_count", $analysisData['portion']['count']);
            $stmt->bindParam(":calories", $analysisData['nutrition']['calories']);
            $stmt->bindParam(":protein", $analysisData['nutrition']['protein']);
            $stmt->bindParam(":carbs", $analysisData['nutrition']['carbs']);
            $stmt->bindParam(":fat", $analysisData['nutrition']['fat']);
            $stmt->bindParam(":ingredients", $ingredients);
            $stmt->bindParam(":confidence_score", $analysisData['confidence_score']);

            if($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Analiz başarıyla kaydedildi',
                    'id' => $this->conn->lastInsertId()
                ];
            }

            throw new Exception('Kayıt sırasında bir hata oluştu');

        } catch(Exception $e) {
            // Hata durumunda görseli sil
            if(isset($filePath) && file_exists($filePath)) {
                unlink($filePath);
            }

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getUserAnalyses($userId, $limit = 10, $offset = 0) {
        try {
            $query = "SELECT * FROM " . $this->table_name . "
                    WHERE user_id = :user_id
                    ORDER BY created_at DESC
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
            $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
            $stmt->execute();

            $analyses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // JSON string'i array'e çevir
            foreach($analyses as &$analysis) {
                $analysis['ingredients'] = json_decode($analysis['ingredients'], true);
            }

            return [
                'success' => true,
                'data' => $analyses
            ];

        } catch(Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getAnalysis($id, $userId) {
        try {
            $query = "SELECT * FROM " . $this->table_name . "
                    WHERE id = :id AND user_id = :user_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":user_id", $userId);
            $stmt->execute();

            $analysis = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$analysis) {
                throw new Exception('Analiz bulunamadı');
            }

            // JSON string'i array'e çevir
            $analysis['ingredients'] = json_decode($analysis['ingredients'], true);

            return [
                'success' => true,
                'data' => $analysis
            ];

        } catch(Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function deleteAnalysis($id, $userId) {
        try {
            // Önce görseli bul
            $query = "SELECT image_path FROM " . $this->table_name . "
                    WHERE id = :id AND user_id = :user_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":user_id", $userId);
            $stmt->execute();

            $analysis = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$analysis) {
                throw new Exception('Analiz bulunamadı');
            }

            // Görseli sil
            if(file_exists($analysis['image_path'])) {
                unlink($analysis['image_path']);
            }

            // Kaydı sil
            $query = "DELETE FROM " . $this->table_name . "
                    WHERE id = :id AND user_id = :user_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":user_id", $userId);

            if($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Analiz başarıyla silindi'
                ];
            }

            throw new Exception('Silme işlemi sırasında bir hata oluştu');

        } catch(Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
} 