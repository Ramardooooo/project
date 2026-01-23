<div class="w-64 bg-yellow-600 text-white min-h-screen fixed">
<div class="p-4 text-xl font-bold">Lurago.id</div>
<ul class="mt-4">
<li class="px-4 py-2 hover:bg-yellow-700"><a href="home.php">Beranda</a></li>
<li class="px-4 py-2 hover:bg-yellow-700"><a href="dashboard.php">Dashboard</a></li>
<?php if ($_SESSION['role'] == 'admin') { ?>
<li class="px-4 py-2 hover:bg-yellow-700"><a href="users.php">Manage Users</a></li>
<?php } ?>
<li class="px-4 py-2 hover:bg-yellow-700"><a href="../auth/logout.php">Logout</a></li>
</ul>
</div>
