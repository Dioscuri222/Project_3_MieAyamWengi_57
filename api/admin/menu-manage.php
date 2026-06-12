<?php
// Admin Menu CRUD Manager - Mie Ayam Wengi 57
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$success_msg = '';
$error_msg = '';

// Make sure uploads directory exists
$upload_dir = '../uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// --------------------------------------------------------
// CRITICAL CRUD CONTROLLER
// --------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // CREATE (ADD MENU)
    if ($action === 'add') {
        $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS));
        $category = trim(filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS));
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        $description = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS));
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        
        $image_path = '';

        // Handle image file upload
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['image_file']['tmp_name'];
            $file_name = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES['image_file']['name']));
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($file_tmp, $target_file)) {
                $image_path = 'uploads/' . $file_name;
            }
        }
        
        // Fallback to image URL if no file is uploaded
        if (empty($image_path)) {
            $image_path = trim(filter_input(INPUT_POST, 'image_url', FILTER_DEFAULT));
        }

        // Validate
        if (empty($name) || empty($category) || $price === false || empty($image_path)) {
            $error_msg = 'Nama, Kategori, Harga, dan Gambar wajib diisi.';
        } else {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO menus (name, category, price, description, image_path, is_available) 
                    VALUES (:name, :category, :price, :description, :image_path, :is_available)
                ");
                $stmt->execute([
                    ':name' => $name,
                    ':category' => $category,
                    ':price' => $price,
                    ':description' => $description ?: null,
                    ':image_path' => $image_path,
                    ':is_available' => $is_available
                ]);
                $success_msg = "Menu '" . htmlspecialchars($name) . "' berhasil ditambahkan.";
            } catch (PDOException $e) {
                $error_msg = 'Gagal menyimpan menu baru: ' . $e->getMessage();
            }
        }
    }

    // UPDATE (EDIT MENU)
    elseif ($action === 'edit') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS));
        $category = trim(filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS));
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        $description = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS));
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        $old_image_path = $_POST['old_image_path'];
        
        $image_path = '';

        // Handle image file upload
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['image_file']['tmp_name'];
            $file_name = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES['image_file']['name']));
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($file_tmp, $target_file)) {
                $image_path = 'uploads/' . $file_name;
            }
        }
        
        // Fallback to input URL if entered, otherwise preserve old image
        if (empty($image_path)) {
            $image_url_input = trim(filter_input(INPUT_POST, 'image_url', FILTER_DEFAULT));
            if (!empty($image_url_input)) {
                $image_path = $image_url_input;
            } else {
                $image_path = $old_image_path;
            }
        }

        // Validate
        if (!$id || empty($name) || empty($category) || $price === false) {
            $error_msg = 'Data edit menu tidak valid.';
        } else {
            try {
                $stmt = $pdo->prepare("
                    UPDATE menus 
                    SET name = :name, category = :category, price = :price, description = :description, image_path = :image_path, is_available = :is_available 
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':id' => $id,
                    ':name' => $name,
                    ':category' => $category,
                    ':price' => $price,
                    ':description' => $description ?: null,
                    ':image_path' => $image_path,
                    ':is_available' => $is_available
                ]);
                $success_msg = "Menu '" . htmlspecialchars($name) . "' berhasil diperbarui.";
            } catch (PDOException $e) {
                $error_msg = 'Gagal menyimpan pembaruan menu: ' . $e->getMessage();
            }
        }
    }

    // DELETE MENU
    elseif ($action === 'delete') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            try {
                // Fetch menu details to show name in success alert
                $name_stmt = $pdo->prepare("SELECT name FROM menus WHERE id = ?");
                $name_stmt->execute([$id]);
                $menu_name = $name_stmt->fetchColumn();

                $stmt = $pdo->prepare("DELETE FROM menus WHERE id = ?");
                $stmt->execute([$id]);
                
                $success_msg = "Menu '" . htmlspecialchars($menu_name) . "' berhasil dihapus secara permanen.";
            } catch (PDOException $e) {
                $error_msg = 'Gagal menghapus menu: ' . $e->getMessage();
            }
        }
    }
}

