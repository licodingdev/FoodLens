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
    <title>Premium | FoodLens AI</title>
    
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    
    <!-- Ubuntu Font -->
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Ubuntu', sans-serif;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col max-w-md mx-auto relative">
        <!-- Header -->
        <header class="relative z-50">
            <div class="safe-area-top"></div>
            <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 px-4 py-4">
                <div class="flex items-center space-x-3">
                    <a href="index.php" class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/10 text-white">
                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>
                    <h1 class="text-lg text-white font-medium">Premium'a Yükselt</h1>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 px-4 pt-6 pb-24">
            <!-- Hero Section -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto bg-gradient-to-r from-amber-200 to-amber-300 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-crown text-amber-700 text-xl"></i>
                </div>
                <h2 class="text-xl font-medium text-gray-900 mb-2">Premium'a Geç</h2>
                <p class="text-sm text-gray-500">Tüm özelliklere sınırsız erişim</p>
            </div>

            <!-- Plans -->
            <div class="space-y-4 mb-8">
                <!-- Aylık Plan -->
                <label class="block">
                    <input type="radio" name="plan" value="monthly" class="hidden peer">
                    <div class="p-4 rounded-2xl bg-white border-2 border-gray-100 peer-checked:border-amber-300 peer-checked:bg-amber-50/50 transition-all duration-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center">
                                    <i class="fas fa-calendar text-amber-600 text-xs"></i>
                                </div>
                                <div>
                                <button id="aylik" onclick="startMonthlySubscription()">Aylık Paket</button>    
                                    <p class="text-xs text-gray-500">Otomatik olarak <strong>yenilenmez!</strong></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-medium text-gray-900">₺49</div>
                                <div class="text-xs text-gray-500">/ ay</div>
                            </div>
                        </div>
                    </div>
                </label>

                <!-- Yıllık Plan -->
                <label class="block">
                    <input type="radio" name="plan" value="yearly" class="hidden peer" checked>
                    <div class="p-4 rounded-2xl bg-white border-2 border-gray-100 peer-checked:border-amber-300 peer-checked:bg-amber-50/50 transition-all duration-200 relative overflow-hidden">
                        <!-- İndirim Badge - Pozisyon düzeltildi -->
                        <div class="absolute -right-12 top-3 bg-green-500 text-white text-[10px] px-10 py-0.5 rotate-45 z-10">%40 İndirim</div>
                        
                        <div class="flex items-center justify-between mb-3 relative z-20">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center">
                                    <i class="fas fa-star text-amber-600 text-xs"></i>
                                </div>
                                <div>
                                <button id="yillik" onclick="startYearlySubscription()">Yıllık Paket</button>
                                    <p class="text-xs text-gray-500">Otomatik olarak <strong>yenilenmez!</strong></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-medium text-gray-900">₺349</div>
                                <div class="text-xs text-gray-500">/ yıl</div>
                            </div>
                        </div>
                    </div>
                </label>
            </div>

            <!-- Features -->
            <div class="bg-white rounded-2xl p-6 mb-8">
                <h3 class="text-sm font-medium text-gray-900 mb-4">Premium Özellikleri</h3>
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-infinity text-blue-500 text-xs"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Sınırsız Analiz</h4>
                            <p class="text-xs text-gray-500">Günlük analiz limiti olmadan kullanın</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-chart-line text-purple-500 text-xs"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Detaylı İstatistikler</h4>
                            <p class="text-xs text-gray-500">Beslenme alışkanlıklarınızı analiz edin</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-rose-50 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-heart text-rose-500 text-xs"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Sağlık Tavsiyeleri</h4>
                            <p class="text-xs text-gray-500">Kişiselleştirilmiş beslenme önerileri</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock-rotate-left text-green-500 text-xs"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Geçmiş Kayıtlar</h4>
                            <p class="text-xs text-gray-500">Tüm geçmiş analizlerinize erişin</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Fixed Bottom Bar -->
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100">
            <div class="max-w-md mx-auto bg-gray-50 px-4 py-4">
                <button class="w-full h-12 bg-gradient-to-r from-gray-800 to-gray-900 rounded-xl text-white text-sm font-medium flex items-center justify-center space-x-2 hover:from-gray-900 hover:to-black transition-all">
                    <i class="fas fa-crown text-amber-300 text-xs"></i>
                    <span>Premium'a Yükselt</span>
                </button>
                <p class="text-center text-xs text-gray-400 mt-2">
                    İstediğiniz zaman iptal edebilirsiniz
                </p>
            </div>
            <div class="safe-area-bottom"></div>
        </div>
    </div>
</body>




<script>
function startMonthlySubscription() {
    if (window.Android) {
        Android.purchaseMonthlySubscription();
    } else {
        console.log('Android interface not found');
    }
}

function startYearlySubscription() {
    if (window.Android) {
        Android.purchaseYearlySubscription();
    } else {
        console.log('Android interface not found');
    }
}
</script>
</html>