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

// Premium durumunu kontrol et
$isPremium = isset($user['subscription_end']) && strtotime($user['subscription_end']) > time();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayarlar | FoodLens AI</title>
    
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
                    <div class="w-10 h-10 bg-white/10 backdrop-blur-lg rounded-2xl flex items-center justify-center border border-white/20">
                        <i class="fas fa-gear text-white text-sm"></i>
                    </div>
                    <div>
                        <h1 class="text-white text-xl font-semibold">Ayarlar</h1>
                        <div class="text-blue-100 text-xs mt-0.5">Hesap ve uygulama ayarları</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 px-4 pt-6 pb-24">
            <!-- Account Section -->
            <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-4">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <div>
                            <div class="text-gray-900 font-medium"><?= htmlspecialchars($user['email']) ?></div>
                            <div class="text-gray-400 text-sm">
                                <?= $isPremium ? 'Premium Üye' : 'Standart Üye' ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($isPremium): ?>
                        <div class="px-3 py-1 bg-amber-100 rounded-lg">
                            <div class="text-amber-600 text-xs font-medium">
                                <?= date('d.m.Y', strtotime($user['subscription_end'])) ?>'e kadar
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Account Settings -->
                <div class="space-y-3">
                    <a href="profile.php" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                                <i class="fas fa-id-card text-blue-500 text-xs"></i>
                            </div>
                            <span class="text-gray-600">Profil Bilgileri</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                    </a>
                    
                    <a href="notifications.php" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center">
                                <i class="fas fa-bell text-purple-500 text-xs"></i>
                            </div>
                            <span class="text-gray-600">Bildirim Ayarları</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                    </a>

                    <?php if (!$isPremium): ?>
                        <a href="premium.php" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center">
                                    <i class="fas fa-crown text-amber-500 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Premium'a Geç</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- App Settings -->
            <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-4">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Uygulama</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-moon text-gray-400 text-xs"></i>
                            </div>
                            <span class="text-gray-600">Karanlık Mod</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gray-800"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-language text-gray-400 text-xs"></i>
                            </div>
                            <span class="text-gray-600">Dil</span>
                        </div>
                        <div class="text-sm text-gray-400">Türkçe</div>
                    </div>
                </div>
            </div>

            <!-- Help & Support -->
            <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-4">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Yardım & Destek</h3>
                <div class="space-y-3">
                    <a href="privacy.php" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-shield text-gray-400 text-xs"></i>
                            </div>
                            <span class="text-gray-600">Gizlilik Politikası</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                    </a>

                    <a href="terms.php" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-file-contract text-gray-400 text-xs"></i>
                            </div>
                            <span class="text-gray-600">Kullanım Koşulları</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                    </a>

                    <a href="support.php" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-headset text-gray-400 text-xs"></i>
                            </div>
                            <span class="text-gray-600">Destek</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="space-y-3">
                <button onclick="logout()" class="w-full p-3 text-center text-red-500 font-medium bg-red-50 hover:bg-red-100 rounded-xl transition-colors">
                    Çıkış Yap
                </button>
                
                <button onclick="confirmDeleteAccount()" class="w-full p-3 text-center text-gray-400 font-medium hover:text-red-500 rounded-xl transition-colors text-sm">
                    Hesabı Sil
                </button>
            </div>
        </main>

        <!-- Footer Navigation -->
        <?php include 'footer.php'; ?>
    </div>

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

    function logout() {
        // Cookie'leri temizle
        document.cookie = "user_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "auth_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        
        // Login sayfasına yönlendir
        window.location.href = 'login.php';
    }

    function confirmDeleteAccount() {
        if (confirm('Hesabınızı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
            fetch('delete_account.php', {
                method: 'POST',
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Hesabınız başarıyla silindi');
                    setTimeout(() => {
                        logout();
                    }, 2000);
                } else {
                    showToast('Bir hata oluştu: ' + data.error, true);
                }
            })
            .catch(error => {
                showToast('Bir hata oluştu', true);
                console.error('Error:', error);
            });
        }
    }
    </script>
</body>
</html> 