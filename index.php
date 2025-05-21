<?php
// Mulai sesi jika menggunakan fitur login

require_once 'includes/config.php';
session_start();

// Query untuk mengambil data dari tabel menu
$query = "SELECT * FROM menu";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Evelyn's Kitchen</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans|Playfair+Display|Poppins" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
  <!-- Top Bar -->
  <!-- <div id="topbar" class="d-flex align-items-center fixed-top">
    <div class="container d-flex justify-content-center justify-content-md-between">
      <div class="contact-info">
        <i class="bi bi-phone"></i><span>+1 5589 55488 55</span>
        <i class="bi bi-clock ms-4"></i><span> Mon-Sat: 11AM - 11PM</span>
      </div>
    </div>
  </div> -->

  <!-- Header -->
  <header id="header" class="fixed-top">
    <div class="container d-flex align-items-center justify-content-between">
      <h1 class="logo"><a href="#">Evelyn's Kitchen</a></h1>
      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto" href="#hero">Home</a></li>
          <li><a class="nav-link scrollto" href="#menu">Menu</a></li>
          <li><a class="nav-link scrollto" href="#contact">Contact</a></li>
          <li><a class="nav-link scrollto" href="login.html">Login</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>
    </div>
  </header>

  <!-- Hero Section -->
  <section id="hero" class="d-flex align-items-center">
    <div class="container text-center" data-aos="fade-up">
      <h1>Welcome to Evelyn's Kitchen</h1>
      <a href="#menu" class="btn-menu scrollto">Our Menu</a>
    </div>
  </section>


    <!-- ======= Menu Section ======= -->
    <section id="menu" class="menu section-bg">
    <div class="container" data-aos="fade-up">

        <div class="section-title">
        <h2>Menu</h2>
        <p>Check Our Tasty Menu</p>
        </div>

        <div class="row menu-container" data-aos="fade-up" data-aos-delay="200">
        <?php
        // Menampilkan setiap menu dari query
        while ($row = mysqli_fetch_assoc($result)) {
            $namaMenu = $row['namaMenu'];
            $deskripsi = $row['deskripsi'];
            $harga = $row['harga'];
            $gambar = $row['gambar']; // Pastikan gambar ada di folder yang sesuai

            // Menampilkan item menu
            echo '
            <div class="col-lg-6 menu-item filter-starters">
            <img src="image/' . $gambar . '" class="menu-img" alt="">
            <div class="menu-content">
                <a href="#">' . $namaMenu . '</a><span>' . $harga . '</span>
            </div>
            <div class="menu-ingredients">
                ' . $deskripsi . '
            </div>
            </div>';
        }
        ?>
        </div>
    </div>
    </section><!-- End Menu Section -->

<!-- ======= Contact Section ======= -->
<section id="contact" class="contact">
      <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2>Contact</h2>
          <p>Contact Us</p>
        </div>
      </div>

      <div data-aos="fade-up">
        <iframe style="border:0; width: 100%; height: 350px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.7788309755665!2d106.78941261017938!3d-6.160368693801074!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f641b3337c29%3A0x83a4e96e7d4527fd!2sJl.%20Dr.%20Makaliwe%20II%2C%20Grogol%2C%20Kec.%20Grogol%20petamburan%2C%20Kota%20Jakarta%20Barat%2C%20Daerah%20Khusus%20Ibukota%20Jakarta%2011450!5e0!3m2!1sid!2sid!4v1732561634637!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" frameborder="0" allowfullscreen></iframe>
      </div>

      <div class="container" data-aos="fade-up">

        <div class="row mt-5">

          <div class="col-lg-4">
            <div class="info">
            <div class="address">
                <i class="bi bi-geo-alt"></i>
                <h4>Location:</h4>
                <p>Jalan Makaliwe 2</p>
              </div>

              <div class="open-hours">
                <i class="bi bi-clock"></i>
                <h4>Open Hours:</h4>
                <p>
                  Monday-Saturday:<br>
                  07:00 AM - 23:00 PM
                </p>
              </div>

              <div class="email">
                <i class="bi bi-envelope"></i>
                <h4>Email:</h4>
                <p>renne@gmail.com</p>
              </div>

              <div class="phone">
                <i class="bi bi-phone"></i>
                <h4>Call:</h4>
                <p>+62 123 456 789</p>
              </div>

            </div>

          </div>

        </div>

      </div>
    </section><!-- End Contact Section -->

  <!-- Footer -->
  <footer id="footer">
    <div class="container">
      <p>&copy; 2024 Restaurantly. All Rights Reserved</p>
    </div>
  </footer>

  <!-- JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>

</html>
