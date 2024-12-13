<?php
function timeAgo($timestamp) {
    $time = strtotime($timestamp);
    $current = time();
    $diff = $current - $time;
    
    $intervals = array(
        31536000 => 'yıl',
        2592000 => 'ay',
        604800 => 'hafta',
        86400 => 'gün',
        3600 => 'saat',
        60 => 'dk',
        1 => 'sn'
    );
    
    foreach ($intervals as $secs => $str) {
        $d = $diff / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . ' ' . $str . ' önce';
        }
    }
    
    return 'şimdi';
}
?> 