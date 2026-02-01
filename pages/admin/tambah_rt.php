<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Data RT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background:#eafaf1; }
    </style>
</head>

<body class="min-h-screen">

<div class="ml-64 min-h-screen flex items-center justify-center p-8">

    <div class="max-w-xl w-full bg-white rounded-md shadow p-7">

        <h1 class="text-xl font-semibold text-gray-800 mb-5">
            Tambah Data RT
        </h1>

        <form method="POST" class="space-y-4">

            <div>
                <label class="block text-sm text-gray-700 mb-1">
                    Nama RT
                </label>
                <input
                    type="text"
                    name="nama_rt"
                    placeholder="Contoh: RT 01"
                    required
                    class="w-full border rounded px-3 py-2
                           focus:outline-none focus:border-green-500"
                >
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">
                    Ketua RT
                </label>
                <input
                    type="text"
                    name="ketua_rt"
                    placeholder="Nama Ketua RT"
                    required
                    class="w-full border rounded px-3 py-2
                           focus:outline-none focus:border-green-500"
                >
            </div>

            <div class="flex gap-3 pt-3">
                <button
                    type="submit"
                    name="add_rt"
                    class="flex-1 bg-green-600 hover:bg-green-700
                           text-white py-2 rounded">
                    Simpan
                </button>

                <a
                    href="manage_rt_rw.php"
                    class="flex-1 text-center bg-gray-200 hover:bg-gray-300
                           text-gray-700 py-2 rounded">
                    Kembali
                </a>
            </div>

        </form>

    </div>
</div>

</body>
</html>
