<section class="bg-white py-8">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Cari Informasi RT/RW</h2>
            <p class="text-gray-600">Temukan data RT/RW dengan cepat</p>
        </div>
        <div class="max-w-md mx-auto">
            <div class="flex">
                <input type="text" id="search-input" placeholder="Masukkan nama RT atau Ketua RT..."
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button id="search-btn" class="bg-blue-600 text-white px-6 py-2 rounded-r-lg hover:bg-blue-700">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div id="search-results" class="mt-4 hidden">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-800">Hasil Pencarian:</h3>
                    <p id="result-text" class="text-gray-600 mt-2">Masukkan kata kunci untuk mencari...</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('search-btn').addEventListener('click', function() {
    const query = document.getElementById('search-input').value.trim();
    const resultsDiv = document.getElementById('search-results');
    const resultText = document.getElementById('result-text');

    if (query) {
        resultsDiv.classList.remove('hidden');
        resultText.textContent = `Mencari "${query}"... Dalam sistem nyata, ini akan menampilkan hasil dari database.`;
    } else {
        resultsDiv.classList.add('hidden');
    }
});

document.getElementById('search-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('search-btn').click();
    }
});
</script>
