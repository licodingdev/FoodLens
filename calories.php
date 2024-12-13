<?php
// Dataset'i yükle
$dataset = json_decode(file_get_contents('dataset.json'), true);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalori Cetveli - FoodLens</title>
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
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-white mb-2">Kalori Cetveli</h1>
            <p class="text-blue-100/60">Yiyecek ve içeceklerin kalori değerleri</p>
        </div>

        <!-- Arama -->
        <div class="mb-8">
            <div class="relative">
                <input type="text" 
                       id="searchInput"
                       placeholder="Yiyecek veya içecek ara..." 
                       class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/5 focus:ring-2 focus:ring-amber-500/50 focus:border-transparent outline-none text-white placeholder:text-blue-100/30">
                <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-blue-100/30"></i>
            </div>
        </div>

        <!-- Kategoriler -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mb-8">
            <?php foreach ($dataset as $category => $items): ?>
            <a href="category.php?name=<?= urlencode($category) ?>" 
               class="category-btn bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/5 hover:bg-white/20 transition-all text-left">
                <div class="text-sm font-medium text-white"><?= htmlspecialchars($category) ?></div>
                <div class="text-xs text-blue-100/60 mt-1"><?= count($items) ?> ürün</div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html> 