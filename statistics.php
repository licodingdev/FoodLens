<?php
require_once 'functions.php';
require_once 'config/db.php';
require_once 'classes/Auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

if(!$auth->checkAuth()) {
    header('Location: login.php');
    exit;
}

$userId = $_COOKIE['user_id'];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İstatistikler | FoodLens AI</title>
    
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
            <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 px-4 pt-4 pb-6">
                <!-- Top Section -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/10 backdrop-blur-lg rounded-2xl flex items-center justify-center border border-white/20">
                            <i class="fas fa-chart-line text-white text-sm"></i>
                        </div>
                        <div>
                            <h1 class="text-white text-xl font-semibold">İstatistikler</h1>
                            <div class="text-blue-100 text-xs mt-0.5">Beslenme alışkanlıklarınız</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 gap-3">
                    <?php
                    // Günlük ortalama kalori
                    $dailyAvgQuery = $db->prepare("
                        SELECT AVG(calories) as avg_cal 
                        FROM food_analyses 
                        WHERE user_id = ? 
                        AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                    ");
                    $dailyAvgQuery->execute([$userId]);
                    $dailyAvg = round($dailyAvgQuery->fetch(PDO::FETCH_ASSOC)['avg_cal'] ?? 0);

                    // Toplam analiz
                    $totalAnalysesQuery = $db->prepare("
                        SELECT COUNT(*) as total 
                        FROM food_analyses 
                        WHERE user_id = ?
                    ");
                    $totalAnalysesQuery->execute([$userId]);
                    $totalAnalyses = $totalAnalysesQuery->fetch(PDO::FETCH_ASSOC)['total'];
                    ?>
                    
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/20">
                        <div class="text-blue-100/60 text-xs mb-1">Günlük Ort. Kalori</div>
                        <div class="text-white font-semibold flex items-baseline space-x-1">
                            <span class="text-2xl"><?= number_format($dailyAvg) ?></span>
                            <span class="text-blue-100/50 text-xs">kcal</span>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/20">
                        <div class="text-blue-100/60 text-xs mb-1">Toplam Analiz</div>
                        <div class="text-white font-semibold flex items-baseline space-x-1">
                            <span class="text-2xl"><?= number_format($totalAnalyses) ?></span>
                            <span class="text-blue-100/50 text-xs">öğün</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 px-4 pt-6 pb-24">
            <!-- Weekly Calories Chart -->
            <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-4">
                <h3 class="text-gray-900 font-medium mb-4">Haftalık Kalori Takibi</h3>
                <canvas id="caloriesChart" height="200"></canvas>
            </div>

            <!-- Nutrition Distribution -->
            <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-4">
                <h3 class="text-gray-900 font-medium mb-4">Besin Değerleri Dağılımı</h3>
                <canvas id="nutritionChart" height="200"></canvas>
            </div>

            <!-- Most Consumed Foods -->
            <div class="bg-white rounded-2xl border border-gray-100 p-4">
                <h3 class="text-gray-900 font-medium mb-4">En Çok Tüketilen Yemekler</h3>
                <?php
                $topFoodsQuery = $db->prepare("
                    SELECT food_name, COUNT(*) as count
                    FROM food_analyses
                    WHERE user_id = ?
                    GROUP BY food_name
                    ORDER BY count DESC
                    LIMIT 5
                ");
                $topFoodsQuery->execute([$userId]);
                $topFoods = $topFoodsQuery->fetchAll(PDO::FETCH_ASSOC);
                ?>
                
                <div class="space-y-3">
                    <?php foreach ($topFoods as $food): ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-utensils text-gray-400 text-xs"></i>
                                </div>
                                <span class="text-sm text-gray-900"><?= htmlspecialchars($food['food_name']) ?></span>
                            </div>
                            <span class="text-sm font-medium text-gray-500"><?= $food['count'] ?> kez</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>

        <!-- Footer Navigation -->
        <?php include 'footer.php'; ?>
    </div>

    <script>
        // Haftalık kalori grafiği
        <?php
        $weeklyDataQuery = $db->prepare("
            SELECT DATE(created_at) as date, SUM(calories) as total_calories
            FROM food_analyses
            WHERE user_id = ?
            AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date
        ");
        $weeklyDataQuery->execute([$userId]);
        $weeklyData = $weeklyDataQuery->fetchAll(PDO::FETCH_ASSOC);

        $labels = [];
        $data = [];
        foreach ($weeklyData as $day) {
            $labels[] = date('d M', strtotime($day['date']));
            $data[] = $day['total_calories'];
        }
        ?>

        new Chart(document.getElementById('caloriesChart'), {
            type: 'line',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Kalori',
                    data: <?= json_encode($data) ?>,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Besin değerleri dağılımı
        <?php
        $nutritionQuery = $db->prepare("
            SELECT 
                AVG(protein) as avg_protein,
                AVG(carbs) as avg_carbs,
                AVG(fat) as avg_fat
            FROM food_analyses
            WHERE user_id = ?
        ");
        $nutritionQuery->execute([$userId]);
        $nutrition = $nutritionQuery->fetch(PDO::FETCH_ASSOC);
        ?>

        new Chart(document.getElementById('nutritionChart'), {
            type: 'doughnut',
            data: {
                labels: ['Protein', 'Karbonhidrat', 'Yağ'],
                datasets: [{
                    data: [
                        <?= round($nutrition['avg_protein']) ?>,
                        <?= round($nutrition['avg_carbs']) ?>,
                        <?= round($nutrition['avg_fat']) ?>
                    ],
                    backgroundColor: [
                        '#fcd34d',
                        '#fbbf24',
                        '#f59e0b'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html> 