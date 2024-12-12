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
                                    <span class="text-blue-100 text-xs">Online</span>
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
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-3 border border-white/20 hover:bg-white/20 transition-colors duration-200">
                        <div class="text-blue-100/60 text-xs mb-1">Toplam Analiz</div>
                        <div class="text-white font-semibold flex items-baseline space-x-1">
                            <span class="text-xl">2,845</span>
                            <span class="text-blue-100/50 text-xs">adet</span>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-3 border border-white/20 hover:bg-white/20 transition-colors duration-200">
                        <div class="text-blue-100/60 text-xs mb-1">Günlük Kalori</div>
                        <div class="text-white font-semibold flex items-baseline space-x-1">
                            <span class="text-xl">1,250</span>
                            <span class="text-blue-100/50 text-xs">kcal</span>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-3 border border-white/20 hover:bg-white/20 transition-colors duration-200">
                        <div class="text-blue-100/60 text-xs mb-1">Aktif Gün</div>
                        <div class="text-white font-semibold flex items-baseline space-x-1">
                            <span class="text-xl">14</span>
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
                                <span class="text-[13px] text-gray-500 font-light">Görseliniz işleniyor...</span>
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
                                        <span class="text-gray-400" id="loadingStatus">Görsel analiz ediliyor...</span>
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
                                        <div class="text-[13px] font-medium text-gray-900">2.4 sn</div>
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
                                    <div class="text-[15px] text-gray-600" data-nutrient="calories">324 kcal</div>
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
                                <div class="space-y-3" id="ingredientsDetail">
                                    <!-- Her malzeme için template -->
                                    <div class="ingredient-item hidden">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-[13px] text-gray-700 ingredient-name">Malzeme Adı</span>
                                            <span class="text-[11px] text-gray-500 ingredient-amount">100gr</span>
                                        </div>
                                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="ingredient-percentage h-full bg-gradient-to-r from-amber-200 to-amber-300 rounded-full"></div>
                                        </div>
                                    </div>
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
        <footer class="fixed bottom-0 left-0 right-0 bg-gray-50 border-t border-gray-100/50 z-40">
            <div class="max-w-md mx-auto px-4 h-12 flex items-center justify-around">
                <!-- Analiz -->
                <a href="#" class="relative group flex flex-col items-center">
                    <div class="w-7 h-7 flex items-center justify-center rounded-md bg-gray-800/10 text-gray-800 group-hover:bg-gray-800 group-hover:text-white transition-all duration-200">
                        <i class="fas fa-camera text-xs"></i>
                    </div>
                    <span class="text-[9px] font-medium mt-0.5 text-gray-800">Analiz</span>
                </a>

                <!-- Geçmiş -->
                <a href="#" class="relative group flex flex-col items-center">
                    <div class="w-7 h-7 flex items-center justify-center rounded-md bg-gray-100/80 text-gray-400 group-hover:bg-gray-200 group-hover:text-gray-600 transition-all duration-200">
                        <i class="fas fa-history text-xs"></i>
                    </div>
                    <span class="text-[9px] font-medium mt-0.5 text-gray-400">Geçmiş</span>
                </a>

                <!-- Premium -->
                <a href="./premium.php" class="relative group flex flex-col items-center">
                    <div class="w-7 h-7 flex items-center justify-center rounded-md bg-gradient-to-r from-amber-200 to-amber-300 text-amber-700 group-hover:from-amber-300 group-hover:to-amber-400 transition-all duration-200">
                        <i class="fas fa-crown text-xs"></i>
                    </div>
                    <span class="text-[9px] font-medium mt-0.5 text-amber-700">Premium</span>
                </a>

                <!-- İstatistik -->
                <a href="#" class="relative group flex flex-col items-center">
                    <div class="w-7 h-7 flex items-center justify-center rounded-md bg-gray-100/80 text-gray-400 group-hover:bg-gray-200 group-hover:text-gray-600 transition-all duration-200">
                        <i class="fas fa-chart-simple text-xs"></i>
                    </div>
                    <span class="text-[9px] font-medium mt-0.5 text-gray-400">İstatistik</span>
                </a>

                <!-- Profil -->
                <a href="#" class="relative group flex flex-col items-center">
                    <div class="w-7 h-7 flex items-center justify-center rounded-md bg-gray-100/80 text-gray-400 group-hover:bg-gray-200 group-hover:text-gray-600 transition-all duration-200">
                        <i class="fas fa-user text-xs"></i>
                    </div>
                    <span class="text-[9px] font-medium mt-0.5 text-gray-400">Profil</span>
                </a>
            </div>
            <div class="safe-area-bottom"></div>
        </footer>
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

                // Form data oluştur
                const formData = new FormData();
                formData.append('image', fileInput.files[0]);

                console.log('Selected file:', fileInput.files[0]); // Debug için dosyayı kontrol et

                // UI'ı güncelle
                uploadSection.classList.add('hidden');
                loadingSection.classList.remove('hidden');

                try {
                    // API isteği
                    const response = await fetch('api.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();
                    console.log('Full API Response:', result); // Tüm yanıtı görelim

                    if (!result.success) {
                        throw new Error(result.error);
                    }

                    // Debug bilgisi
                    if (result.debug) {
                        console.log('Original Response:', result.debug.original_response);
                        console.log('AI Content:', result.debug.ai_content);
                        console.log('Parsed Response:', result.debug.parsed_response);
                    }

                    // Null kontrolü ekleyelim
                    if (!result.data) {
                        throw new Error('API yanıtı boş');
                    }

                    // Sonuçları UI'a yerleştir
                    const foodNameEl = document.querySelector('#resultSection h2');
                    const portionEl = document.querySelector('#resultSection p');
                    
                    if (foodNameEl) foodNameEl.textContent = result.data.food_name;
                    if (portionEl) portionEl.textContent = 
                        `${result.data.portion?.count || 1} porsiyon (${result.data.portion?.amount || '300ml'})`;

                    // Besin değerlerini güncelle - Null kontrolü ile
                    const nutrients = {
                        'calories': 'kcal',
                        'protein': 'g',
                        'carbs': 'g',
                        'fat': 'g'
                    };

                    Object.entries(nutrients).forEach(([key, unit]) => {
                        const el = document.querySelector(`[data-nutrient="${key}"]`);
                        if (el) {
                            const value = result.data.nutrition?.[key] || '0';
                            el.textContent = `${value}${unit}`;
                        }
                    });

                    // Loading'i gizle, sonucu göster
                    loadingSection.classList.add('hidden');
                    resultSection.classList.remove('hidden');

                } catch (error) {
                    console.error('Error details:', error); // Hata detaylarını göster
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
        // Temel bilgileri güncelle
        document.querySelector('[data-result="food_name"]').textContent = data.food_name;
        document.querySelector('[data-result="portion"]').textContent = 
            `${data.portion.count} porsiyon (${data.portion.amount})`;
        document.querySelector('[data-result="plate_fullness"]').textContent = 
            data.portion.plate_fullness;
        document.querySelector('[data-result="cooking_method"]').textContent = 
            data.cooking_method;

        // Besin değerlerini güncelle
        // ... existing nutrition updates ...

        // Malzeme detaylarını güncelle
        const ingredientsContainer = document.getElementById('ingredientsDetail');
        const template = ingredientsContainer.querySelector('.ingredient-item');
        ingredientsContainer.innerHTML = ''; // Mevcut malzemeleri temizle

        // ingredients_detail JSON string ise parse et
        let ingredients = data.ingredients;
        if (typeof ingredients === 'string') {
            try {
                ingredients = JSON.parse(ingredients);
            } catch (e) {
                console.error('Ingredients parsing error:', e);
            }
        }

        // Her malzeme için yeni bir element oluştur
        ingredients.forEach(ingredient => {
            const item = template.cloneNode(true);
            item.classList.remove('hidden');
            
            // Malzeme adı ve miktarını ayarla
            const nameEl = item.querySelector('.ingredient-name');
            const amountEl = item.querySelector('.ingredient-amount');
            const percentageEl = item.querySelector('.ingredient-percentage');
            
            if (nameEl) nameEl.textContent = ingredient.name;
            if (amountEl) amountEl.textContent = ingredient.amount;
            if (percentageEl) percentageEl.style.width = `${ingredient.percentage}%`;
            
            ingredientsContainer.appendChild(item);
        });
    }
    </script>
</body>
</html>