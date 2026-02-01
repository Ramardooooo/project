<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit User - Lurahgo.id</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background:#eafaf1; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="bg-white p-7 rounded-md shadow w-96">
        <h2 class="text-xl font-semibold mb-5 text-center text-gray-800">
            Edit User
        </h2>

        <form method="POST">
            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">Username</label>
                <input type="text" name="username" value="malasngoding123"
                    class="w-full px-3 py-2 border rounded
                           focus:outline-none focus:border-green-500"
                    required>
            </div>

            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="ramaxkont1@gmail.com"
                    class="w-full px-3 py-2 border rounded
                           focus:outline-none focus:border-green-500"
                    required>
            </div>

            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">Password</label>
                <input type="password" name="password"
                    class="w-full px-3 py-2 border rounded
                           focus:outline-none focus:border-green-500">
                <small class="text-gray-500 text-xs">
                    Kosongkan jika tidak ingin mengubah password
                </small>
            </div>

            <div class="mb-5">
                <label class="block text-sm text-gray-700 mb-1">Role</label>
                <select name="role"
                    class="w-full px-3 py-2 border rounded
                           focus:outline-none focus:border-green-500">
                    <option value="user">User</option>
                    <option value="ketua">Ketua</option>
                    <option value="admin" selected>Admin</option>
                </select>
            </div>

            <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700
                       text-white py-2 rounded">
                Update User
            </button>
        </form>

        <a href="dashboard_admin.php"
            class="block text-center mt-4 text-sm text-green-600 hover:underline">
            Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
