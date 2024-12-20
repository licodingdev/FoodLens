<?php
// Dataset'i yükle
$dataset = json_decode(file_get_contents('dataset.json'), true);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalori Cetveli | FoodLens AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Ubuntu', sans-serif;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col max-w-4xl mx-auto relative">
        <!-- Header -->
        <header class="relative z-50">
            <div class="safe-area-top"></div>
            <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <a href="index.php" class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/10 text-white">
                            <i class="fas fa-arrow-left text-sm"></i>
                        </a>
                        <h1 class="text-lg text-white font-medium">Kalori Cetveli</h1>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 px-4 pt-6 pb-8">
            <!-- Arama -->
            <div class="mb-8">
                <div class="relative">
                    <input type="text" 
                           id="searchInput"
                           placeholder="Yiyecek veya içecek ara..." 
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-gray-800 focus:border-transparent outline-none">
                    <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Kategoriler -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <?php foreach ($dataset as $category => $items): ?>
                <a href="category.php?name=<?= urlencode($category) ?>" 
                   class="p-4 rounded-2xl bg-white border-2 border-gray-100 hover:border-gray-800 hover:bg-gray-50/50 transition-all duration-200">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-utensils text-gray-600 text-xs"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900"><?= htmlspecialchars($category) ?></h3>
                            <p class="text-xs text-gray-500"><?= count($items) ?> ürün</p>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </main>

        <!-- Bottom Navigation -->
        <?php include 'footer.php'; ?>
    </div>
    </div>
</body>
</html> 