<?php
function get_profile_photo_url($photo_path = '') {
    if (empty($photo_path)) {
        return '';
    }
    
    $filename = basename($photo_path);
    
    // Common locations relative to web root
    $possible_web_paths = [
        'uploads/profiles/' . $filename,
        'account/account/uploads/profiles/' . $filename,
        'account/uploads/profiles/' . $filename,
        $photo_path  // fallback to raw DB
    ];
    
    $base_dir = dirname(__DIR__);  // project root from account/
    
    foreach ($possible_web_paths as $web_path) {
        $server_path = $base_dir . '/' . $web_path;
        if (file_exists($server_path)) {
            return $web_path;
        }
    }
    
    return '';  // No image found, let HTML use placeholder
}

function get_profile_photo_path($photo_path = '') {
    $url = get_profile_photo_url($photo_path);
    return $url ?: 'https://via.placeholder.com/120/3B82F6/FFFFFF?text=PP';
}
?>
