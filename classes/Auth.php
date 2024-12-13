<?php
class Auth {
    private $conn;
    private $table_name = "users";

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function register($data) {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                    (username, email, password, full_name) 
                    VALUES (:username, :email, :password, :full_name)";

            $stmt = $this->conn->prepare($query);
            $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

            $stmt->bindParam(":username", $data['username']);
            $stmt->bindParam(":email", $data['email']);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":full_name", $data['full_name']);

            if($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Kayıt başarılı!'
                ];
            }

            return [
                'success' => false,
                'message' => 'Kayıt sırasında bir hata oluştu.'
            ];

        } catch(PDOException $e) {
            if($e->getCode() == 23000) {
                return [
                    'success' => false,
                    'message' => 'Bu kullanıcı adı veya email zaten kullanımda.'
                ];
            }
            return [
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function login($username, $password) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                    WHERE username = :username OR email = :username";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if($user && password_verify($password, $user['password'])) {
                // Güvenlik için benzersiz token oluştur
                $token = bin2hex(random_bytes(32));
                
                // Token'ı veritabanına kaydet
                $this->saveUserToken($user['id'], $token);

                // Sınırsız süreli cookie'ler (2038 yılına kadar)
                $expire = 2147483647; // Unix timestamp max değeri
                
                // Cookie'leri ayarla (HttpOnly ve Secure flagleri aktif)
                setcookie('user_id', $user['id'], $expire, '/', '', true, true);
                setcookie('username', $user['username'], $expire, '/', '', true, true);
                setcookie('auth_token', $token, $expire, '/', '', true, true);
                setcookie('is_premium', $user['is_premium'], $expire, '/', '', true, true);

                // Son giriş zamanını güncelle
                $this->updateLastLogin($user['id']);

                return [
                    'success' => true,
                    'message' => 'Giriş başarılı!',
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'full_name' => $user['full_name'],
                        'is_premium' => $user['is_premium']
                    ]
                ];
            }

            return [
                'success' => false,
                'message' => 'Kullanıcı adı veya şifre hatalı.'
            ];

        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    private function saveUserToken($userId, $token) {
        $query = "UPDATE " . $this->table_name . " 
                SET auth_token = :token
                WHERE id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->bindParam(":user_id", $userId);
        return $stmt->execute();
    }

    private function updateLastLogin($userId) {
        $query = "UPDATE " . $this->table_name . " 
                SET last_login = NOW() 
                WHERE id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        return $stmt->execute();
    }

    public function checkAuth() {
        if(isset($_COOKIE['user_id']) && isset($_COOKIE['token'])) {
            $userId = $_COOKIE['user_id'];
            $token = $_COOKIE['token'];
            
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? AND token = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$userId, $token]);
            
            return $stmt->rowCount() > 0;
        }
        return false;
    }

    public function logout() {
        // Cookie'leri sil
        setcookie('user_id', '', time() - 3600, '/');
        setcookie('username', '', time() - 3600, '/');
        setcookie('auth_token', '', time() - 3600, '/');
        setcookie('is_premium', '', time() - 3600, '/');

        // Token'ı veritabanından temizle
        if(isset($_COOKIE['user_id'])) {
            $query = "UPDATE " . $this->table_name . " 
                    SET auth_token = NULL 
                    WHERE id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $_COOKIE['user_id']);
            $stmt->execute();
        }

        return [
            'success' => true,
            'message' => 'Çıkış yapıldı'
        ];
    }

    public function getUserId() {
        if(isset($_COOKIE['user_id'])) {
            return $_COOKIE['user_id'];
        }
        return null;
    }
}