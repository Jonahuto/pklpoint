<?php
session_start();
require '../config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Chek if Role = 'mahasiswa'
if ($_SESSION['role'] !== 'mahasiswa') {
    echo "Akses ditolak. Halaman ini hanya untuk mahasiswa.";
    exit();
}

$user_id = $_SESSION['user_id'];

$query = $conn->prepare("SELECT * FROM kegiatan WHERE id_user = ? ORDER BY tanggal DESC");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$kegiatan_data = [];

while ($row = $result->fetch_assoc()) {
    $kegiatan_data[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus']) && isset($_POST['id_kegiatan'])) {
    $id_kegiatan = $_POST['id_kegiatan'];

    // Ambil nama file untuk dihapus dari direktori
    $stmt_file = $conn->prepare("SELECT file FROM kegiatan WHERE id_kegiatan = ? AND id_user = ?");
    $stmt_file->bind_param("ii", $id_kegiatan, $user_id);
    $stmt_file->execute();
    $result_file = $stmt_file->get_result();

    if ($result_file->num_rows > 0) {
        $row = $result_file->fetch_assoc();
        $file_name = $row['file'];

        // Hapus file dari folder jika ada
        if (!empty($file_name) && file_exists("uploads/" . $file_name)) {
            unlink("uploads/" . $file_name);
        }

        // Hapus data dari database
        $stmt_delete = $conn->prepare("DELETE FROM kegiatan WHERE id_kegiatan = ? AND id_user = ?");
        $stmt_delete->bind_param("ii", $id_kegiatan, $user_id);
        $stmt_delete->execute();
        $stmt_delete->close();

        // Redirect ulang agar halaman diperbarui
        header("Location: kegiatan.php");
        exit();
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama_kegiatan'])) {
    $nama_kegiatan = $_POST['nama_kegiatan'];
    $minggu = $_POST['minggu'];
    $tanggal = date('Y-m-d'); // tanggal input hari ini

    // Validasi dan upload file
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // buat folder jika belum ada
        }

        $file_tmp = $_FILES['file']['tmp_name'];
        $file_name = basename($_FILES['file']['name']);
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file_name = uniqid('kegiatan_', true) . '.' . $file_ext;
        $destination = $upload_dir . $new_file_name;

        if (move_uploaded_file($file_tmp, $destination)) {
            // Simpan ke database
            $stmt = $conn->prepare("INSERT INTO kegiatan (id_user, nama_kegiatan, minggu, file, tanggal) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $user_id, $nama_kegiatan, $minggu, $new_file_name, $tanggal);
            $stmt->execute();
            $stmt->close();

            header("Location: kegiatan.php");
            exit();
        } else {
            echo "<script>alert('Gagal mengunggah file.');</script>";
        }
    } else {
        echo "<script>alert('File tidak valid.');</script>";
    }
}



$query = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
$user = $query->fetch_assoc();
$query = $conn->query("SELECT * FROM pkl_data WHERE user_id = $user_id");
$pkl_data = $query->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKL Point Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="../css/mhs/kegiatan.css">
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <a href="dashboard.php">
                    <span class="material-icons" aria-hidden="true"><svg height="50" width="50" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                            <path d="M220 112c0 23.6-8.29 45.23-23.35 60.88C182.52 187.57 163.33 196 144 196c-15.45 0-26.78-4.18-34.89-9.31l-9.43 40.06a12 12 0 1 1-23.36-5.5l32-136a12 12 0 1 1 23.36 5.5l-16.45 69.93C118.72 164.86 127.16 172 144 172c25.56 0 52-22.45 52-60a68 68 0 1 0-126.91 34a12 12 0 0 1-20.77 12A92 92 0 1 1 220 112Z" fill="currentColor" />
                        </svg>
                    </span>
                    PKLPoint
                </a>
            </div>
            <nav>
                <a href="dashboard.php"><svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#000000" alttext="Logo Home">
                        <path d="M240-200h120v-240h240v240h120v-360L480-740 240-560v360Zm-80 80v-480l320-240 320 240v480H520v-240h-80v240H160Zm320-350Z" />
                    </svg> Dashboard</a>
                <a href="isidata.php"><svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#000000" alttext="Logo Isi Data">
                        <path d="M200-440h240v-160H200v160Zm0-240h560v-80H200v80Zm0 560q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v252q-19-8-39.5-10.5t-40.5.5q-21 4-40.5 13.5T684-479l-39 39-205 204v116H200Zm0-80h240v-160H200v160Zm320-240h125l39-39q16-16 35.5-25.5T760-518v-82H520v160Zm0 360v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T863-300L643-80H520Zm300-263-37-37 37 37ZM580-140h38l121-122-37-37-122 121v38Zm141-141-19-18 37 37-18-19Z" />
                    </svg> Isi Data PKL</a>
                <a href="kegiatan.php" class="active"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000" alttext="Logo Kegiatan PKL">
                        <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h167q11-35 43-57.5t70-22.5q40 0 71.5 22.5T594-840h166q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560h-80v120H280v-120h-80v560Zm280-560q17 0 28.5-11.5T520-800q0-17-11.5-28.5T480-840q-17 0-28.5 11.5T440-800q0 17 11.5 28.5T480-760Z" />
                    </svg> Kegiatan PKL</a>
                <a href="laporanakhir.php"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000" alttext="Logo Laporan Akhir">
                        <path d="M120-120v-80l80-80v160h-80Zm160 0v-240l80-80v320h-80Zm160 0v-320l80 81v239h-80Zm160 0v-239l80-80v319h-80Zm160 0v-400l80-80v480h-80ZM120-327v-113l280-280 160 160 280-280v113L560-447 400-607 120-327Z" />
                    </svg> Laporan Akhir</a>
            </nav>
            <div class="online-profile">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24">
                    <path fill="#0DAE17" d="M14 19.5c0-2 1.1-3.8 2.7-4.7c-1.3-.5-2.9-.8-4.7-.8c-4.4 0-8 1.8-8 4v2h10zm5.5-3.5c-1.9 0-3.5 1.6-3.5 3.5s1.6 3.5 3.5 3.5s3.5-1.6 3.5-3.5s-1.6-3.5-3.5-3.5M16 8c0 2.2-1.8 4-4 4s-4-1.8-4-4s1.8-4 4-4s4 1.8 4 4" />
                </svg>
                <p><?php echo $user['nama_lengkap'] ?></p>
                <p><?php echo $user['nim'] ?></p>
            </div>
            <hr class="separator">
            <a href="../logout.php" class="logout"><svg class="logout-logo" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#F0080C">
                    <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z" />
                </svg> Logout</a>
        </aside>
        <main class="main-content">
            <header>
                <div class="notification-wrapper">
                    <div class="notification" onclick="toggleNotificationList()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 512 512">
                            <path fill="#000" d="M440.08 341.31c-1.66-2-3.29-4-4.89-5.93c-22-26.61-35.31-42.67-35.31-118c0-39-9.33-71-27.72-95c-13.56-17.73-31.89-31.18-56.05-41.12a3 3 0 0 1-.82-.67C306.6 51.49 282.82 32 256 32s-50.59 19.49-59.28 48.56a3.1 3.1 0 0 1-.81.65c-56.38 23.21-83.78 67.74-83.78 136.14c0 75.36-13.29 91.42-35.31 118c-1.6 1.93-3.23 3.89-4.89 5.93a35.16 35.16 0 0 0-4.65 37.62c6.17 13 19.32 21.07 34.33 21.07H410.5c14.94 0 28-8.06 34.19-21a35.17 35.17 0 0 0-4.61-37.66M256 480a80.06 80.06 0 0 0 70.44-42.13a4 4 0 0 0-3.54-5.87H189.12a4 4 0 0 0-3.55 5.87A80.06 80.06 0 0 0 256 480" />
                        </svg>
                    </div>

                    <div class="notification-list" id="notificationList">
                        <div class="notification-header">Notifikasi</div>
                        <div class="notification-item">
                            <p class="notif-title">Notifikasi baru</p>
                            <p class="notif-message"><i>“Segera upload kegiatan mingguan Anda”</i></p>
                            <p class="notif-time">20 Juni 2025, 15:34</p>
                        </div>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn"><?php echo $user['nama_lengkap'] ?> ▼</button>
                    <div class="dropdown-content">
                        <a href="lihatprofile.php">Lihat Profile</a>
                    </div>
                </div>
            </header>
            <section class="dashboard">
                <hr class="separator">
                <div class="section-upload">

                    <h3>Upload Laporan Kegiatan PKL</h3>
                    <button class="tambah-kegiatan"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="#fff" d="M19 12.998h-6v6h-2v-6H5v-2h6v-6h2v6h6z" />
                        </svg> Tambah Laporan</button>

                    <table border="1" cellpadding="10" cellspacing="0" style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kegiatan</th>
                                <th>Minggu</th>
                                <th>File</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($kegiatan_data as $kegiatan) :
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($kegiatan['nama_kegiatan']) ?></td>
                                    <td>Minggu ke-<?= $kegiatan['minggu'] ?></td>
                                    <td>
                                        <?php if (!empty($kegiatan['file'])): ?>
                                            <a href="uploads/<?= $kegiatan['file'] ?>" target="_blank">Lihat File</a>
                                        <?php else: ?>
                                            Tidak ada file
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d M Y', strtotime($kegiatan['tanggal'])) ?></td>
                                    <td>
                                        <a class="aksi-edit" href="edit_kegiatan.php?id=<?= $kegiatan['id_kegiatan'] ?>">✏️</a>
                                        <a class="aksi-hapus" href="javascript:void(0);" onclick="konfirmasiHapus(<?= $kegiatan['id_kegiatan'] ?>)">🗑️</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div id="popupHapus" class="popup-hapus">
                        <div class="popup-card">
                            <h3>Konfirmasi Hapus</h3>
                            <p>Apakah kamu yakin ingin menghapus data ini?</p>
                            <form method="POST" id="formHapus">
                                <input type="hidden" name="id_kegiatan" id="hapusId">
                                <button type="submit" name="hapus" class="btn-hapus">Hapus</button>
                                <button type="button" class="btn-batal" onclick="tutupPopup()">Batal</button>
                            </form>
                        </div>
                    </div>

                    <!-- Popup Form -->
                    <div class="popup-tambah-kegiatan" id="popupTambahKegiatan">
                        <div class="form-content">
                            <h3>Tambah Laporan Kegiatan</h3>
                            <form action="kegiatan.php" method="POST" enctype="multipart/form-data">
                                <label for="nama_kegiatan">Nama Kegiatan:</label>
                                <input type="text" name="nama_kegiatan" required>

                                <label for="minggu">Minggu ke-:</label>
                                <select name="minggu" required>
                                    <?php
                                    $start = new DateTime($pkl_data['tanggal_mulai']);
                                    $end = new DateTime($pkl_data['tanggal_selesai']);
                                    $interval = $start->diff($end);
                                    $weeks = ceil($interval->days / 7);
                                    for ($i = 1; $i <= $weeks; $i++) {
                                        echo "<option value='$i'>Minggu ke-$i</option>";
                                    }
                                    ?>
                                </select>

                                <label for="file">Upload File:</label>
                                <input type="file" name="file" required>

                                <div class="form-buttons">
                                    <button type="button" class="gagal-tambah-kegiatan" onclick="closeForm()">Batal</button>
                                    <button type="submit" class="kegiatan-tambah">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>


                </div>
            </section>
        </main>
    </div>
</body>

</html>

<script>
    function toggleNotificationList() {
        const list = document.getElementById("notificationList");
        list.style.display = list.style.display === "block" ? "none" : "block";
    }
    document.addEventListener('click', function(event) {
        const notif = document.querySelector('.notification-wrapper');
        const notifList = document.getElementById('notificationList');
        if (!notif.contains(event.target)) {
            notifList.style.display = 'none';
        }
    });
    document.querySelector('.tambah-kegiatan').addEventListener('click', function() {
        document.getElementById('popupTambahKegiatan').style.display = 'flex';
    });

    function closeForm() {
        document.getElementById('popupTambahKegiatan').style.display = 'none';
    }

    function konfirmasiHapus(id) {
        document.getElementById('hapusId').value = id;
        document.getElementById('popupHapus').classList.add('active');
    }

    function tutupPopup() {
        document.getElementById('popupHapus').classList.remove('active');
    }
</script>