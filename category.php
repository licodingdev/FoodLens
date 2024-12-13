<?php
// Dataset'i yükle
$dataset = json_decode(file_get_contents('dataset.json'), true);

// Kategori adını al
$categoryName = $_GET['name'] ?? '';

// Kategori bulunamadıysa ana sayfaya yönlendir
if (!isset($dataset[$categoryName])) {
    header('Location: calories.php');
    exit;
}

$items = $dataset[$categoryName];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($categoryName) ?> | FoodLens AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Ubuntu', sans-serif;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        }
        .search-backdrop {
            background: linear-gradient(to bottom, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.7) 100%);
            backdrop-filter: blur(8px);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col max-w-4xl mx-auto relative">
        <!-- Header -->
        <header class="relative z-50">
            <div class="safe-area-top"></div>
            <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 px-4 py-4">
                <div class="flex items-center space-x-3">
                    <a href="calories.php" class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/10 text-white">
                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>
                    <h1 class="text-lg text-white font-medium"><?= htmlspecialchars($categoryName) ?></h1>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 px-4 pt-6 pb-24">
            <!-- Arama -->
            <div class="sticky top-0 z-40 -mx-4 px-4 pb-4 search-backdrop">
                <div class="relative">
                    <input type="text" 
                           id="searchInput"
                           placeholder="Bu kategori içinde ara..." 
                           class="w-full px-4 py-3.5 rounded-2xl bg-white/80 backdrop-blur border border-gray-100 focus:ring-2 focus:ring-gray-800 focus:border-transparent outline-none shadow-sm">
                    <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Yiyecek Listesi -->
            <div class="space-y-3">
                <?php foreach ($items as $item): ?>
                <div class="food-item bg-white rounded-2xl border border-gray-100 p-4 hover:border-gray-200 hover:shadow-sm transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-utensils text-gray-400"></i>
                            </div>
                            <div>
                                <div class="text-[15px] font-medium text-gray-900">
                                    <?= htmlspecialchars($item['food']) ?>
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5 flex items-center">
                                    <i class="fas fa-scale-balanced text-[10px] mr-1.5 text-gray-400"></i>
                                    <?= htmlspecialchars($item['portion']) ?>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-end">
                            <div class="text-[15px] font-medium text-gray-900">
                                <?= htmlspecialchars($item['calories']) ?>
                            </div>
                            <div class="text-[10px] text-gray-400 mt-0.5 flex items-center">
                                <i class="fas fa-fire-flame-simple text-amber-500 mr-1"></i>
                                kalori değeri
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- No Results Message -->
                <div id="noResults" class="hidden text-center py-12">
                    <div class="w-16 h-16 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-search text-gray-300 text-xl"></i>
                    </div>
                    <div class="text-sm text-gray-400">Sonuç bulunamadı</div>
                </div>
            </div>
        </main>

        <!-- Bottom Navigation -->
        <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100">
            <div class="max-w-4xl mx-auto px-4 py-3">
                <div class="flex items-center justify-around">
                    <a href="index.php" class="flex flex-col items-center">
                        <i class="fas fa-camera text-gray-400 mb-1"></i>
                        <span class="text-xs text-gray-600">Analiz</span>
                    </a>
                    <a href="calories.php" class="flex flex-col items-center">
                        <i class="fas fa-book-open text-gray-800 mb-1"></i>
                        <span class="text-xs text-gray-900 font-medium">Kalori Cetveli</span>
                    </a>
                    <a href="history.php" class="flex flex-col items-center">
                        <i class="fas fa-history text-gray-400 mb-1"></i>
                        <span class="text-xs text-gray-600">Geçmiş</span>
                    </a>
                    <a href="profile.php" class="flex flex-col items-center">
                        <i class="fas fa-user text-gray-400 mb-1"></i>
                        <span class="text-xs text-gray-600">Profil</span>
                    </a>
                </div>
            </div>
            <div class="safe-area-bottom"></div>
        </nav>
    </div>

    <script>
        // Arama fonksiyonu
        document.getElementById('searchInput').addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            let hasResults = false;
            
            document.querySelectorAll('.food-item').forEach(item => {
                const foodName = item.querySelector('.text-gray-900').textContent.toLowerCase();
                const portion = item.querySelector('.text-gray-500').textContent.toLowerCase();
                
                if (foodName.includes(searchTerm) || portion.includes(searchTerm)) {
                    item.style.display = '';
                    hasResults = true;
                } else {
                    item.style.display = 'none';
                }
            });

            // Sonuç bulunamadı mesajını göster/gizle
            const noResultsMessage = document.getElementById('noResults');
            noResultsMessage.style.display = hasResults ? 'none' : 'block';
        });
    </script>
</body>
</html> 