<?php
include '../../config/database.php';
include '../../layouts/admin/header.php';
include '../../layouts/admin/sidebar.php';
if(!isset($_SESSION['role'])||$_SESSION['role']!=='admin'){header("Location: home");exit();}
if($_SERVER['REQUEST_METHOD']==='POST'){if(isset($_POST['add_gallery'])){$title=mysqli_real_escape_string($conn,$_POST['title']);$description=mysqli_real_escape_string($conn,$_POST['description']);if(isset($_FILES['image'])&&$_FILES['image']['error']===UPLOAD_ERR_OK){$upload_dir='../../beranda/gallery/';if(!is_dir($upload_dir)){mkdir($upload_dir,0755,true);}$file_name=time().'_'.basename($_FILES['image']['name']);$target_path=$upload_dir.$file_name;if(move_uploaded_file($_FILES['image']['tmp_name'],$target_path)){$image_path=$file_name;$query="INSERT INTO gallery (title, description, image_path) VALUES ('$title', '$description', '$image_path')";if(mysqli_query($conn,$query)){$success="Gallery item added successfully!";}else{$error="Error adding gallery item: ".mysqli_error($conn);}}else{$error="Error uploading image.";}}else{$error="Please select an image to upload.";}}elseif(isset($_POST['delete_gallery'])){$id=(int)$_POST['id'];$query="SELECT image_path FROM gallery WHERE id = $id";$result=mysqli_query($conn,$query);if($row=mysqli_fetch_assoc($result)){if(file_exists('../../'.$row['image_path'])){unlink('../../'.$row['image_path']);}mysqli_query($conn,"DELETE FROM gallery WHERE id = $id");$success="Gallery item deleted successfully!";}}}
$search=isset($_GET['search'])?mysqli_real_escape_string($conn,$_GET['search']):'';$where_clause='';if(!empty($search)){$where_clause="WHERE title LIKE '%$search%' OR description LIKE '%$search%'";}$query="SELECT * FROM gallery $where_clause ORDER BY created_at DESC";$result=mysqli_query($conn,$query);$gallery_items=mysqli_fetch_all($result,MYSQLI_ASSOC);
?>
<div id="mainContent" class="ml-64 min-h-screen bg-blue-900">
<div class="p-8">
<h1 class="text-4xl font-extrabold mb-8 text-white drop-shadow-lg">Kelola Galeri</h1>
<?php if(isset($success)):?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
<?php echo $success;?>
</div>
<?php endif;?>
<?php if(isset($error)):?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
<?php echo $error;?>
</div>
<?php endif;?>
<div class="bg-white/90 backdrop-blur-md rounded-2xl shadow-lg p-7 mb-8 border border-white/20 hover:shadow-2xl hover:bg-white/95 transition-all duration-300">
<h3 class="text-xl font-bold mb-6 text-black drop-shadow-lg flex items-center gap-2">
<i class="fas fa-plus-circle text-green-600"></i>Tambah Item Galeri
</h3>
<form method="POST" enctype="multipart/form-data" class="space-y-4">
<div>
<label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
<i class="fas fa-heading text-gray-500"></i>Judul
</label>
<input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
<i class="fas fa-align-left text-gray-500"></i>Deskripsi
</label>
<textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
<i class="fas fa-image text-gray-500"></i>Gambar
</label>
<input type="file" name="image" accept="image/*" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
</div>
<button type="submit" name="add_gallery" class="w-full py-3 rounded-xl font-semibold text-white bg-gradient-to-r from-green-400 to-emerald-600 hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2">
<i class="fas fa-save"></i>Tambah Galeri
</button>
</form>
</div>
<div class="bg-white/90 backdrop-blur-md rounded-2xl shadow-lg p-7 border border-white/20 hover:shadow-2xl hover:bg-white/95 transition-all duration-300">
<div class="flex justify-between items-center mb-6">
<h3 class="text-xl font-bold text-black drop-shadow-lg flex items-center gap-2">
<i class="fas fa-images text-blue-600"></i>Daftar Galeri
</h3>
<form method="GET" class="flex gap-2">
<input type="text" name="search" value="<?php echo htmlspecialchars($search);?>" placeholder="Cari galeri..." class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
<button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors flex items-center gap-2">
<i class="fas fa-search"></i>Cari
</button>
</form>
</div>
<?php if(empty($gallery_items)):?>
<p class="text-gray-500">Belum ada item galeri.</p>
<?php else:?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
<?php foreach($gallery_items as $item):?>
<div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
<div class="relative group">
<img src="beranda/gallery/<?php echo str_replace(['uploads/gallery/','beranda/gallery/'],'',$item['image_path']);?>" alt="<?php echo $item['title'];?>" class="w-full h-64 object-cover transition-transform duration-300 group-hover:scale-105">
<div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center">
<button onclick="openModal('<?php echo $item['image_path'];?>','<?php echo $item['title'];?>','<?php echo $item['description'];?>','<?php echo date('d M Y',strtotime($item['created_at']));?>')" class="opacity-0 group-hover:opacity-100 bg-white text-gray-800 px-4 py-2 rounded-full font-medium hover:bg-gray-100 transition-all duration-300">
<i class="fas fa-eye mr-2"></i>Lihat
</button>
</div>
</div>
<div class="p-6">
<h4 class="font-bold text-xl mb-2 text-gray-800"><?php echo $item['title'];?></h4>
<p class="text-gray-600 mb-4 leading-relaxed"><?php echo $item['description'];?></p>
<div class="flex justify-end">
<form method="POST" onsubmit="return confirm('Hapus item ini?')" class="inline">
<input type="hidden" name="id" value="<?php echo $item['id'];?>">
<button type="submit" name="delete_gallery" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-6 py-2 rounded-lg font-medium transition-all duration-300 transform hover:scale-105">
<i class="fas fa-trash-alt mr-2"></i>Hapus
</button>
</form>
</div>
</div>
</div>
<?php endforeach;?>
</div>
<div id="gallery-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50 p-4">
<div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden relative">
<div class="p-6 border-b border-gray-200">
<div class="flex justify-between items-center">
<h3 id="modal-title" class="text-2xl font-bold text-gray-800"></h3>
<button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-300">
<i class="fas fa-times text-2xl"></i>
</button>
</div>
</div>
<div class="p-6">
<img id="modal-image" src="" alt="" class="w-full h-auto max-h-[60vh] object-contain rounded-lg">
<div class="mt-4">
<p id="modal-description" class="text-gray-600 mb-2"></p>
<p id="modal-date" class="text-sm text-gray-500">
<i class="fas fa-calendar-alt mr-2"></i>
</p>
</div>
</div>
</div>
</div>
<?php endif;?>
</div>
</div>
</div>
<script src="beranda/gallery.js"></script>

