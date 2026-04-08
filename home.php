<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lurahgo.id</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="static/css/gallery-modern.css">
    <link rel="stylesheet" href="static/css/scroll-animations.css">
    <link rel="stylesheet" href="static/css/whatsapp-float.css">
    <script>
      // Smooth scroll for gallery nav
      document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('a[href=\"#gallery\"]').forEach(anchor => {
          anchor.addEventListener('click', e => {
            e.preventDefault();
            document.querySelector('#gallery')?.scrollIntoView({ 
              behavior: 'smooth', 
              block: 'start'
            });
          });
        });
      });
    </script>

</head>
<body class="bg-gray-50">
    <?php include 'beranda/header.php'; ?>
    <?php include 'beranda/hero.php'; ?>
    <?php include 'beranda/services.php'; ?>
    <?php include 'beranda/about.php'; ?>
<?php include 'beranda/gallery.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/vanilla-tilt@1.7.2/dist/vanilla-tilt.min.js"></script>
    <script src="beranda/script/scroll-animations.js"></script>

    <?php include 'beranda/testimonials.php'; ?>
    <?php include 'beranda/announcements.php'; ?>
    <?php include 'beranda/footer.php'; ?>
</body>
</html>
