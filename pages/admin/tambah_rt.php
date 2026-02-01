<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/database.php';

$success = '';
$error = '';

if ($_SESSION['role'] == 'admin') {
    if (isset($_POST['add_rt'])) {
        $nama_rt = $_POST['nama_rt'];
        $ketua_rt = $_POST['ketua_rt'];

        $stmt = mysqli_prepare($conn, "INSERT INTO rt (nama_rt, ketua_rt) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $nama_rt, $ketua_rt);

        if (mysqli_stmt_execute($stmt)) {
            $success = "RT berhasil ditambahkan.";
        } else {
            $error = "Gagal menambahkan RT: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}


if ($_SESSION['role'] == 'admin') {
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <script src="https://cdn.tailwindcss.com"></script>
    <title>Document</title>
</head>
<body>
    
</body>
</html>
<div class="ml-64 p-6">
<h1 class="text-2xl font-bold mb-6">Tambah Data RT</h1>

    <?php if ($success) echo "<p class='text-green-500 mb-4'>$success</p>"; ?>
    <?php if ($error) echo "<p class='text-red-500 mb-4'>$error</p>"; ?>

    <form method="POST" class="mb-4">
        <div class="mb-2">
            <label for="nama_rt" class="block text-sm font-medium text-gray-700">Nama RT</label>
            <input type="text" name="nama_rt" id="nama_rt" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div class="mb-2">
            <label for="ketua_rt" class="block text-sm font-medium text-gray-700">Ketua RT</label>
            <input type="text" name="ketua_rt" id="ketua_rt" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <button type="submit" name="add_rt" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Tambah RT</button>
        <a href="manage_rt_rw.php" class="ml-2 bg-teal-500 text-white px-4 py-2 rounded hover:bg-teal-600">Kembali</a>
    </form>
</div>
<?php
}
?>
