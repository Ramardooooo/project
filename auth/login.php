<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_input = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($login_input)) {
        if (filter_var($login_input, FILTER_VALIDATE_EMAIL)) {
            $sql = "SELECT * FROM users WHERE email=?";
        } else {
            $sql = "SELECT * FROM users WHERE username=?";
        }
        $param = $login_input;
    } else {
        $error = "Masukkan username atau email.";
    }

    if (!isset($error)) {
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $param);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] == 'admin') {
                    header("Location: /PROJECT/dashboard_admin");
                } elseif ($user['role'] == 'ketua') {
                    header("Location: /PROJECT/dashboard_ketua");
                } else {
                    header("Location: ../../PROJECT/home");
                }
                exit();
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "User tidak ditemukan.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Lurahgo.id</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
body{
    background-image:url('https://images.unsplash.com/photo-1500382017468-9049fed747ef');
    background-size:cover;
    background-position:center;
}
</style>

</head>

<body class="min-h-screen flex items-center justify-center">
<div class="absolute inset-0 bg-black/40"></div>

<div class="relative w-96 p-[2px] rounded-3xl bg-gradient-to-r from-white/40 to-white/10 shadow-2xl">

    <div class="bg-white/20 backdrop-blur-xl rounded-3xl p-8 border border-white/30">

        <h2 class="text-3xl font-bold text-center text-white mb-2">Lurahgo.id</h2>
        <p class="text-center text-white/80 mb-6">Selamat datang 👋</p>

        <?php if (isset($error)): ?>
            <p class="text-red-200 text-center mb-4"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST" class="space-y-5">

<div class="relative">
    <input type="text" name="email" required placeholder=" "
    class="peer w-full px-4 py-3 bg-white/20 border border-white/30 rounded-xl text-white focus:outline-none">

    <label class="pointer-events-none absolute left-4 top-3 text-white/70 transition-all duration-200

    peer-placeholder-shown:top-3 
    peer-placeholder-shown:text-base
    
    peer-focus:-top-2 
    peer-focus:text-sm 
    peer-focus:text-white">
    
    Username / Email
    </label>
</div>

<div class="relative">
    <input id="password" type="password" name="password" required placeholder=" "
    class="peer w-full px-4 py-3 bg-white/20 border border-white/30 rounded-xl text-white focus:outline-none">

    <label class="pointer-events-none absolute left-4 top-3 text-white/70 transition-all duration-200

    peer-placeholder-shown:top-3 
    peer-placeholder-shown:text-base
    
    peer-focus:-top-2 
    peer-focus:text-sm 
    peer-focus:text-white">
    
    Password
    </label>
</div>

<div class="flex items-center gap-2 text-white/90 text-sm">
    <input type="checkbox" onclick="togglePass()"> Show Password
</div>

<button type="submit"
class="w-full py-3 rounded-xl font-semibold text-white
bg-gradient-to-r from-green-400 to-emerald-600
hover:scale-105 transition-all duration-300">
Login
</button>

</form>

    </div>
</div>

<script>
function togglePass(){
    const p=document.getElementById("password");
    p.type=p.type==="password"?"text":"password";
}
</script>

</body>
</html>