// READ (FETCH ALL MENUS)
try {
    $stmt = $pdo->query("SELECT * FROM menus ORDER BY category ASC, name ASC");
    $menus = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Gagal mengambil data menu: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu CRUD - Mie Ayam Wengi 57</title>
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <!-- Custom Style -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <!-- Admin Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-wengi sticky-top py-3">
        <div class="container">
            <a class="navbar-brand text-gradient" href="dashboard.php">
                <i class="bi bi-fire text-warning"></i> WENGI 57 ADMIN
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php"><i class="bi bi-receipt me-1"></i> Riwayat Pesanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="menu-manage.php"><i class="bi bi-menu-button-wide me-1"></i> CRUD Menu</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted fs-7 d-none d-lg-inline"><i class="bi bi-person-circle text-warning me-1"></i> <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    <a href="../logout.php" class="btn btn-danger btn-sm px-3 py-2"><i class="bi bi-box-arrow-right"></i> Keluar</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Workspace -->
    <main class="container py-5">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h1 class="text-white fw-bold mb-1">Manajemen Menu Kuliner</h1>
                <p class="text-muted mb-0">Tambah, ubah, dan hapus sajian menu Mie Ayam Wengi 57.</p>
            </div>
            <div>
                <button type="button" class="btn btn-wengi-primary" data-bs-toggle="modal" data-bs-target="#addMenuModal">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Menu Baru
                </button>
            </div>
        </div>

        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success card-wengi text-white py-3 border-success mb-4" role="alert">
                <i class="bi bi-check-circle-fill text-success me-2"></i> <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger card-wengi text-white py-3 border-danger mb-4" role="alert">
                <i class="bi bi-exclamation-octagon-fill text-danger me-2"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <!-- Menus CRUD Listing Table -->
        <div class="card-wengi p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h5 fw-bold text-white mb-0">Daftar Hidangan Kuliner</h2>
                <span class="text-muted fs-7">Terdaftar **<?php echo count($menus); ?>** menu</span>
            </div>

            <div class="table-responsive">
                <table class="table table-wengi table-hover mb-0">
                    <thead>
                        <tr>
                            <th>GAMBAR</th>
                            <th>NAMA HIDANGAN</th>
                            <th>KATEGORI</th>
                            <th>DESKRIPSI</th>
                            <th>HARGA SATUAN</th>
                            <th>STATUS</th>
                            <th class="text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($menus)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-egg-fried fs-2 mb-2 d-block text-secondary"></i>
                                    Belum ada menu yang didaftarkan. Klik "Tambah Menu Baru" untuk memulai!
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($menus as $menu): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo (strpos($menu['image_path'], 'http') === 0 ? '' : '../') . htmlspecialchars($menu['image_path']); ?>" 
                                             alt="<?php echo htmlspecialchars($menu['name']); ?>" 
                                             class="rounded border border-color" 
                                             style="width: 55px; height: 55px; object-fit: cover;"
                                             onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=100&auto=format&fit=crop&q=80'">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-white"><?php echo htmlspecialchars($menu['name']); ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary py-1 px-2.5 border border-color fs-8">
                                            <?php echo htmlspecialchars($menu['category']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-muted text-truncate fs-7" style="max-width: 250px;" title="<?php echo htmlspecialchars($menu['description']); ?>">
                                            <?php echo htmlspecialchars($menu['description'] ?: '-'); ?>
                                        </div>
                                    </td>
                                    <td class="fw-bold text-gradient">
                                        Rp <?php echo number_format($menu['price'], 0, ',', '.'); ?>
                                    </td>
                                    <td>
                                        <?php if ($menu['is_available'] == 1): ?>
                                            <span class="status-badge badge-completed" style="padding: 4px 10px; font-size: 0.7rem;">Tersedia</span>
                                        <?php else: ?>
                                            <span class="status-badge badge-cancelled" style="padding: 4px 10px; font-size: 0.7rem;">Habis</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <!-- Populate Edit Modal via Dataset JS -->
                                            <button type="button" class="btn btn-wengi-outline btn-sm py-1.5 px-2.5 edit-menu-btn"
                                                    data-id="<?php echo $menu['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($menu['name']); ?>"
                                                    data-category="<?php echo htmlspecialchars($menu['category']); ?>"
                                                    data-price="<?php echo $menu['price']; ?>"
                                                    data-description="<?php echo htmlspecialchars($menu['description']); ?>"
                                                    data-image="<?php echo htmlspecialchars($menu['image_path']); ?>"
                                                    data-available="<?php echo $menu['is_available']; ?>">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            
                                            <!-- Delete Button triggers Confirm Modal -->
                                            <button type="button" class="btn btn-danger btn-sm py-1.5 px-2.5 delete-menu-btn" 
                                                    data-id="<?php echo $menu['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($menu['name']); ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- CREATE MODAL: ADD MENU -->
    <div class="modal fade" id="addMenuModal" tabindex="-1" aria-labelledby="addMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-wengi">
                <div class="modal-header border-color">
                    <h5 class="modal-title fw-bold text-white" id="addMenuModalLabel">
                        <i class="bi bi-plus-circle text-warning me-1"></i> Daftarkan Menu Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="menu-manage.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <input type="hidden" name="action" value="add">

                        <div class="mb-3">
                            <label for="name" class="form-label text-muted fs-7 fw-semibold">NAMA HIDANGAN</label>
                            <input type="text" class="form-control form-wengi bg-primary" id="name" name="name" placeholder="Contoh: Mie Ayam Bakso Jumbo..." required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="category" class="form-label text-muted fs-7 fw-semibold">KATEGORI</label>
                                <select class="form-select form-wengi bg-primary" id="category" name="category" required>
                                    <option value="" disabled selected>Pilih...</option>
                                    <option value="Mie Ayam">Mie Ayam</option>
                                    <option value="Sampingan">Sampingan</option>
                                    <option value="Minuman">Minuman</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="price" class="form-label text-muted fs-7 fw-semibold">HARGA (RP)</label>
                                <input type="number" class="form-control form-wengi bg-primary" id="price" name="price" placeholder="15000..." required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted fs-7 fw-semibold d-block">SUMBER GAMBAR</label>
                            <!-- Image URL input -->
                            <div class="mb-2">
                                <input type="text" class="form-control form-wengi bg-primary" name="image_url" placeholder="URL Gambar Online (https://...)...">
                                <span class="fs-8 text-muted">Tempel link gambar Unsplash/kuliner online lainnya.</span>
                            </div>
                            <!-- Image File input -->
                            <div class="text-center text-muted my-2 border-bottom border-color pb-2" style="font-size: 0.8rem;">ATAU UPLOAD GAMBAR LOCAL</div>
                            <div>
                                <input type="file" class="form-control form-wengi bg-primary" name="image_file" accept="image/*">
                                <span class="fs-8 text-muted">Direkomendasikan foto kuliner berformat JPG/PNG.</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label text-muted fs-7 fw-semibold">DESKRIPSI SAJIAN</label>
                            <textarea class="form-control form-wengi bg-primary" id="description" name="description" rows="3" placeholder="Masukkan deskripsi rasa hidangan, kelengkapan kuah dsb..."></textarea>
                        </div>

                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_available" name="is_available" checked value="1">
                            <label class="form-check-label text-white fw-semibold" for="is_available">Status Menu: Tersedia / Aktif</label>
                        </div>

                    </div>
                    <div class="modal-footer border-color">
                        <button type="button" class="btn btn-wengi-outline" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-wengi-primary">Simpan Menu Baru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- UPDATE MODAL: EDIT MENU -->
    <div class="modal fade" id="editMenuModal" tabindex="-1" aria-labelledby="editMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-wengi">
                <div class="modal-header border-color">
                    <h5 class="modal-title fw-bold text-white" id="editMenuModalLabel">
                        <i class="bi bi-pencil-square text-warning me-1"></i> Ubah Detail Menu
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="menu-manage.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit-id">
                        <input type="hidden" name="old_image_path" id="edit-old-image">

                        <div class="mb-3">
                            <label for="edit-name" class="form-label text-muted fs-7 fw-semibold">NAMA HIDANGAN</label>
                            <input type="text" class="form-control form-wengi bg-primary" id="edit-name" name="name" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="edit-category" class="form-label text-muted fs-7 fw-semibold">KATEGORI</label>
                                <select class="form-select form-wengi bg-primary" id="edit-category" name="category" required>
                                    <option value="Mie Ayam">Mie Ayam</option>
                                    <option value="Sampingan">Sampingan</option>
                                    <option value="Minuman">Minuman</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-price" class="form-label text-muted fs-7 fw-semibold">HARGA (RP)</label>
                                <input type="number" class="form-control form-wengi bg-primary" id="edit-price" name="price" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted fs-7 fw-semibold d-block">UBAH GAMBAR (KOSONGKAN JIKA TIDAK DIUBAH)</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <img src="" id="edit-image-preview" class="rounded border border-color" style="width: 50px; height: 50px; object-fit: cover;">
                                <span class="fs-8 text-muted">Gambar yang saat ini digunakan.</span>
                            </div>
                            <!-- Image URL input -->
                            <div class="mb-2">
                                <input type="text" class="form-control form-wengi bg-primary" id="edit-image-url" name="image_url" placeholder="Masukkan URL gambar baru jika diganti...">
                            </div>
                            <!-- Image File input -->
                            <div class="text-center text-muted my-2 border-bottom border-color pb-2" style="font-size: 0.8rem;">ATAU UPLOAD GAMBAR LOCAL BARU</div>
                            <div>
                                <input type="file" class="form-control form-wengi bg-primary" name="image_file" accept="image/*">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit-description" class="form-label text-muted fs-7 fw-semibold">DESKRIPSI SAJIAN</label>
                            <textarea class="form-control form-wengi bg-primary" id="edit-description" name="description" rows="3"></textarea>
                        </div>

                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" role="switch" id="edit-available" name="is_available" value="1">
                            <label class="form-check-label text-white fw-semibold" for="edit-available">Status Menu: Tersedia / Aktif</label>
                        </div>

                    </div>
                    <div class="modal-footer border-color">
                        <button type="button" class="btn btn-wengi-outline" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-wengi-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div class="modal fade" id="deleteMenuModal" tabindex="-1" aria-labelledby="deleteMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content card-wengi" style="border-color: #ef4444;">
                <div class="modal-body text-center p-4">
                    <div class="text-danger mb-3" style="font-size: 3rem;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2 text-white">Hapus Menu?</h5>
                    <p class="text-muted fs-7" id="delete-alert-text">Apakah Anda yakin ingin menghapus menu ini secara permanen dari database?</p>
                    
                    <form action="menu-manage.php" method="POST">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="delete-id">
                        
                        <div class="d-flex gap-2 justify-content-center mt-4">
                            <button type="button" class="btn btn-wengi-outline btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger btn-sm px-3">Hapus Permanen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS CRUD Interactive Helpers -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const editModal = new bootstrap.Modal(document.getElementById('editMenuModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteMenuModal'));

            // Edit Button Trigger
            document.querySelectorAll('.edit-menu-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.dataset.id;
                    const name = button.dataset.name;
                    const category = button.dataset.category;
                    const price = button.dataset.price;
                    const description = button.dataset.description;
                    const image = button.dataset.image;
                    const available = parseInt(button.dataset.available);

                    // Prefill Modal Fields
                    document.getElementById('edit-id').value = id;
                    document.getElementById('edit-name').value = name;
                    document.getElementById('edit-category').value = category;
                    document.getElementById('edit-price').value = Math.round(price);
                    document.getElementById('edit-description').value = description;
                    document.getElementById('edit-old-image').value = image;
                    
                    // Handle image preview path
                    const previewImg = document.getElementById('edit-image-preview');
                    if (image.startsWith('http')) {
                        previewImg.src = image;
                    } else {
                        previewImg.src = '../' + image;
                    }

                    // Set switch availability state
                    document.getElementById('edit-available').checked = (available === 1);

                    editModal.show();
                });
            });

            // Delete Button Trigger
            document.querySelectorAll('.delete-menu-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.dataset.id;
                    const name = button.dataset.name;

                    document.getElementById('delete-id').value = id;
                    document.getElementById('delete-alert-text').innerHTML = `Apakah Anda yakin ingin menghapus hidangan <strong>${name}</strong> secara permanen?`;

                    deleteModal.show();
                });
            });
        });
    </script>
</body>
</html>
