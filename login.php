<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>naberGiriş Yap - FoodLens AI</title>
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <!-- Toastify -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Ubuntu Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Ubuntu', sans-serif;
            background-color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex">
        <!-- Sol Taraf - Görsel Alanı -->
        <div class="hidden lg:flex lg:w-1/2 bg-gray-50 items-center justify-center p-12">
            <div class="max-w-lg">
                <img src="assets/images/food-analysis.svg" alt="Food Analysis" class="w-full">
                <h2 class="text-2xl font-semibold text-gray-800 mt-8 text-center">FoodLens AI ile Besin Analizi</h2>
                <p class="text-gray-600 mt-4 text-center leading-relaxed">
                    Yapay zeka destekli besin analizi ile yemeklerinizin besin değerlerini saniyeler içinde öğrenin.
                </p>
            </div>
        </div>

        <!-- Sağ Taraf - Login Formu -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                        <i class="fas fa-utensils text-gray-800 text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-800">Tekrar Hoşgeldiniz</h2>
                    <p class="text-gray-500 mt-2">Hesabınıza giriş yapın</p>
                </div>

                <!-- Login Form -->
                <form id="loginForm" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Kullanıcı Adı veya E-posta
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" 
                                   name="username" 
                                   class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 focus:border-gray-800 focus:ring-2 focus:ring-gray-100 outline-none transition-all text-sm"
                                   placeholder="kullaniciadi@example.com"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Şifre
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" 
                                   name="password" 
                                   class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-50 outline-none transition-all text-sm"
                                   placeholder="••••••••"
                                   required>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="remember" 
                                   class="h-4 w-4 text-gray-800 border-gray-300 rounded focus:ring-gray-800">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Beni hatırla
                            </label>
                        </div>
                        <a href="#" class="text-sm text-gray-800 hover:text-gray-900">
                            Şifremi unuttum
                        </a>
                    </div>

                    <button type="submit" 
                            class="w-full py-3 px-4 bg-gray-800 hover:bg-gray-900 text-white rounded-xl transition-colors duration-200 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-gray-800 focus:ring-offset-2">
                        Giriş Yap
                    </button>
                </form>

                <!-- Links -->
                <div class="mt-8 text-center">
                    <p class="text-gray-600">
                        Hesabınız yok mu? 
                        <a href="register.php" class="text-gray-800 hover:text-gray-900 font-medium">
                            Hemen kaydolun
                        </a>
                    </p>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        &copy; 2024 FoodLens AI. Tüm hakları saklıdır.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <!-- Login Script -->
    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);
            const data = {
                username: formData.get('username'),
                password: formData.get('password')
            };

            try {
                const response = await fetch('ajax/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                Toastify({
                    text: result.message,
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    style: {
                        background: result.success ? "#22c55e" : "#ef4444",
                        borderRadius: "8px",
                    }
                }).showToast();

                if(result.success) {
                    window.location.href = 'index.php';
                }

            } catch(error) {
                Toastify({
                    text: "Bir hata oluştu!",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    style: {
                        background: "#ef4444",
                        borderRadius: "8px",
                    }
                }).showToast();
            }
        });
    </script>
</body>
</html>