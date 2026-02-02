Hanya manusia biasa bukan pria solo

.htaccess jangan lupa supaya url websitenya tanpa ekstensi .php atau apapun itu lah pokoknya!

RewriteEngine On
RewriteRule ^dashboard_admin$ pages/admin/dashboard_admin.php [L]
RewriteRule ^manage_users$ pages/admin/manage_users.php [L]
RewriteRule ^manage_rt_rw$ pages/admin/manage_rt_rw.php [L]
RewriteRule ^manage_master_data$ pages/admin/manage_master_data.php [L]
RewriteRule ^tambah_user$ pages/admin/tambah_user.php [L]
RewriteRule ^edit_user$ pages/admin/edit_user.php [L]
RewriteRule ^tambah_rt$ pages/admin/tambah_rt.php [L]
RewriteRule ^edit_rt$ pages/admin/edit_rt.php [L]
RewriteRule ^dashboard_ketua$ pages/ketua/dashboard_ketua.php [L]
RewriteRule ^dashboard_user$ pages/user/dashboard_user.php [L]
RewriteRule ^home$ home.php [L]
RewriteRule ^login$ auth/login.php [L]
RewriteRule ^register$ auth/register.php [L]
RewriteRule ^logout$ auth/logout.php [L]
