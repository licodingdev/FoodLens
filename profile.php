<?php
require_once 'functions.php';
error_reporting(E_ALL); ini_set('display_errors', 1);

require_once 'config/db.php';
require_once 'classes/Auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

if(!$auth->checkAuth()) {
    header('Location: login.php');
    exit;
}

// Kullanıcı bilgilerini al
$userId = $_COOKIE['user_id'];
$userQuery = $db->prepare("SELECT * FROM users WHERE id = ?");
$userQuery->execute([$userId]);
$user = $userQuery->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil | FoodLens AI</title>
    
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    
    <!-- Ubuntu Font -->
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Toastify -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col max-w-md mx-auto relative">
        <!-- Header -->
        <header class="relative z-50">
            <div class="safe-area-top"></div>
            <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 px-4 pt-4 pb-6">
                <div class="flex items-center space-x-3">
                    <a href="settings.php" class="w-10 h-10 bg-white/10 backdrop-blur-lg rounded-2xl flex items-center justify-center border border-white/20">
                        <i class="fas fa-arrow-left text-white text-sm"></i>
                    </a>
                    <div>
                        <h1 class="text-white text-xl font-semibold">Profil</h1>
                        <div class="text-blue-100 text-xs mt-0.5">Hesap bilgilerinizi güncelleyin</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 px-4 pt-6 pb-24">
            <form id="profileForm" class="space-y-6">
                <!-- Profil Bilgileri -->
                <div class="bg-white rounded-2xl border border-gray-100 p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Profil Bilgileri</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kullanıcı Adı</label>
                            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" 
                                   class="w-full h-11 px-4 rounded-xl border border-gray-200 focus:border-gray-300 focus:ring-0 text-sm"
                                   placeholder="Kullanıcı adı">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" 
                                   class="w-full h-11 px-4 rounded-xl border border-gray-200 focus:border-gray-300 focus:ring-0 text-sm"
                                   placeholder="E-posta">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Günlük Kalori Hedefi</label>
                            <input type="number" name="daily_calorie_goal" value="<?= htmlspecialchars($user['daily_calorie_goal']) ?>" 
                                   class="w-full h-11 px-4 rounded-xl border border-gray-200 focus:border-gray-300 focus:ring-0 text-sm"
                                   placeholder="Günlük kalori hedefi">
                        </div>
                    </div>
                </div>

                <!-- Şifre Değiştirme -->
                <div class="bg-white rounded-2xl border border-gray-100 p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Şifre Değiştirme</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mevcut Şifre</label>
                            <input type="password" name="current_password" 
                                   class="w-full h-11 px-4 rounded-xl border border-gray-200 focus:border-gray-300 focus:ring-0 text-sm"
                                   placeholder="••••••••">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Yeni Şifre</label>
                            <input type="password" name="new_password" 
                                   class="w-full h-11 px-4 rounded-xl border border-gray-200 focus:border-gray-300 focus:ring-0 text-sm"
                                   placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <!-- Kaydet Butonu -->
                <button type="submit" class="w-full h-11 bg-gray-900 hover:bg-gray-800 text-white rounded-xl text-sm font-medium transition-colors">
                    Değişiklikleri Kaydet
                </button>
            </form>
        </main>

        <!-- Footer Navigation -->
        <?php include 'footer.php'; ?>
    </div>

    <?php if (isset($message)): ?>
    <script>
    function showToast(message, isError = false) {
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "center",
            stopOnFocus: true,
            className: "rounded-lg",
            style: {
                background: isError 
                    ? "linear-gradient(to right, #ef4444, #dc2626)" 
                    : "linear-gradient(to right, #10b981, #059669)",
                boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1)",
                borderRadius: "12px",
                padding: "12px 24px",
                fontSize: "14px",
                fontFamily: "Ubuntu, sans-serif",
                margin: "0 16px",
                maxWidth: "calc(100% - 32px)"
            }
        }).showToast();
    }

    showToast('<?= addslashes($message) ?>', <?= $success ? 'false' : 'true' ?>);
    </script>
    <?php endif; ?>

    <script>
    document.getElementById('profileForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        
        try {
            const response = await fetch('/ajax/update_profile.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            showToast(result.message, !result.success);
            
            if (result.success) {
                // Başarılı güncelleme durumunda sayfayı yenile
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }
        } catch (error) {
            showToast('Bir hata oluştu', true);
            console.error('Error:', error);
        }
    });
    </script>
</body>
</html> 