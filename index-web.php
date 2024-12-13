<?php
require_once 'functions.php';
require_once 'config/db.php';
require_once 'classes/Auth.php';
require_once 'classes/FoodAnalysis.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

if(!$auth->checkAuth()) {
    header('Location: login.php');
    exit;
}

$userId = $_COOKIE['user_id'];
$foodAnalysis = new FoodAnalysis($db);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodLens AI</title>
    
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    
    <!-- Ubuntu Font -->
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Toastify -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <style>
        body {
            font-family: 'Ubuntu', sans-serif;
            background: #f8fafc;
        }
        
        .gradient-border {
            position: relative;
            border-radius: 16px;
            background: white;
        }
        
        .gradient-border::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 18px;
            background: linear-gradient(45deg, #3b82f6, #8b5cf6, #ec4899);
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .gradient-border:hover::before {
            opacity: 1;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
        }
        
        .hover-scale {
            transition: transform 0.2s;
        }
        
        .hover-scale:hover {
            transform: scale(1.02);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar - Daha zengin tasarım -->
        <div class="w-72 bg-white border-r border-gray-200 fixed h-full shadow-lg">
            <div class="p-6">
                <!-- Logo Area -->
                <div class="flex items-center space-x-3 mb-10">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-utensils text-white text-xl"></i>
                    </div>
                    <div>
                        <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 text-transparent bg-clip-text">FoodLens AI</span>
                        <div class="text-xs text-gray-500 mt-0.5">Yapay Zeka Destekli Besin Analizi</div>
                    </div>
                </div>

                <!-- Navigation - Daha zengin -->
                <nav class="space-y-2">
                    <a href="index-web.php" class="flex items-center space-x-3 px-4 py-3.5 rounded-xl bg-gradient-to-r from-gray-900 to-gray-800 text-white shadow-lg">
                        <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center">
                            <i class="fas fa-camera"></i>
                        </div>
                        <div>
                            <span class="font-medium">Analiz</span>
                            <div class="text-xs text-blue-200">Yemek Fotoğrafı Çek</div>
                        </div>
                    </a>
                    
                    <a href="statistics-web.php" class="flex items-center space-x-3 px-4 py-3.5 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                            <i class="fas fa-chart-line text-blue-500"></i>
                        </div>
                        <div>
                            <span class="font-medium">İstatistikler</span>
                            <div class="text-xs text-gray-500">Beslenme Takibi</div>
                        </div>
                    </a>
                    
                    <a href="profile-web.php" class="flex items-center space-x-3 px-4 py-3.5 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center">
                            <i class="fas fa-user text-purple-500"></i>
                        </div>
                        <div>
                            <span class="font-medium">Profil</span>
                            <div class="text-xs text-gray-500">Hesap Bilgileri</div>
                        </div>
                    </a>
                    
                    <a href="settings-web.php" class="flex items-center space-x-3 px-4 py-3.5 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 rounded-lg bg-pink-50 flex items-center justify-center">
                            <i class="fas fa-cog text-pink-500"></i>
                        </div>
                        <div>
                            <span class="font-medium">Ayarlar</span>
                            <div class="text-xs text-gray-500">Tercihler</div>
                        </div>
                    </a>
                </nav>

                <!-- Premium Banner -->
                <div class="mt-8 p-4 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 text-white">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                            <i class="fas fa-crown text-yellow-300"></i>
                        </div>
                        <span class="font-medium">Premium'a Geç</span>
                    </div>
                    <p class="text-sm text-blue-100 mb-3">Sınırsız analiz ve detaylı besin raporları için premium'a geçin.</p>
                    <button class="w-full py-2 bg-white text-indigo-600 rounded-lg text-sm font-medium">
                        Detaylı Bilgi
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 ml-72">
            <!-- Header - Daha zengin -->
            <header class="glass-effect border-b border-gray-200 fixed w-full ml-72 top-0 z-10">
                <div class="px-8 py-4 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Besin Analizi</h1>
                        <p class="text-sm text-gray-500 mt-1">Yemek fotoğrafı yükleyerek besin değerlerini öğrenin</p>
                    </div>
                    
                    <div class="flex items-center space-x-6">
                        <!-- Notifications -->
                        <button class="w-10 h-10 rounded-xl border border-gray-200 flex items-center justify-center relative hover:bg-gray-50">
                            <i class="fas fa-bell text-gray-600"></i>
                            <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        
                        <!-- User Menu -->
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">John Doe</div>
                                <div class="text-xs text-gray-500">Premium Üye</div>
                            </div>
                            <img src="https://ui-avatars.com/api/?name=John+Doe" 
                                 class="w-10 h-10 rounded-xl border border-gray-200" 
                                 alt="Profile">
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Area - Daha zengin -->
            <main class="pt-24 p-8">
                <div class="max-w-5xl mx-auto">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-4 gap-6 mb-8">
                        <div class="gradient-border p-6 hover-scale">
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                                    <i class="fas fa-fire text-blue-500"></i>
                                </div>
                                <span class="text-sm text-gray-500">Günlük Kalori</span>
                            </div>
                            <div class="text-2xl font-bold text-gray-900">2,456</div>
                            <div class="text-sm text-green-500 mt-2">
                                <i class="fas fa-arrow-up"></i> %12 artış
                            </div>
                        </div>
                        
                        <!-- ... Diğer stat kartları ... -->
                    </div>

                    <!-- Upload Area - Daha zengin -->
                    <div class="gradient-border p-8 mb-8">
                        <div class="text-center">
                            <input type="file" id="fileInput" class="hidden" accept="image/*">
                            <label for="fileInput" class="cursor-pointer block">
                                <div class="border-3 border-dashed border-gray-200 rounded-xl p-12 hover:border-blue-400 transition-colors">
                                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-500 rounded-2xl mx-auto mb-6 flex items-center justify-center shadow-lg">
                                        <i class="fas fa-camera text-white text-2xl"></i>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Fotoğraf Yükle</h3>
                                    <p class="text-gray-500">veya sürükleyip bırakın</p>
                                    <div class="mt-4 text-sm text-gray-400">
                                        PNG, JPG veya JPEG • Max 5MB
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Analysis Results - Daha zengin -->
                    <div id="resultArea" class="hidden">
                        <!-- ... Analiz sonuçları ... -->
                    </div>

                    <!-- Recent Analyses - Daha zengin -->
                    <div class="mt-12">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-900">Son Analizler</h2>
                            <a href="#" class="text-blue-500 hover:text-blue-600 text-sm font-medium">
                                Tümünü Gör <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-6">
                            <?php foreach ($analyses as $analysis): ?>
                            <div class="gradient-border overflow-hidden hover-scale">
                                <img src="<?= htmlspecialchars($analysis['image_path']) ?>" 
                                     class="w-full h-48 object-cover" alt="Food">
                                <div class="p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="font-semibold text-gray-900">
                                            <?= htmlspecialchars($analysis['food_name']) ?>
                                        </h3>
                                        <span class="text-xs text-gray-500">
                                            <?= timeAgo($analysis['created_at']) ?>
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-1 bg-gray-50 rounded-lg p-2 text-center">
                                            <div class="text-sm text-gray-500">Kalori</div>
                                            <div class="font-semibold text-gray-900">
                                                <?= round($analysis['calories']) ?> kcal
                                            </div>
                                        </div>
                                        <div class="flex-1 bg-gray-50 rounded-lg p-2 text-center">
                                            <div class="text-sm text-gray-500">Protein</div>
                                            <div class="font-semibold text-gray-900">
                                                <?= round($analysis['protein']) ?>g
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
    // ... (JavaScript kodları aynı kalacak)
    </script>
</body>
</html> 