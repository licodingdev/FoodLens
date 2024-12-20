<?php
require_once 'functions.php';
error_reporting(E_ALL); ini_set('display_errors', 1);

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

// Kullanıcının geçmiş analizlerini getir
$userId = $_COOKIE['user_id'];
$query = $db->prepare("
    SELECT * FROM food_analyses 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$query->execute([$userId]);
$analyses = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geçmiş Analizler | FoodLens AI</title>
    
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    
    <!-- Ubuntu Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Ubuntu', sans-serif;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        }
        
        /* Modal Animation */
        #analysisModal {
            transition: opacity 0.2s ease-in-out;
        }
        
        #analysisModal.hidden {
            opacity: 0;
            pointer-events: none;
        }
        
        #analysisModal:not(.hidden) {
            opacity: 1;
        }
        
        /* Modal Content Animation */
        #analysisModal > div:last-child {
            transform: translateY(100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        #analysisModal:not(.hidden) > div:last-child {
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col max-w-md mx-auto relative">
        <!-- Header -->
        <header class="relative z-50">
            <div class="safe-area-top"></div>
            <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 px-4 pt-4 pb-6">
                <!-- Top Section -->
                <div class="flex items-center justify-between mb-8">
                    <!-- Brand -->
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/10 backdrop-blur-lg rounded-2xl flex items-center justify-center border border-white/20 shadow-lg shadow-gray-900/10">
                            <i class="fas fa-history text-white text-sm"></i>
                        </div>
                        <div>
                            <h1 class="text-white text-xl font-semibold tracking-tight">Geçmiş Analizler</h1>
                            <div class="text-blue-100 text-xs mt-0.5">Son 30 günlük analizleriniz</div>
                        </div>
                    </div>
                </div>

                <!-- Stats Section -->
                <div class="grid grid-cols-3 gap-3">
                    <?php
                    try {
                        // Toplam analiz sayısı
                        $totalQuery = $db->prepare("SELECT COUNT(*) as total FROM food_analyses WHERE user_id = ?");
                        $totalQuery->execute([$userId]);
                        $totalAnalyses = $totalQuery->fetch(PDO::FETCH_ASSOC)['total'];

                        // Bugünkü analiz sayısı
                        $todayQuery = $db->prepare("SELECT COUNT(*) as today FROM food_analyses WHERE user_id = ? AND DATE(created_at) = CURDATE()");
                        $todayQuery->execute([$userId]);
                        $todayAnalyses = $todayQuery->fetch(PDO::FETCH_ASSOC)['today'];

                        // Ortalama günlük kalori
                        $avgQuery = $db->prepare("SELECT AVG(calories) as avg_cal FROM food_analyses WHERE user_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
                        $avgQuery->execute([$userId]);
                        $avgCalories = round($avgQuery->fetch(PDO::FETCH_ASSOC)['avg_cal'] ?? 0);
                    } catch (Exception $e) {
                        error_log("Stats Error: " . $e->getMessage());
                    }
                    ?>
                    
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-3 border border-white/20">
                        <div class="text-blue-100/60 text-xs mb-1">Toplam</div>
                        <div class="text-white font-semibold flex items-baseline space-x-1">
                            <span class="text-xl"><?= number_format($totalAnalyses) ?></span>
                            <span class="text-blue-100/50 text-xs">analiz</span>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-3 border border-white/20">
                        <div class="text-blue-100/60 text-xs mb-1">Bugün</div>
                        <div class="text-white font-semibold flex items-baseline space-x-1">
                            <span class="text-xl"><?= number_format($todayAnalyses) ?></span>
                            <span class="text-blue-100/50 text-xs">analiz</span>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-3 border border-white/20">
                        <div class="text-blue-100/60 text-xs mb-1">Ort. Kalori</div>
                        <div class="text-white font-semibold flex items-baseline space-x-1">
                            <span class="text-xl"><?= number_format($avgCalories) ?></span>
                            <span class="text-blue-100/50 text-xs">kcal</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 px-4 pt-6 pb-24">
            <!-- Analyses List -->
            <div class="space-y-4">
                <?php if (empty($analyses)): ?>
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-camera text-gray-400 text-xl"></i>
                        </div>
                        <h3 class="text-gray-900 font-medium mb-1">Henüz Analiz Yok</h3>
                        <p class="text-gray-500 text-sm">İlk yemek analizinizi yapmak için hazır!</p>
                        <button onclick="window.location.href='index.php'" class="mt-4 px-4 py-2 bg-gray-900 text-white rounded-xl text-sm">
                            Analiz Yap
                        </button>
                    </div>
                <?php else: ?>
                    <?php foreach ($analyses as $analysis): ?>
                        <div class="bg-white rounded-2xl border border-gray-100 p-4 hover:border-gray-200 hover:shadow-sm transition-all duration-200 cursor-pointer"
                             onclick="showAnalysisDetails(<?= htmlspecialchars(json_encode($analysis)) ?>)">
                            <div class="flex items-start space-x-4">
                                <!-- Image Preview -->
                                <div class="w-20 h-20 rounded-xl bg-gray-100 flex-shrink-0 overflow-hidden">
                                    <img src="<?= $analysis['image_path'] ? '/' . $analysis['image_path'] : 'assets/images/placeholder-food.png' ?>" 
                                         alt="Food Analysis" 
                                         class="w-full h-full object-cover">
                                </div>
                                
                                <!-- Content -->
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <h3 class="text-gray-900 font-medium">
                                                <?= htmlspecialchars($analysis['food_name']) ?>
                                            </h3>
                                            <div class="text-gray-500 text-xs mt-0.5">
                                                <?= date('d.m.Y H:i', strtotime($analysis['created_at'])) ?>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-gray-900 font-medium">
                                                <?= number_format($analysis['calories']) ?> kcal
                                            </div>
                                            
                                        </div>
                                    </div>
                                    
                                    <!-- Nutritional Info -->
                                    <div class="grid grid-cols-3 gap-2 mt-3">
                                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                                            <div class="text-xs text-gray-500 mb-1">Protein</div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= number_format($analysis['protein'], 1) ?>g
                                            </div>
                                        </div>
                                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                                            <div class="text-xs text-gray-500 mb-1">Karb</div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= number_format($analysis['carbs'], 1) ?>g
                                            </div>
                                        </div>
                                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                                            <div class="text-xs text-gray-500 mb-1">Yağ</div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= number_format($analysis['fat'], 1) ?>g
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>

        <!-- Footer Navigation -->
        <?php include 'footer.php'; ?>
    </div>

    <!-- Modal -->
    <div id="analysisModal" class="fixed inset-0 z-50 hidden">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="closeModal()"></div>
        
        <!-- Modal Content -->
        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl max-w-md mx-auto">
            <div class="relative">
                <!-- Close Button -->
                <button onclick="closeModal()" class="absolute right-4 top-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </button>

                <!-- Modal Header -->
                <div class="p-6">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-blue-500/10 rounded-xl flex items-center justify-center">
                            <i class="fas fa-chart-pie text-blue-500"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">İçerik Detayları</h3>
                            <p class="text-sm text-gray-500">Yemeğin içindeki malzemeler</p>
                        </div>
                    </div>

                    <!-- Ingredients List -->
                    <div id="ingredientsList" class="space-y-4">
                        <!-- JavaScript ile doldurulacak -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
    function showAnalysisDetails(analysis) {
        // Modal'ı göster
        document.getElementById('analysisModal').classList.remove('hidden');
        
        // İçerik detaylarını parse et
        let ingredients = [];
        try {
            ingredients = JSON.parse(analysis.ingredients_detail || '[]');
        } catch (e) {
            console.error('JSON parse error:', e);
        }

        // İçerik listesini oluştur
        const list = document.getElementById('ingredientsList');
        list.innerHTML = '';

        ingredients.forEach(ingredient => {
            const item = document.createElement('div');
            item.className = 'mb-4';
            item.innerHTML = `
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-utensils text-gray-400 text-xs"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">${ingredient.name}</div>
                            <div class="text-xs text-gray-500">${ingredient.amount}</div>
                        </div>
                    </div>
                    <div class="text-sm font-medium text-gray-900">
                        ${ingredient.percentage}%
                    </div>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-amber-500 rounded-full" style="width: ${ingredient.percentage}%"></div>
                </div>
            `;
            list.appendChild(item);
        });
    }

    function closeModal() {
        document.getElementById('analysisModal').classList.add('hidden');
    }

    // ESC tuşu ile modal'ı kapatma
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
    </script>
</body>
</html> 