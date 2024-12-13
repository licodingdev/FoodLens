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
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Desktop Layout -->
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white border-r border-gray-200 fixed h-full">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-8">
                    <div class="w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center">
                        <i class="fas fa-utensils text-white"></i>
                    </div>
                    <span class="text-xl font-semibold">FoodLens AI</span>
                </div>

                <!-- Navigation -->
                <nav class="space-y-1">
                    <a href="index-web.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gray-900 text-white">
                        <i class="fas fa-camera"></i>
                        <span>Analiz</span>
                    </a>
                    <a href="statistics-web.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-chart-line"></i>
                        <span>İstatistikler</span>
                    </a>
                    <a href="profile-web.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user"></i>
                        <span>Profil</span>
                    </a>
                    <a href="settings-web.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-cog"></i>
                        <span>Ayarlar</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 ml-64">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 fixed w-full ml-64 top-0 z-10">
                <div class="px-8 py-4 flex items-center justify-between">
                    <h1 class="text-2xl font-semibold">Besin Analizi</h1>
                    <div class="flex items-center space-x-4">
                        <button class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm">
                            Premium'a Geç
                        </button>
                        <div class="w-10 h-10 bg-gray-100 rounded-full"></div>
                    </div>
                </div>
            </header>

            <!-- Main Area -->
            <main class="pt-20 p-8">
                <div class="max-w-4xl mx-auto">
                    <!-- Upload Area -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-8 mb-8">
                        <div class="text-center">
                            <input type="file" id="fileInput" class="hidden" accept="image/*">
                            <label for="fileInput" class="cursor-pointer">
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 hover:border-gray-400 transition-colors">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                                        <i class="fas fa-camera text-gray-500 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Fotoğraf Yükle</h3>
                                    <p class="text-gray-500 text-sm">veya sürükleyip bırakın</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Analysis Results -->
                    <div id="resultArea" class="hidden">
                        <div class="grid grid-cols-2 gap-8">
                            <!-- Image Preview -->
                            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                                <img id="previewImage" class="w-full h-64 object-cover rounded-xl" src="" alt="Preview">
                            </div>

                            <!-- Analysis Details -->
                            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                                <div id="analysisResults">
                                    <!-- Results will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Analyses -->
                    <div class="mt-8">
                        <h2 class="text-xl font-semibold mb-4">Son Analizler</h2>
                        <div class="grid grid-cols-3 gap-6">
                            <?php
                            $analyses = $foodAnalysis->getUserAnalyses($userId, 6)['data'] ?? [];
                            foreach ($analyses as $analysis):
                            ?>
                            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                                <img src="<?= htmlspecialchars($analysis['image_path']) ?>" 
                                     class="w-full h-40 object-cover" alt="Food">
                                <div class="p-4">
                                    <h3 class="font-medium text-gray-900"><?= htmlspecialchars($analysis['food_name']) ?></h3>
                                    <p class="text-sm text-gray-500 mt-1"><?= timeAgo($analysis['created_at']) ?></p>
                                    <div class="mt-2 text-sm">
                                        <span class="text-gray-900 font-medium"><?= round($analysis['calories']) ?></span>
                                        <span class="text-gray-500">kcal</span>
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
    // ... (mevcut JavaScript kodları aynı kalacak)
    </script>
</body>
</html> 