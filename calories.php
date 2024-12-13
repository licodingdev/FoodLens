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
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Kalori Cetveli</h1>
            <p class="text-gray-500">Yiyecek ve içeceklerin kalori değerleri</p>
        </div>

        <!-- Arama -->
        <div class="mb-8">
            <div class="relative">
                <input type="text" 
                       id="searchInput"
                       placeholder="Yiyecek veya içecek ara..." 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <!-- Kategoriler -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mb-8">
            <?php foreach ($dataset as $category => $items): ?>
            <button class="category-btn bg-white p-4 rounded-xl border border-gray-100 hover:border-blue-500 hover:bg-blue-50 transition-all text-left"
                    data-category="<?= htmlspecialchars($category) ?>">
                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($category) ?></div>
                <div class="text-xs text-gray-500 mt-1"><?= count($items) ?> ürün</div>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- Yiyecek Listesi -->
        <div id="foodList" class="space-y-4">
            <?php foreach ($dataset as $category => $items): ?>
            <div class="category-section hidden" data-category="<?= htmlspecialchars($category) ?>">
                <div class="flex items-center gap-2 mb-4">
                    <h2 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($category) ?></h2>
                    <div class="h-px flex-1 bg-gray-100"></div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="divide-y divide-gray-100">
                        <?php foreach ($items as $item): ?>
                        <div class="food-item p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($item['Yiyecek']) ?>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <?= htmlspecialchars($item['Porsiyon']) ?>
                                    </div>
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($item['Kalori']) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Kategori butonları
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                // Tüm kategorileri gizle
                document.querySelectorAll('.category-section').forEach(section => {
                    section.classList.add('hidden');
                });
                
                // Seçilen kategoriyi göster
                const category = btn.dataset.category;
                document.querySelector(`.category-section[data-category="${category}"]`).classList.remove('hidden');
                
                // Aktif buton stilini güncelle
                document.querySelectorAll('.category-btn').forEach(b => {
                    b.classList.remove('border-blue-500', 'bg-blue-50');
                });
                btn.classList.add('border-blue-500', 'bg-blue-50');
            });
        });

        // Arama fonksiyonu
        document.getElementById('searchInput').addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            
            document.querySelectorAll('.food-item').forEach(item => {
                const foodName = item.querySelector('.text-gray-900').textContent.toLowerCase();
                if (foodName.includes(searchTerm)) {
                    item.style.display = '';
                    // Ebeveyn kategoriyi göster
                    item.closest('.category-section').classList.remove('hidden');
                } else {
                    item.style.display = 'none';
                }
            });

            // Eğer arama terimi boşsa tüm kategorileri gizle
            if (searchTerm === '') {
                document.querySelectorAll('.category-section').forEach(section => {
                    section.classList.add('hidden');
                });
            }
        });

        // Sayfa yüklendiğinde ilk kategoriyi seç
        document.querySelector('.category-btn').click();
    </script>
</body>
</html> 