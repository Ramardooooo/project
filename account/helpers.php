<?php
function get_profile_photo_path($photo_path = '') {
    $default = 'account/profiles/default-avatar.png';
    if (empty($photo_path)) {
        return $default;
    }
    
    // Extract filename from DB path like 'uploads/profiles/file.png' → 'file.png'
    $filename = basename($photo_path);
    
    // Actual files are deep nested - construct direct web path
    $actual_path = 'account/account/account/uploads/profiles/' . $filename;
    
    // Verify if exists (server path)
    $server_paths = [
        __DIR__ . '/account/account/account/uploads/profiles/' . $filename,
        __DIR__ . '/../account/account/account/uploads/profiles/' . $filename,
        __DIR__ . '/../../account/account/account/uploads/profiles/' . $filename,
        'account/account/account/uploads/profiles/' . $filename,
    ];
    
    $exists = false;
    foreach ($server_paths as $sp) {
        if (file_exists($sp)) {
            $exists = true;
            break;
        }
    }
    
    return $exists ? $actual_path : $default;
}
?>
