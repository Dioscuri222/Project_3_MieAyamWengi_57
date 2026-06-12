<?php
// Customer Frontend - Mie Ayam Wengi 57
require_once 'config/db.php';

// Fetch all available menus grouped/ordered by category
try {
    $stmt = $pdo->prepare("SELECT * FROM menus WHERE is_available = 1 ORDER BY category DESC, name ASC");
    $stmt->execute();
    $menus = $stmt->fetchAll();
} catch (PDOException $e) {
    $menus = [];
    $error_msg = "Gagal mengambil data menu: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mie Ayam Wengi 57 - Sensasi Kuliner Mie Malam Premium</title>
    
    <!-- Meta SEO -->
    <meta name="description" content="Nikmati kelezatan mie ayam premium legendaris Mie Ayam Wengi 57. Disajikan hangat di malam hari dengan potongan ayam melimpah dan kaldu gurih.">
    <meta name="author" content="Mie Ayam Wengi 57">
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <!-- Custom Style -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Header / Navbar --> 
    <nav class="navbar navbar-expand-lg navbar-dark navbar-wengi fixed-top">
        <div class="container">
            <a class="navbar-brand text-gradient" href="index.php">
                <i class="bi bi-fire text-warning"></i>
                MIE AYAM WENGI 57
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#menu">Menu Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">Tentang Wengi</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-wengi-outline btn-sm py-2 px-3 mt-2 mt-lg-0" href="login.php">
                            <i class="bi bi-person-lock me-1"></i> Admin Portal
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header id="home" class="wengi-hero d-flex align-items-center">
        <div class="container">
            <div class="row align-items-center gy-5">
                <div class="col-lg-7 text-center text-lg-start fade-in-up">
                    <span class="badge bg-warning text-dark mb-3 px-3 py-2 fw-bold text-uppercase tracking-wider" style="border-radius: 30px;">
                        🌙 Citarasa Malam Legendaris
                    </span>
                    <h1 class="display-3 fw-extrabold text-white mb-3">
                        Kelezatan Hangat Di Kesunyian <span class="text-gradient serif-title">Wengi</span>.
                    </h1>
                    <p class="lead text-muted mb-4 fs-5" style="max-width: 600px;">
                        Menghadirkan semangkuk mie ayam kenyal homemade dengan olahan daging ayam kecap rempah melimpah, disajikan fresh di bawah gemerlap malam kota Jogja.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
                        <a href="#menu" class="btn btn-wengi-primary btn-lg">
                            <i class="bi bi-journal-richtext me-2"></i> Jelajahi Menu
                        </a>
                        <a href="#about" class="btn btn-wengi-outline btn-lg">
                            Keunikan Kami
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 text-center fade-in-up" style="animation-delay: 0.2s;">
                    <div class="position-relative d-inline-block">
                        <div class="position-absolute translate-middle-y start-0 top-50 w-100 h-100 bg-warning rounded-circle opacity-10 filter-blur-xl" style="filter: blur(80px); z-index: -1;"></div>
                        <img src="https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=600&auto=format&fit=crop&q=80" alt="Mie Ayam Premium" class="img-fluid rounded-4 shadow-lg border border-color" style="max-width: 90%; transform: rotate(1deg); transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.02) rotate(-1deg)'" onmouseout="this.style.transform='rotate(1deg)'">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Noodle Menu Catalog (Read-Only Menu) -->
    <section id="menu" class="py-5" style="background-color: var(--bg-secondary);">
        <div class="container py-4">
            
            <div class="text-center mb-5 fade-in-up">
                <h2 class="display-5 fw-bold text-white mb-2">Daftar Menu <span class="text-gradient">Wengi 57</span></h2>
                <p class="text-muted mx-auto" style="max-width: 580px;">Semua menu kami dibuat secara higienis, halal, dan disajikan hangat terpercaya dari resep keluarga turun-temurun.</p>
            </div>

            <!-- Categories Tabs -->
            <div class="d-flex justify-content-center mb-5 fade-in-up" style="animation-delay: 0.1s;">
                <div class="nav nav-pills nav-pills-wengi" id="menu-categories" role="tablist">
                    <button class="nav-link active" id="cat-all-tab" data-bs-toggle="pill" data-bs-target="#cat-all" type="button" role="tab" aria-controls="cat-all" aria-selected="true">
                        🍲 Semua Menu
                    </button>
                    <button class="nav-link" id="cat-mie-tab" data-bs-toggle="pill" data-bs-target="#cat-mie" type="button" role="tab" aria-controls="cat-mie" aria-selected="false">
                        🍜 Mie Ayam
                    </button>
                    <button class="nav-link" id="cat-side-tab" data-bs-toggle="pill" data-bs-target="#cat-side" type="button" role="tab" aria-controls="cat-side" aria-selected="false">
                        🥟 Sampingan
                    </button>
                    <button class="nav-link" id="cat-drink-tab" data-bs-toggle="pill" data-bs-target="#cat-drink" type="button" role="tab" aria-controls="cat-drink" aria-selected="false">
                        🥤 Minuman
                    </button>
                </div>
            </div>

            <?php if (isset($error_msg)): ?>
                <div class="alert alert-danger card-wengi text-white text-center border-danger py-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-2 text-danger mb-2 d-block"></i>
                    <?php echo $error_msg; ?>
                </div>
            <?php else: ?>
                
                <div class="tab-content" id="menu-tabs-content">
                    
                    <!-- 1. TAB: ALL MENUS -->
                    <div class="tab-pane fade show active" id="cat-all" role="tabpanel" aria-labelledby="cat-all-tab">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                            <?php foreach ($menus as $menu): ?>
                                <div class="col fade-in-up">
                                    <div class="card-wengi">
                                        <div class="card-img-wrapper">
                                            <img src="<?php echo htmlspecialchars($menu['image_path']); ?>" alt="<?php echo htmlspecialchars($menu['name']); ?>" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop&q=80'">
                                            <span class="position-absolute top-3 end-3 badge bg-dark bg-opacity-75 text-warning px-2.5 py-1.5 border border-color" style="top: 12px; right: 12px;">
                                                <?php echo htmlspecialchars($menu['category']); ?>
                                            </span>
                                        </div>
                                        <div class="card-body-wengi">
                                            <div>
                                                <h3 class="food-title"><?php echo htmlspecialchars($menu['name']); ?></h3>
                                                <p class="food-desc"><?php echo htmlspecialchars($menu['description']); ?></p>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-auto">
                                                <span class="food-price">Rp <?php echo number_format($menu['price'], 0, ',', '.'); ?></span>
                                                <button class="btn btn-wengi-primary btn-sm btn-add-to-cart" 
                                                        data-id="<?php echo $menu['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($menu['name']); ?>"
                                                        data-price="<?php echo $menu['price']; ?>"
                                                        data-image="<?php echo htmlspecialchars($menu['image_path']); ?>">
                                                    <i class="bi bi-cart-plus me-1"></i> + Keranjang
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- 2. TAB: MIE AYAM -->
                    <div class="tab-pane fade" id="cat-mie" role="tabpanel" aria-labelledby="cat-mie-tab">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                            <?php 
                            $mie_found = false;
                            foreach ($menus as $menu): 
                                if ($menu['category'] === 'Mie Ayam'):
                                    $mie_found = true;
                            ?>
                                <div class="col">
                                    <div class="card-wengi">
                                        <div class="card-img-wrapper">
                                            <img src="<?php echo htmlspecialchars($menu['image_path']); ?>" alt="<?php echo htmlspecialchars($menu['name']); ?>" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop&q=80'">
                                        </div>
                                        <div class="card-body-wengi">
                                            <div>
                                                <h3 class="food-title"><?php echo htmlspecialchars($menu['name']); ?></h3>
                                                <p class="food-desc"><?php echo htmlspecialchars($menu['description']); ?></p>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-auto">
                                                <span class="food-price">Rp <?php echo number_format($menu['price'], 0, ',', '.'); ?></span>
                                                <button class="btn btn-wengi-primary btn-sm btn-add-to-cart" 
                                                        data-id="<?php echo $menu['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($menu['name']); ?>"
                                                        data-price="<?php echo $menu['price']; ?>"
                                                        data-image="<?php echo htmlspecialchars($menu['image_path']); ?>">
                                                    <i class="bi bi-cart-plus me-1"></i> + Keranjang
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                endif;
                            endforeach; 
                            if (!$mie_found):
                            ?>
                                <div class="col-12 text-center text-muted py-5">Belum ada menu mie ayam yang aktif.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 3. TAB: SAMPINGAN -->
                    <div class="tab-pane fade" id="cat-side" role="tabpanel" aria-labelledby="cat-side-tab">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                            <?php 
                            $side_found = false;
                            foreach ($menus as $menu): 
                                if ($menu['category'] === 'Sampingan'):
                                    $side_found = true;
                            ?>
                                <div class="col">
                                    <div class="card-wengi">
                                        <div class="card-img-wrapper">
                                            <img src="<?php echo htmlspecialchars($menu['image_path']); ?>" alt="<?php echo htmlspecialchars($menu['name']); ?>" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop&q=80'">
                                        </div>
                                        <div class="card-body-wengi">
                                            <div>
                                                <h3 class="food-title"><?php echo htmlspecialchars($menu['name']); ?></h3>
                                                <p class="food-desc"><?php echo htmlspecialchars($menu['description']); ?></p>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-auto">
                                                <span class="food-price">Rp <?php echo number_format($menu['price'], 0, ',', '.'); ?></span>
                                                <button class="btn btn-wengi-primary btn-sm btn-add-to-cart" 
                                                        data-id="<?php echo $menu['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($menu['name']); ?>"
                                                        data-price="<?php echo $menu['price']; ?>"
                                                        data-image="<?php echo htmlspecialchars($menu['image_path']); ?>">
                                                    <i class="bi bi-cart-plus me-1"></i> + Keranjang
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                endif;
                            endforeach; 
                            if (!$side_found):
                            ?>
                                <div class="col-12 text-center text-muted py-5">Belum ada menu sampingan yang aktif.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 4. TAB: DRINKS -->
                    <div class="tab-pane fade" id="cat-drink" role="tabpanel" aria-labelledby="cat-drink-tab">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                            <?php 
                            $drink_found = false;
                            foreach ($menus as $menu): 
                                if ($menu['category'] === 'Minuman'):
                                    $drink_found = true;
                            ?>
                                <div class="col">
                                    <div class="card-wengi">
                                        <div class="card-img-wrapper">
                                            <img src="<?php echo htmlspecialchars($menu['image_path']); ?>" alt="<?php echo htmlspecialchars($menu['name']); ?>" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop&q=80'">
                                        </div>
                                        <div class="card-body-wengi">
                                            <div>
                                                <h3 class="food-title"><?php echo htmlspecialchars($menu['name']); ?></h3>
                                                <p class="food-desc"><?php echo htmlspecialchars($menu['description']); ?></p>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-auto">
                                                <span class="food-price">Rp <?php echo number_format($menu['price'], 0, ',', '.'); ?></span>
                                                <button class="btn btn-wengi-primary btn-sm btn-add-to-cart" 
                                                        data-id="<?php echo $menu['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($menu['name']); ?>"
                                                        data-price="<?php echo $menu['price']; ?>"
                                                        data-image="<?php echo htmlspecialchars($menu['image_path']); ?>">
                                                    <i class="bi bi-cart-plus me-1"></i> + Keranjang
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                endif;
                            endforeach; 
                            if (!$drink_found):
                            ?>
                                <div class="col-12 text-center text-muted py-5">Belum ada menu minuman yang aktif.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

            <?php endif; ?>

        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5" style="border-top: 1px solid var(--border-color); background: radial-gradient(circle at 10% 50%, rgba(255, 123, 0, 0.03) 0%, transparent 40%);">
        <div class="container py-5">
            <div class="row align-items-center gy-5">
                <div class="col-lg-5">
                    <img src="https://images.unsplash.com/photo-1552611052-33e04de081de?w=600&auto=format&fit=crop&q=80" alt="Tentang Mie Ayam Wengi 57" class="img-fluid rounded-4 border border-color shadow" style="transform: rotate(-1deg);">
                </div>
                <div class="col-lg-7">
                    <span class="text-warning fw-bold text-uppercase d-block mb-2">⭐ Filosofi Wengi 57</span>
                    <h2 class="display-6 fw-bold text-white mb-4">Citarasa Hangat di Tengah <span class="text-gradient">Kesunyian Malam</span></h2>
                    <p class="text-muted leading-relaxed mb-3">
                        Mie Ayam Wengi 57 berawal dari kerinduan penikmat kuliner Jogja akan sajian mie ayam hangat yang lezat dan berkelas di waktu larut malam. Kami menyadari bahwa kuliner malam tidak melulu harus cepat saji, melainkan bisa dinikmati dengan citarasa istimewa.
                    </p>
                    <p class="text-muted leading-relaxed mb-4">
                        Dengan mie kenyal bertekstur khas racikan sendiri, potongan daging ayam pilihan yang dimasak bumbu kecap pekat meresap, serta kuah kaldu murni yang gurih, kami siap menghangatkan suasana santap malam Anda.
                    </p>
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-dark rounded-circle d-flex align-items-center justify-content-center border border-color" style="width: 48px; height: 48px; min-width: 48px;">
                                    <i class="bi bi-shield-check text-warning fs-5"></i>
                                </div>
                                <div>
                                    <h4 class="h6 text-white mb-1">100% Halal & Segar</h4>
                                    <p class="text-muted mb-0" style="font-size: 0.85rem;">Bahan baku fresh setiap hari.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-dark rounded-circle d-flex align-items-center justify-content-center border border-color" style="width: 48px; height: 48px; min-width: 48px;">
                                    <i class="bi bi-clock-history text-warning fs-5"></i>
                                </div>
                                <div>
                                    <h4 class="h6 text-white mb-1">Buka Hingga Larut</h4>
                                    <p class="text-muted mb-0" style="font-size: 0.85rem;">Setiap malam 17.00 - 02.00 WIB.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5" style="background-color: #05070a; border-top: 1px solid var(--border-color);">
        <div class="container text-center">
            <div class="mb-4">
                <a href="#" class="fs-4 fw-extrabold text-gradient text-decoration-none">
                    <i class="bi bi-fire text-warning"></i> MIE AYAM WENGI 57
                </a>
            </div>
            <p class="text-muted" style="max-width: 500px; margin: 0 auto 24px; font-size: 0.9rem;">
                Jl. Kaliurang No. 57, Yogyakarta<br>
                Hubungi Kami: +62 857-5757-5757 | Jam Buka: 17:00 - 02:00 WIB
            </p>
            <div class="d-flex justify-content-center gap-3 mb-4">
                <a href="#" class="btn btn-dark btn-sm rounded-circle border border-color" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-instagram text-white"></i></a>
                <a href="#" class="btn btn-dark btn-sm rounded-circle border border-color" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-tiktok text-white"></i></a>
                <a href="#" class="btn btn-dark btn-sm rounded-circle border border-color" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-whatsapp text-white"></i></a>
            </div>
            <hr class="my-4 border-secondary opacity-25">
            <p class="text-muted mb-0" style="font-size: 0.8rem;">&copy; <?php echo date('Y'); ?> Mie Ayam Wengi 57. Seluruh Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <!-- Floating Cart Trigger Button -->
    <button class="btn-cart-float" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartDrawer" aria-controls="cartDrawer">
        <i class="bi bi-bag-heart fs-4"></i>
        <span id="cart-badge" class="cart-badge" style="display: none;">0</span>
    </button>

    <!-- Shopping Cart Drawer (Offcanvas) -->
    <div class="offcanvas offcanvas-end offcanvas-wengi" tabindex="-1" id="cartDrawer" aria-labelledby="cartDrawerLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title fw-bold text-white d-flex align-items-center gap-2" id="cartDrawerLabel">
                <i class="bi bi-cart3 text-warning"></i> Keranjang Wengi Anda
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column justify-content-between h-100">
            
            <!-- Items Area -->
            <div class="flex-grow-1 overflow-y-auto pe-2" style="max-height: calc(100vh - 350px);">
                <!-- Empty State -->
                <div id="cart-empty-state" class="text-center py-5">
                    <i class="bi bi-basket text-muted mb-3 d-block" style="font-size: 3rem;"></i>
                    <p class="text-muted">Keranjang belanja Anda masih kosong.<br>Pilih menu lezat kami di sebelah kiri.</p>
                </div>
                <!-- Dynamic Items Container -->
                <div id="cart-items-container"></div>
            </div>

            <!-- Checkout Form & Pricing Summary -->
            <div id="cart-checkout-section" class="pt-4 border-top border-color d-none">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted fw-semibold">Total Pembayaran:</span>
                    <span id="cart-total-amount" class="fs-4 fw-extrabold text-gradient">Rp 0</span>
                </div>
                
                <button class="btn btn-wengi-primary w-100 py-3 mb-2" data-bs-toggle="modal" data-bs-target="#checkoutModal">
                    <i class="bi bi-cursor me-2"></i> Lanjutkan Pemesanan
                </button>
            </div>

        </div>
    </div>

    <!-- Checkout Details Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-wengi">
                <div class="modal-header border-color">
                    <h5 class="modal-title fw-bold text-white" id="checkoutModalLabel">
                        <i class="bi bi-journal-check text-warning me-2"></i> Detail Pemesan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="checkout-form">
                    <div class="modal-body p-4">
                        
                        <!-- Hidden Inputs for JS integration -->
                        <input type="hidden" name="cart_items" id="checkout-cart-items-input">
                        <input type="hidden" name="total_amount" id="checkout-total-amount-input">

                        <div class="mb-3">
                            <label for="customer_name" class="form-label text-muted fs-7 fw-semibold">NAMA LENGKAP</label>
                            <input type="text" class="form-control form-wengi" id="customer_name" name="customer_name" placeholder="Masukkan nama Anda..." required>
                        </div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-5">
                                <label for="table_number" class="form-label text-muted fs-7 fw-semibold">NOMOR MEJA</label>
                                <select class="form-select form-wengi" id="table_number" name="table_number" required>
                                    <option value="" disabled selected>Pilih...</option>
                                    <?php for($i=1; $i<=25; $i++): ?>
                                        <option value="<?php echo sprintf('%02d', $i); ?>">Meja <?php echo sprintf('%02d', $i); ?></option>
                                    <?php endfor; ?>
                                    <option value="Takeaway">Bungkus / Takeaway</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <label for="phone_number" class="form-label text-muted fs-7 fw-semibold">NOMOR WHATSAPP</label>
                                <input type="tel" class="form-control form-wengi" id="phone_number" name="phone_number" placeholder="Contoh: 0857..." required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label text-muted fs-7 fw-semibold">CATATAN KHUSUS (OPSIONAL)</label>
                            <textarea class="form-control form-wengi" id="notes" name="notes" rows="2" placeholder="Contoh: Mie gak pakai daun bawang, kuah dipisah..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label text-muted fs-7 fw-semibold">METODE PEMBAYARAN</label>
                            <select class="form-select form-wengi" id="payment_method" name="payment_method" required>
                                <option value="Tunai" selected>Cash / Tunai (Bayar di Kasir)</option>
                                <option value="QRIS">QRIS / E-Wallet</option>
                            </select>
                        </div>

                        <div class="p-3 bg-secondary rounded-3 border border-color mb-3" style="font-size: 0.85rem;">
                            <span class="text-warning d-block mb-1"><i class="bi bi-info-circle me-1"></i> Informasi Pembayaran</span>
                            <span class="text-muted" id="payment-info-text">Silakan tunjukkan Invoice pesanan digital yang muncul setelah pemesanan dikonfirmasi ke kasir untuk proses pembayaran Tunai.</span>
                        </div>

                    </div>
                    <div class="modal-footer border-color">
                        <button type="button" class="btn btn-wengi-outline" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-wengi-primary">
                            <i class="bi bi-check-circle me-1"></i> Konfirmasi Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Cart System Logic -->
    <script src="assets/js/cart.js"></script>
</body>
</html>
