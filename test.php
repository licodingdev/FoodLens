<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>API Hata Logs</title>
    <style>
        body { font-family: monospace; padding: 20px; }
        .error { 
            background: #fff3f3; 
            padding: 15px; 
            margin: 10px 0; 
            border: 1px solid #ffd7d7;
            border-radius: 4px;
        }
        .time { color: #666; }
        .message { color: #ff0000; font-weight: bold; }
        .trace { font-size: 12px; margin-top: 10px; }
    </style>
</head>
<body>
    <h1>API Hata Logları</h1>
    
    <?php if (!empty($_SESSION['api_errors'])): ?>
        <?php foreach ($_SESSION['api_errors'] as $error): ?>
            <div class="error">
                <div class="time"><?php echo $error['time']; ?></div>
                <div class="message"><?php echo $error['message']; ?></div>
                <div>File: <?php echo $error['file']; ?></div>
                <div>Line: <?php echo $error['line']; ?></div>
                <pre class="trace"><?php echo $error['trace']; ?></pre>
            </div>
        <?php endforeach; ?>
        
        <form method="post">
            <button type="submit" name="clear_logs">Logları Temizle</button>
        </form>
    <?php else: ?>
        <p>Henüz hata logu yok.</p>
    <?php endif; ?>

    <?php
    // Logları temizleme
    if (isset($_POST['clear_logs'])) {
        $_SESSION['api_errors'] = [];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    ?>
</body>
</html> 