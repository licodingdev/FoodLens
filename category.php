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
    <title><?= htmlspecialchars($categoryName) ?> - FoodLens</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-b from-gray-900 to-gray-800">
    <!-- Navbar -->
    <nav class="border-b border-white/5">
        <div class="max-w-4xl mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <a href="index.php" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-white/10 rounded-xl flex items-center justify-center">
                        <i class="fas fa-bolt text-amber-400"></i>
                    </div>
                    <span class="text-white font-medium">FoodLens</span>
                </a>
                <div class="flex items-center gap-4">
                    <a href="index.php" class="text-blue-100/60 hover:text-white transition-colors">
                        <i class="fas fa-camera mr-1"></i>
                        Analiz
                    </a>
                    <a href="calories.php" class="text-white">
                        <i class="fas fa-book-open mr-1"></i>
                        Kalori Cetveli
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Back Button -->
        <a href="calories.php" class="inline-flex items-center gap-2 text-blue-100/60 hover:text-white mb-6">
            <i class="fas fa-arrow-left"></i>
            <span>Geri Dön</span>
        </a>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-white mb-2"><?= htmlspecialchars($categoryName) ?></h1>
            <p class="text-blue-100/60"><?= count($items) ?> ürün listeleniyor</p>
        </div>

        <!-- Arama -->
        <div class="mb-8">
            <div class="relative">
                <input type="text" 
                       id="searchInput"
                       placeholder="Bu kategori içinde ara..." 
                       class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/5 focus:ring-2 focus:ring-amber-500/50 focus:border-transparent outline-none text-white placeholder:text-blue-100/30">
                <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-blue-100/30"></i>
            </div>
        </div>

        <!-- Yiyecek Listesi -->
        <div class="bg-white/10 backdrop-blur-sm rounded-2xl border border-white/5 overflow-hidden">
            <div class="divide-y divide-white/5">
                <?php foreach ($items as $item): ?>
                <div class="food-item p-4 hover:bg-white/5 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-white">
                                <?= htmlspecialchars($item['Yiyecek']) ?>
                            </div>
                            <div class="text-xs text-blue-100/60 mt-1">
                                <?= htmlspecialchars($item['Porsiyon']) ?>
                            </div>
                        </div>
                        <div class="text-sm font-medium text-amber-400">
                            <?= htmlspecialchars($item['Kalori']) ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        // Arama fonksiyonu
        document.getElementById('searchInput').addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            
            document.querySelectorAll('.food-item').forEach(item => {
                const foodName = item.querySelector('.text-white').textContent.toLowerCase();
                item.style.display = foodName.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
</body>
</html> 