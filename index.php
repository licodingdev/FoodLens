<?php

require_once 'config/db.php';
require_once 'classes/Auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

// Kullanıcı giriş yapmış mı kontrol et
if(!$auth->checkAuth()) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>1FoodLens AI | Yemek Analiz</title>
    
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    
    <!-- Ubuntu Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Base Styles */
        body {
            font-family: 'Ubuntu', sans-serif;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        }

        /* Gradient Backgrounds */
        .header-gradient {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Modern Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 100px;
        }

        /* Modern Card Styles */
        .modern-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 24px -8px rgba(0, 0, 0, 0.05);
        }

        /* Modern Button Styles */
        .modern-button {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(31, 41, 55, 0.15);
        }

        .modern-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(31, 41, 55, 0.25);
        }

        .modern-button:active {
            transform: translateY(0px);
        }

        /* Animations */
        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .header-animate {
            animation: slideDown 0.5s ease-out;
        }

        .footer-animate {
            animation: slideUp 0.5s ease-out;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        /* Loading Animation */
        .loading-ring {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 8px solid #f3f3f3;
            border-top: 8px solid #2563eb;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* iOS Safe Areas */
        .safe-area-top {
            padding-top: env(safe-area-inset-top, 20px);
        }

        .safe-area-bottom {
            padding-bottom: env(safe-area-inset-bottom, 20px);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #666;
        }

        /* Notification Badge */
        .notification-dot {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background-color: #ef4444;
            border-radius: 50%;
            border: 2px solid #1f2937;
        }

        /* Interactive Elements */
        .interactive-hover {
            transition: all 0.2s ease;
        }

        .interactive-hover:active {
            transform: scale(0.95);
        }

        .interactive-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @keyframes float-slow {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }

        @keyframes float-slower {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        @keyframes shine {
            from { transform: translateX(-100%); }
            to { transform: translateX(100%); }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        .animate-float-slow {
            animation: float-slow 4s ease-in-out infinite;
        }

        .animate-float-slower {
            animation: float-slower 5s ease-in-out infinite;
        }

        .animate-shine {
            animation: shine 2s linear infinite;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col max-w-md mx-auto relative">
        <!-- Header -->
        <header class="relative z-50">
            <!-- Status Bar Area -->
            <div class="safe-area-top"></div>
            
            <!-- Header Content -->
            <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 px-4 pt-4 pb-6">
                <!-- Top Section -->
                <div class="flex items-center justify-between mb-8">
                    <!-- Brand -->
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/10 backdrop-blur-lg rounded-2xl flex items-center justify-center border border-white/20 shadow-lg shadow-gray-900/10">
                            <i class="fas fa-utensils text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h1 class="text-white text-xl font-semibold tracking-tight">FoodLens</h1>
                                <span class="bg-white/10 backdrop-blur-sm text-[10px] px-2 py-0.5 rounded-full text-white/90 border border-white/20">AI</span>
                            </div>
                            <div class="flex items-center space-x-1.5 mt-0.5">
                                <div class="flex items-center space-x-1">
                                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                                    <span class="text-blue-100 text-xs">Operasyonel</span>
                                </div>
                                <span class="text-blue-200/30">•</span>
                                <span class="text-blue-100 text-xs">v2.0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-2.5">
                        <button class="relative p-2.5 hover:bg-white/10 rounded-xl transition-colors duration-200">
                            <i class="fas fa-bell text-white/80 text-sm"></i>
                            <span class="absolute top-2 right-2 w-1.5 h-1.5 bg-red-400 rounded-full ring-4 ring-gray-800"></span>
                        </button>
                        <button onclick="window.location.href='premium.php'" class="flex items-center space-x-2 bg-white/10 border border-white/20 backdrop-blur-sm py-1.5 px-3 rounded-xl hover:bg-white/20 transition-all duration-200">
                            <i class="fas fa-crown text-amber-300 text-xs"></i>
                            <span class="text-white text-xs font-medium">Premium</span>
                        </button>
                    </div>
                </div>

                <!-- Stats Section -->
                <div class="grid grid-cols-3 gap-3 mb-8">
                    <?php
                    // Auth sınıfı ile kullanıcı kontrolü
                    if($auth->checkAuth()) {
                        // Kullanıcının ID'sini al
                        $userId = $auth->getUserId();
                        
                        // Toplam analiz sayısı
                        $totalQuery = $db->prepare("SELECT COUNT(*) as total FROM food_analyses WHERE user_id = ?");
                        $totalQuery->execute([$userId]);
                        $totalAnalysis = $totalQuery->fetch(PDO::FETCH_ASSOC)['total'];

                        // Günlük kalori
                        $today = date('Y-m-d');
                        $caloriesQuery = $db->prepare("SELECT SUM(calories) as daily FROM food_analyses WHERE user_id = ? AND DATE(created_at) = ?");
                        $caloriesQuery->execute([$userId, $today]);
                        $dailyCalories = $caloriesQuery->fetch(PDO::FETCH_ASSOC)['daily'] ?? 0;

                        // Aktif gün sayısı
                        $daysQuery = $db->prepare("SELECT COUNT(DISTINCT DATE(created_at)) as days FROM food_analyses WHERE user_id = ?");
                        $daysQuery->execute([$userId]);
                        $activeDays = $daysQuery->fetch(PDO::FETCH_ASSOC)['days'];
                    } else {
                        // Kullanıcı girişi yoksa varsayılan değerler
                        $totalAnalysis = 0;
                        $dailyCalories = 0;
                        $activeDays = 0;
                    }
                    ?>
                    
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-3 border border-white/20 hover:bg-white/20 transition-colors duration-200">
                        <div class="text-blue-100/60 text-xs mb-1">Toplam Analiz</div>
                        <div class="text-white font-semibold flex items-baseline space-x-1">
                            <span class="text-xl"><?= number_format($totalAnalysis, 0, ',', '.') ?></span>
                            <span class="text-blue-100/50 text-xs">adet</span>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-3 border border-white/20 hover:bg-white/20 transition-colors duration-200">
                        <div class="text-blue-100/60 text-xs mb-1">Günlük Kalori</div>
                        <div class="text-white font-semibold flex items-baseline space-x-1">
                            <span class="text-xl"><?= number_format($dailyCalories, 0, ',', '.') ?></span>
                            <span class="text-blue-100/50 text-xs">kcal</span>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-3 border border-white/20 hover:bg-white/20 transition-colors duration-200">
                        <div class="text-blue-100/60 text-xs mb-1">Aktif Gün</div>
                        <div class="text-white font-semibold flex items-baseline space-x-1">
                            <span class="text-xl"><?= number_format($activeDays, 0, ',', '.') ?></span>
                            <span class="text-blue-100/50 text-xs">gün</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="flex items-center justify-between">
                    <!-- Sol taraf - Kategori ikonları -->
                    <div class="flex -space-x-2">
                        <div class="w-8 h-8 rounded-full border-2 border-gray-800 bg-white/10 backdrop-blur-sm flex items-center justify-center">
                            <i class="fas fa-bowl-food text-white text-xs"></i>
                        </div>
                        <div class="w-8 h-8 rounded-full border-2 border-gray-800 bg-white/10 backdrop-blur-sm flex items-center justify-center">
                            <i class="fas fa-apple-whole text-white text-xs"></i>
                        </div>
                        <div class="w-8 h-8 rounded-full border-2 border-gray-800 bg-white/10 backdrop-blur-sm flex items-center justify-center">
                            <i class="fas fa-mug-hot text-white text-xs"></i>
                        </div>
                        <div class="w-8 h-8 rounded-full border-2 border-gray-800 bg-white/10 backdrop-blur-sm flex items-center justify-center group hover:bg-white/20 transition-all duration-200 cursor-pointer">
                            <i class="fas fa-ellipsis text-white text-xs"></i>
                        </div>
                    </div>

                    <!-- Sağ taraf - Yeni Analiz butonu -->
                    <button class="bg-white/10 border border-white/20 backdrop-blur-sm py-2 px-4 rounded-xl flex items-center space-x-2 hover:bg-white/20 transition-all duration-200 group">
                        <i class="fas fa-plus text-white/80 text-xs group-hover:rotate-90 transition-transform duration-200"></i>
                        <span class="text-white text-sm">Yeni Analiz</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 relative px-4 pt-8 pb-24">
            <!-- Upload Section -->
            <div id="uploadSection" class="fade-in">
                <!-- Hero Section - Premium Minimal -->
                <div class="mb-10">
                    <!-- AI Badge -->
                    <div class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-gray-800/5 to-gray-900/5 rounded-lg mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-1 h-1 bg-gradient-to-r from-gray-800 to-gray-900 rounded-full animate-pulse"></span>
                            <span class="bg-gradient-to-r from-gray-800 to-gray-900 bg-clip-text text-transparent text-[10px] font-medium tracking-wide uppercase">Yapay Zeka ile desteklenmektedir.</span>
                        </div>
                    </div>
                    <h1 class="text-lg font-medium text-gray-900 tracking-tight mb-1">
                        Yemeğini Analiz Et
                    </h1>
                    <p class="text-[13px] text-gray-500 font-light">
                        Yapay zeka ile besin değerlerini öğren
                    </p>
                </div>

                <!-- Main Upload Container - Premium Minimal -->
                <div class="bg-gradient-to-b from-white/80 to-white/40 backdrop-blur-sm rounded-[32px] shadow-[0_8px_40px_-12px_rgba(0,0,0,0.05)] mb-10">
                    <div class="p-6">
                        <!-- Stats - Premium Minimal -->
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-6">
                                <div>
                                    <div class="text-base font-medium text-gray-800">2.8k+</div>
                                    <div class="text-[11px] text-gray-400 font-light mt-0.5">Analiz</div>
                                </div>
                                <div>
                                    <div class="text-base font-medium text-gray-800">99%</div>
                                    <div class="text-[11px] text-gray-400 font-light mt-0.5">Doğruluk</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fas fa-star text-amber-400 text-xs"></i>
                                <span class="text-sm font-medium text-gray-800">4.9</span>
                            </div>
                        </div>

                        <!-- Upload Zone - Premium Minimal -->
                        <div class="relative group">
                            <input type="file" 
                                   accept="image/*" 
                                   capture="environment" 
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                                   id="fileInput">
                            
                            <!-- Preview Container -->
                            <div id="imagePreview" class="hidden mb-6">
                                <div class="relative w-full aspect-video rounded-3xl overflow-hidden">
                                    <img src="" alt="Preview" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent"></div>
                                    <button id="removeImage" class="absolute top-3 right-3 w-8 h-8 bg-black/5 backdrop-blur-xl rounded-full flex items-center justify-center text-white/90 hover:bg-black/10 transition-colors">
                                        <i class="fas fa-xmark text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Upload Area -->
                            <div id="uploadArea" class="bg-gray-50/50 rounded-3xl p-8 text-center group-hover:bg-gray-50/80 transition-colors duration-300 relative">
                                <!-- Dashed Border Overlay -->
                                <div class="absolute inset-[2px] rounded-[22px] border-2 border-dashed border-gray-200/80 pointer-events-none"></div>
                                
                                <div class="relative">
                                    <div class="w-14 h-14 mx-auto bg-white/80 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-105 transition-all duration-300 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.02)]">
                                        <i class="fas fa-camera text-gray-300 text-lg group-hover:text-blue-500 transition-colors"></i>
                                    </div>
                                    <p class="text-[13px] text-gray-400 font-light">Fotoğraf Yükle veya Sürükle</p>
                                    <div class="flex items-center justify-center gap-2 mt-3">
                                        <span class="text-[10px] text-gray-400 px-2 py-1 bg-white/80 rounded-full">PNG</span>
                                        <span class="text-[10px] text-gray-400 px-2 py-1 bg-white/80 rounded-full">JPG</span>
                                        <span class="text-[10px] text-gray-400 px-2 py-1 bg-white/80 rounded-full">HEIC</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons - Premium Minimal -->
                        <div class="space-y-3 mt-6">
                            <!-- Analyze Button -->
                            <button id="analyzeBtn" class="w-full h-11 bg-gradient-to-r from-gray-800 to-gray-900 rounded-2xl text-white text-[13px] font-medium transition-all flex items-center justify-center gap-2 hover:shadow-lg hover:shadow-gray-800/20 group">
                                <div class="w-5 h-5 rounded-lg bg-white/10 flex items-center justify-center">
                                    <i class="fas fa-wand-magic-sparkles text-[10px] group-hover:rotate-12 transition-transform"></i>
                                </div>
                                <span>Yapay Zeka ile Analiz Et</span>
                            </button>
                            
                            <button id="cameraBtn" class="w-full h-11 bg-gray-50 hover:bg-gray-100 rounded-2xl text-gray-500 text-[13px] font-medium transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-camera text-[10px]"></i>
                                <span>Kamera ile Çek</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Recent Analysis - Premium Minimal -->
                
            </div>

            <!-- Loading Section -->
            <div id="loadingSection" class="hidden fade-in">
                <div class="min-h-[60vh] flex items-center justify-center">
                    <div class="text-center max-w-sm mx-auto">
                        <!-- Modern Minimal Loading Animation -->
                        

                        <!-- Status Text -->
                        <div class="space-y-3 mb-10">
                            <h3 class="text-base font-medium text-gray-900">
                                Yapay Zeka Analiz Ediyor
                            </h3>
                            <div class="flex items-center justify-center gap-2">
                                <span class="w-1 h-1 bg-blue-500 rounded-full animate-ping"></span>
                                <span class="text-[13px] text-gray-500 font-light" id="loadingStatus">Görsel analiz ediliyor...</span>
                            </div>
                        </div>

                        <!-- Progress Container -->
                        <div class="bg-gray-50/80 rounded-2xl p-6 backdrop-blur-sm relative">
                            <!-- Dashed Border -->
                            <div class="absolute inset-[2px] rounded-xl border-2 border-dashed border-gray-200/80 pointer-events-none"></div>
                            
                            <div class="relative space-y-6">
                                <!-- Progress Bar -->
                                <div class="space-y-2">
                                    <div class="h-1 bg-gray-100 rounded-full overflow-hidden">
                                        <div id="progressBar" class="h-full w-0 bg-gradient-to-r from-gray-800 to-gray-900 rounded-full transition-all duration-300">
                                            <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0"></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between text-[11px] font-light">
                                        <span class="text-gray-400">Görsel analiz ediliyor...</span>
                                        <span class="text-gray-900 font-medium" id="progressText">0%</span>
                                    </div>
                                </div>

                                <!-- Process Steps -->
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="text-center">
                                        <div class="w-8 h-8 bg-white rounded-xl flex items-center justify-center mx-auto mb-2 shadow-[0_2px_12px_-4px_rgba(0,0,0,0.05)]">
                                            <i class="fas fa-image text-[11px] text-gray-400"></i>
                                        </div>
                                        <div class="text-[11px] text-gray-500 font-light">Görsel Analizi</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="w-8 h-8 bg-white rounded-xl flex items-center justify-center mx-auto mb-2 shadow-[0_2px_12px_-4px_rgba(0,0,0,0.05)]">
                                            <i class="fas fa-microchip text-[11px] text-gray-400"></i>
                                        </div>
                                        <div class="text-[11px] text-gray-500 font-light">AI İşlemi</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="w-8 h-8 bg-white rounded-xl flex items-center justify-center mx-auto mb-2 shadow-[0_2px_12px_-4px_rgba(0,0,0,0.05)]">
                                            <i class="fas fa-check text-[11px] text-gray-400"></i>
                                        </div>
                                        <div class="text-[11px] text-gray-500 font-light">Sonuç</div>
                                    </div>
                                </div>

                                <!-- Stats -->
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="text-center">
                                        <div class="text-[13px] font-medium text-gray-900">99.8%</div>
                                        <div class="text-[11px] text-gray-400 font-light">Doğruluk</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-[13px] font-medium text-gray-900" id="analysisTime">0.0 sn</div>
                                        <div class="text-[11px] text-gray-400 font-light">Süre</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-[13px] font-medium text-gray-900">v2.1</div>
                                        <div class="text-[11px] text-gray-400 font-light">Model</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Result Section -->
            <div id="resultSection" class="hidden fade-in">
                <div class="bg-gradient-to-b from-white/80 to-white/40 backdrop-blur-sm rounded-[32px] shadow-[0_8px_40px_-12px_rgba(0,0,0,0.05)] p-8">
                    <!-- Success Icon - Minimal -->
                    <div class="mb-8">
                        <div class="w-10 h-10 mx-auto bg-green-500/5 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                        </div>
                        <div class="mt-3 flex justify-center">
                            <div class="flex items-center gap-1.5 px-3 py-1 bg-green-50 rounded-full">
                                <span class="w-1 h-1 bg-green-500 rounded-full animate-pulse"></span>
                                <span class="text-[11px] text-green-600">Analiz Tamamlandı</span>
                            </div>
                        </div>
                    </div>

                    <!-- Result Content -->
                    <div class="space-y-8">
                        <!-- Food Name and Portion -->
                        <div class="text-center">
                            <h2 class="text-base text-gray-800 mb-1" data-result="food_name">Mercimek Çorbası</h2>
                            <div class="flex items-center justify-center gap-2">
                                <p class="text-[11px] text-gray-400" data-result="portion">1 porsiyon (300ml)</p>
                                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                <p class="text-[11px] text-gray-400">Tabak Doluluk: <span data-result="plate_fullness">75</span>%</p>
                            </div>
                        </div>

                        <!-- Nutrition Grid -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50/50 rounded-2xl p-4 relative">
                                <div class="absolute inset-[2px] rounded-xl border border-dashed border-gray-200/60 pointer-events-none"></div>
                                <div class="relative">
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-6 h-6 rounded-lg bg-blue-500/5 flex items-center justify-center">
                                            <i class="fas fa-fire-flame-simple text-blue-500 text-[10px]"></i>
                                        </div>
                                        <span class="text-[11px] text-gray-400">Kalori</span>
                                    </div>
                                    <div class="text-[15px] text-gray-600" data-nutrient="calories">324 kcal/porsiyon</div>
                                </div>
                            </div>

                            <div class="bg-gray-50/50 rounded-2xl p-4 relative">
                                <div class="absolute inset-[2px] rounded-xl border border-dashed border-gray-200/60 pointer-events-none"></div>
                                <div class="relative">
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-6 h-6 rounded-lg bg-purple-500/5 flex items-center justify-center">
                                            <i class="fas fa-dna text-purple-500 text-[10px]"></i>
                                        </div>
                                        <span class="text-[11px] text-gray-400">Protein</span>
                                    </div>
                                    <div class="text-[15px] text-gray-600" data-nutrient="protein">18.5g</div>
                                </div>
                            </div>

                            <div class="bg-gray-50/50 rounded-2xl p-4 relative">
                                <div class="absolute inset-[2px] rounded-xl border border-dashed border-gray-200/60 pointer-events-none"></div>
                                <div class="relative">
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-6 h-6 rounded-lg bg-amber-500/5 flex items-center justify-center">
                                            <i class="fas fa-bread-slice text-amber-500 text-[10px]"></i>
                                        </div>
                                        <span class="text-[11px] text-gray-400">Karbonhidrat</span>
                                    </div>
                                    <div class="text-[15px] text-gray-600" data-nutrient="carbs">42g</div>
                                </div>
                            </div>

                            <div class="bg-gray-50/50 rounded-2xl p-4 relative">
                                <div class="absolute inset-[2px] rounded-xl border border-dashed border-gray-200/60 pointer-events-none"></div>
                                <div class="relative">
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-6 h-6 rounded-lg bg-rose-500/5 flex items-center justify-center">
                                            <i class="fas fa-droplet text-rose-500 text-[10px]"></i>
                                        </div>
                                        <span class="text-[11px] text-gray-400">Yağ</span>
                                    </div>
                                    <div class="text-[15px] text-gray-600" data-nutrient="fat">12.3g</div>
                                </div>
                            </div>
                        </div>

                        <!-- Ingredients Detail -->
                        <div class="bg-gray-50/50 rounded-2xl p-4 relative">
                            <div class="absolute inset-[2px] rounded-xl border border-dashed border-gray-200/60 pointer-events-none"></div>
                            <div class="relative">
                                <!-- Header -->
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="w-6 h-6 rounded-lg bg-amber-500/5 flex items-center justify-center">
                                        <i class="fas fa-list text-amber-500 text-[10px]"></i>
                                    </div>
                                    <span class="text-[13px] font-medium text-gray-700">Malzeme Detayları</span>
                                </div>

                                <!-- Ingredients List -->
                                <div id="ingredientsDetail" class="space-y-3">
                                    <!-- JavaScript ile doldurulacak -->
                                </div>
                            </div>
                        </div>

                        <!-- Cooking Method -->
                        <div class="flex items-center justify-center gap-2 bg-gray-50/50 rounded-xl py-2">
                            <i class="fas fa-fire-flame-simple text-orange-400 text-xs"></i>
                            <span class="text-[11px] text-gray-600" data-result="cooking_method">Pişirme Yöntemi</span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-2.5 pt-4">
                            <button onclick="location.reload()" class="w-full h-10 bg-gray-900 rounded-xl text-white text-[12px] transition-all flex items-center justify-center gap-2 hover:bg-gray-800">
                                <i class="fas fa-plus text-[10px]"></i>
                                <span>Yeni Analiz</span>
                            </button>
                            
                            <button class="w-full h-10 bg-gray-50 hover:bg-gray-100 rounded-xl text-gray-500 text-[12px] transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-share-nodes text-[10px]"></i>
                                <span>Sonucu Paylaş</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <?php include 'footer.php'; ?>
    </div>

    <!-- Bildirim Çekmecesi -->
    <div id="notificationDrawer" class="fixed inset-0 bg-gray-900/20 backdrop-blur-sm z-50 transition-opacity duration-300 opacity-0 pointer-events-none">
        <div class="absolute right-0 top-0 h-full w-full max-w-md bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 translate-x-full transition-transform duration-300">
            <!-- Header -->
            <div class="safe-area-top"></div>
            <div class="p-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <button id="closeNotifications" class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/10 text-white">
                        <i class="fas fa-xmark text-sm"></i>
                    </button>
                    <h2 class="text-white text-lg font-medium">Bildirimler</h2>
                </div>
                <button class="text-xs text-white/60 hover:text-white transition-colors">Tümünü Okundu İşaretle</button>
            </div>

            <!-- Notifications List -->
            <div class="px-4 py-2">
                <!-- Unread Notification -->
                <div class="bg-white/10 backdrop-blur-sm border border-white/10 rounded-2xl p-3 mb-2">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-star text-blue-400 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <h3 class="text-white text-sm font-medium">Premium'a Özel İndirim</h3>
                                <span class="text-white/40 text-[10px]">2 dk önce</span>
                            </div>
                            <p class="text-white/60 text-xs">Yıllık plana geçişte %40 indirim fırsatını kaçırma!</p>
                        </div>
                    </div>
                </div>

                <!-- Read Notification -->
                <div class="bg-white/5 backdrop-blur-sm border border-white/5 rounded-2xl p-3 mb-2">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-green-500/10 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-chart-simple text-green-400 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <h3 class="text-white/80 text-sm font-medium">Haftalık Rapor</h3>
                                <span class="text-white/40 text-[10px]">2 gün önce</span>
                            </div>
                            <p class="text-white/60 text-xs">Geçen haftaki beslenme analizin hazır!</p>
                        </div>
                    </div>
                </div>

                <!-- Read Notification -->
                <div class="bg-white/5 backdrop-blur-sm border border-white/5 rounded-2xl p-3">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-gift text-amber-400 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <h3 class="text-white/80 text-sm font-medium">Hoş Geldin!</h3>
                                <span class="text-white/40 text-[10px]">1 hafta önce</span>
                            </div>
                            <p class="text-white/60 text-xs">FoodLens AI'ya hoş geldin! Hemen ilk analizini yapmaya başla.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const fileInput = document.getElementById('fileInput');
            const imagePreview = document.getElementById('imagePreview');
            const analyzeBtn = document.getElementById('analyzeBtn');
            const uploadSection = document.getElementById('uploadSection');
            const loadingSection = document.getElementById('loadingSection');
            const resultSection = document.getElementById('resultSection');

            fileInput.addEventListener('change', (e) => {
                if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        // Görsel preview alanını göster
                        imagePreview.querySelector('img').src = e.target.result;
                        imagePreview.classList.remove('hidden');
                        
                        // Upload alanını gizle
                        document.querySelector('#uploadArea').classList.add('hidden');
                    };
                    reader.readAsDataURL(e.target.files[0]);
                }
            });

            // Görseli kaldırma butonu için
            document.querySelector('#removeImage').addEventListener('click', () => {
                // Görseli kaldır
                fileInput.value = '';
                imagePreview.classList.add('hidden');
                imagePreview.querySelector('img').src = '';
                
                // Upload alanını tekrar göster
                document.querySelector('#uploadArea').classList.remove('hidden');
            });

            analyzeBtn.addEventListener('click', async () => {
                if (!fileInput.files || !fileInput.files[0]) {
                    alert('Lütfen bir görsel seçin');
                    return;
                }

                const formData = new FormData();
                formData.append('image', fileInput.files[0]);

                uploadSection.classList.add('hidden');
                loadingSection.classList.remove('hidden');

                // Progress bar ve süre için değişkenler
                const progressBar = document.getElementById('progressBar');
                const progressText = document.getElementById('progressText');
                const analysisTime = document.getElementById('analysisTime');
                let startTime = Date.now();
                let progress = 0;

                // Progress bar animasyonu
                const progressInterval = setInterval(() => {
                    if (progress < 100) {
                        progress += (100 / (8 * 10)); // 8 saniyede 100'e ulaşacak şekilde
                        progressBar.style.width = `${Math.min(progress, 100)}%`;
                        progressText.textContent = `${Math.min(Math.round(progress), 100)}%`;
                    }
                }, 100);

                // Süre sayacı
                const timeInterval = setInterval(() => {
                    const elapsedTime = (Date.now() - startTime) / 1000;
                    analysisTime.textContent = `${elapsedTime.toFixed(1)} sn`;
                }, 100);

                try {
                    const response = await fetch('api.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    // İnterval'leri temizle
                    clearInterval(progressInterval);
                    clearInterval(timeInterval);

                    // Son süreyi göster
                    const finalTime = ((Date.now() - startTime) / 1000).toFixed(1);
                    analysisTime.textContent = `${finalTime} sn`;

                    // Progress bar'ı tamamla
                    progressBar.style.width = '100%';
                    progressText.textContent = '100%';

                    if (!result.success) {
                        throw new Error(result.error);
                    }
                    
                    if (result.data) {
                        updateResults(result.data);
                        loadingSection.classList.add('hidden');
                        resultSection.classList.remove('hidden');
                    } else {
                        throw new Error('API yanıtında data yok');
                    }

                } catch (error) {
                    // İnterval'leri temizle
                    clearInterval(progressInterval);
                    clearInterval(timeInterval);
                    
                    alert('Bir hata oluştu: ' + error.message);
                    loadingSection.classList.add('hidden');
                    uploadSection.classList.remove('hidden');
                }
            });
        });
    </script>

    <!-- JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const notificationBtn = document.querySelector('.fa-bell').parentElement;
        const notificationDrawer = document.getElementById('notificationDrawer');
        const closeBtn = document.getElementById('closeNotifications');
        const drawer = notificationDrawer.querySelector('.absolute');

        function openDrawer() {
            notificationDrawer.classList.remove('pointer-events-none', 'opacity-0');
            setTimeout(() => {
                drawer.classList.remove('translate-x-full');
            }, 0);
        }

        function closeDrawer() {
            drawer.classList.add('translate-x-full');
            setTimeout(() => {
                notificationDrawer.classList.add('pointer-events-none', 'opacity-0');
            }, 300);
        }

        notificationBtn.addEventListener('click', openDrawer);
        closeBtn.addEventListener('click', closeDrawer);
        notificationDrawer.addEventListener('click', (e) => {
            if (e.target === notificationDrawer) {
                closeDrawer();
            }
        });
    });
    </script>

    <script>
    function updateResults(data) {
        try {
            // Temel bilgileri güncelle
            const foodNameEl = document.querySelector('[data-result="food_name"]');
            if (foodNameEl) foodNameEl.textContent = data.food_name;

            const portionEl = document.querySelector('[data-result="portion"]');
            if (portionEl) portionEl.textContent = `${data.portion.count} porsiyon (${data.portion.amount})`;

            const plateFullnessEl = document.querySelector('[data-result="plate_fullness"]');
            if (plateFullnessEl) plateFullnessEl.textContent = data.portion.plate_fullness;

            const cookingMethodEl = document.querySelector('[data-result="cooking_method"]');
            if (cookingMethodEl) cookingMethodEl.textContent = data.cooking_method;

            // Besin değerlerini güncelle
            const caloriesEl = document.querySelector('[data-nutrient="calories"]');
            if (caloriesEl) caloriesEl.textContent = `${data.nutrition.calories} kcal/porsiyon`;

            const proteinEl = document.querySelector('[data-nutrient="protein"]');
            if (proteinEl) proteinEl.textContent = `${data.nutrition.protein}g`;

            const carbsEl = document.querySelector('[data-nutrient="carbs"]');
            if (carbsEl) carbsEl.textContent = `${data.nutrition.carbs}g`;

            const fatEl = document.querySelector('[data-nutrient="fat"]');
            if (fatEl) fatEl.textContent = `${data.nutrition.fat}g`;

            // Malzeme detaylarını güncelle
            const ingredientsContainer = document.getElementById('ingredientsDetail');

            if (ingredientsContainer) {
                ingredientsContainer.innerHTML = '';

                data.ingredients.forEach(ingredient => {
                    const item = document.createElement('div');
                    item.className = 'space-y-1 mb-3';
                    
                    item.innerHTML = `
                        <div class="flex items-center justify-between">
                            <span class="text-[13px] text-gray-700">${ingredient.name}</span>
                            <span class="text-[11px] text-gray-500">${ingredient.amount}</span>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-amber-200 to-amber-300 rounded-full transition-all duration-500" 
                                 style="width: ${ingredient.percentage}%"></div>
                        </div>
                    `;
                    
                    ingredientsContainer.appendChild(item);
                });
            }

            // Sonuç bölümünü göster
            const resultSection = document.getElementById('resultSection');
            if (resultSection) {
                resultSection.classList.remove('hidden');
            }

            // Header stats'ı güncelle
            if (data.stats) {
                // Toplam analiz
                document.querySelector('[data-stat="total_analysis"]').textContent = 
                    new Intl.NumberFormat('tr-TR').format(data.stats.total_analysis);
                
                // Günlük kalori
                document.querySelector('[data-stat="daily_calories"]').textContent = 
                    new Intl.NumberFormat('tr-TR').format(data.stats.daily_calories);
                    
                // Aktif gün
                document.querySelector('[data-stat="active_days"]').textContent = 
                    data.stats.active_days;
            }

        } catch (err) {
            throw new Error('Sonuçlar güncellenirken bir hata oluştu');
        }
    }
    </script>
</body>
</html>