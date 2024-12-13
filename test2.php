<?php
// IMAP sunucu bilgileri
$mailbox = '{outlook.office365.com:993/imap/ssl}INBOX'; // Outlook IMAP sunucusu
$username = 'kontakt@badheizkoerper.shop'; // E-posta adresiniz
$password = '1yeni#Posta'; // Şifreniz

// E-posta kutusuna bağlan
$imap = imap_open($mailbox, $username, $password);

if (!$imap) {
    // Bağlantı başarısız ise hata mesajını göster
    echo "Bağlantı başarısız: " . imap_last_error();
    exit;
}

// Gelen kutusundaki tüm e-postaları getir
$emails = imap_search($imap, 'ALL'); // Tüm e-postaları arar

if ($emails) {
    // E-postaları en yeniye göre sırala
    rsort($emails);

    // İlk 10 e-postayı listele
    echo "Son 10 e-posta:\n";
    foreach (array_slice($emails, 0, 10) as $email_number) {
        // E-posta başlıklarını ve temel bilgileri al
        $overview = imap_fetch_overview($imap, $email_number, 0);

        echo "Konu: " . $overview[0]->subject . "\n";
        echo "Gönderen: " . $overview[0]->from . "\n";
        echo "Tarih: " . $overview[0]->date . "\n";
        echo "-------------------------\n";
    }
} else {
    echo "Gelen kutusunda e-posta bulunamadı.";
}

// Bağlantıyı kapat
imap_close($imap);
?>