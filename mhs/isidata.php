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
$query = mysqli_query($conn, "SELECT * FROM pkl_data WHERE user_id = '$user_id'");
$data_pkl = mysqli_fetch_assoc($query);

// Ambil data user
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$user_id'");
$user = mysqli_fetch_assoc($query_user);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama_lengkap = $_POST['nama_lengkap'];
    $nim = $_POST['nim'];
    $prodi = $_POST['prodi'];
    $semester = $_POST['semester'];
    $no_telp = $_POST['no_telp'];
    $email = $_POST['email'];
    $nama_perusahaan = $_POST['nama_perusahaan'];
    $alamat_lengkap = $_POST['alamat_lengkap'];
    $no_telp_perusahaan = $_POST['no_telp_perusahaan'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];

    // Cek apakah data sudah ada (update atau insert)
    $cek = $conn->query("SELECT * FROM pkl_data WHERE user_id = $user_id");

    if ($cek->num_rows > 0) {
        // UPDATE
        $conn->query("UPDATE pkl_data SET 
        nama_lengkap='$nama_lengkap', nim='$nim', prodi='$prodi', semester=$semester,
        no_telp='$no_telp', email='$email', nama_perusahaan='$nama_perusahaan',
        alamat_lengkap='$alamat_lengkap', no_telp_perusahaan='$no_telp_perusahaan',
        tanggal_mulai='$tanggal_mulai', tanggal_selesai='$tanggal_selesai',
        updated_at=NOW()
        WHERE user_id=$user_id
    ");
    } else {
        // INSERT
        $conn->query("INSERT INTO pkl_data (
        user_id, nama_lengkap, nim, prodi, semester, no_telp, email,
        nama_perusahaan, alamat_lengkap, no_telp_perusahaan, tanggal_mulai, tanggal_selesai, created_at, updated_at
    ) VALUES (
        $user_id, '$nama_lengkap', '$nim', '$prodi', $semester, '$no_telp', '$email',
        '$nama_perusahaan', '$alamat_lengkap', '$no_telp_perusahaan', '$tanggal_mulai', '$tanggal_selesai', NOW(), NOW()
    )");
    }


    // Redirect atau tampilkan pesan
    header("Location: isidata.php?success=1");
    exit();
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
    <link rel="stylesheet" href="../css/mhs/isidata.css">
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
                <a href="isidata.php" class="active"><svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#000000" alttext="Logo Isi Data">
                        <path d="M200-440h240v-160H200v160Zm0-240h560v-80H200v80Zm0 560q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v252q-19-8-39.5-10.5t-40.5.5q-21 4-40.5 13.5T684-479l-39 39-205 204v116H200Zm0-80h240v-160H200v160Zm320-240h125l39-39q16-16 35.5-25.5T760-518v-82H520v160Zm0 360v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T863-300L643-80H520Zm300-263-37-37 37 37ZM580-140h38l121-122-37-37-122 121v38Zm141-141-19-18 37 37-18-19Z" />
                    </svg> Isi Data PKL</a>
                <a href="kegiatan.php"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000" alttext="Logo Kegiatan PKL">
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
                <?php if ($data_pkl): ?>
                    <!-- Jika data PKL sudah diisi -->
                    <div style="background:#fff; padding:20px; border-radius:8px;">
                        <h3 style="color:green;">Data PKL Sudah Terisi</h3>
                        <div onclick="showPopup()" style="cursor:pointer; padding:20px; border:1px solid #ccc; border-radius:10px; margin-top:10px;">
                            <strong>⚠️ Tinjau kembali data PKL agar lebih detail</strong>
                        </div>

                        <!-- Form muncul saat diklik -->
                        <div id="popupOverlay" class="popup-overlay">
                            <div class="popup-content">
                                <h1 style="text-align:center;">Data PKL</h1>
                                <span class="close-popup" onclick="closePopup()">&times;</span>

                                <h4>Data Diri</h4>
                                <table class="data-table">
                                    <tr>
                                        <td>Nama Lengkap</td>
                                        <td>:</td>
                                        <td><?= $pkl_data['nama_lengkap'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>NIM</td>
                                        <td>:</td>
                                        <td><?= $pkl_data['nim'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Program Studi</td>
                                        <td>:</td>
                                        <td><?= $pkl_data['prodi'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Semester</td>
                                        <td>:</td>
                                        <td><?= $pkl_data['semester'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>No. Telp</td>
                                        <td>:</td>
                                        <td><?= $pkl_data['no_telp'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>E-Mail</td>
                                        <td>:</td>
                                        <td><?= $pkl_data['email'] ?></td>
                                    </tr>
                                </table>

                                <h4>Data Perusahaan</h4>
                                <table class="data-table">
                                    <tr>
                                        <td>Nama Perusahaan</td>
                                        <td>:</td>
                                        <td><?= $pkl_data['nama_perusahaan'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Alamat Perusahaan</td>
                                        <td>:</td>
                                        <td><?= $pkl_data['alamat_lengkap'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>No. Telp Perusahaan</td>
                                        <td>:</td>
                                        <td><?= $pkl_data['no_telp_perusahaan'] ?></td>
                                    </tr>
                                </table>

                                <h4>Periode</h4>
                                <table class="data-table">
                                    <tr>
                                        <td>Tanggal Mulai</td>
                                        <td>:</td>
                                        <td><?= date('d F Y', strtotime($pkl_data['tanggal_mulai'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Selesai</td>
                                        <td>:</td>
                                        <td><?= date('d F Y', strtotime($pkl_data['tanggal_selesai'])) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <h2>Isi Data PKL</h2>
                    <form action="isidata.php" method="POST" class="profile-form">
                        <h1>Form Isi Data PKL</h1>
                        <!-- DATA DIRI -->
                        <h3>1. Data Diri</h3>
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" value="<?= $user['nama_lengkap'] ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="nim">NIM</label>
                            <input type="text" name="nim" id="nim" value="<?= $user['nim'] ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="prodi">Program Studi</label>
                            <select name="prodi" id="prodi" class="drpdown-prodi" required>
                                <option value="">-- Pilih Program Studi --</option>
                                <option value="Informatika">Informatika</option>
                                <option value="Sistem Informasi">Sistem Informasi</option>
                                <option value="Teknik Elektro">Teknik Elektro</option>
                                <option value="Teknik Industri">Teknik Industri</option>
                                <option value="Manajemen">Manajemen</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="semester">Semester</label>
                            <input type="number" name="semester" id="semester" min="1" max="14" value="<?= $user['semester'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="no_telp">No. Telepon</label>
                            <input type="text" name="no_telp" id="no_telp" pattern="[0-9]+" value="<?= $user['no_telp'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" name="email" id="email" value="<?= $user['email'] ?>" required>
                        </div>

                        <!-- DATA PERUSAHAAN -->
                        <h3>2. Data Perusahaan</h3>
                        <div class="form-group">
                            <label for="nama_perusahaan">Nama Perusahaan</label>
                            <input type="text" name="nama_perusahaan" id="nama_perusahaan" required>
                        </div>

                        <div class="form-group">
                            <label for="alamat_lengkap">Alamat Lengkap</label>
                            <textarea name="alamat_lengkap" id="alamat_lengkap" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="no_telp_perusahaan">No. Telp Perusahaan</label>
                            <input type="text" name="no_telp_perusahaan" pattern="[0-9]+" id="no_telp_perusahaan" required>
                        </div>

                        <!-- PERIODE -->
                        <h3>3. Periode PKL</h3>
                        <div class="form-group">
                            <label for="tanggal_mulai">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" required>
                        </div>

                        <div class="form-group">
                            <label for="tanggal_selesai">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="save-btn">Simpan</button>
                        </div>
                    </form>
                <?php endif; ?>
            </section>
        </main>
    </div>
    <script>
        function ClosePopup() {
            document.getElementById("overlay").style.display = "none";
        }

        function closePopup() {
            document.getElementById('popupOverlay').style.display = 'none';
        }

        function showPopup() {
            document.getElementById('popupOverlay').style.display = 'flex';
        }

        
    </script>
</body>

<?php if (isset($_GET['success'])): ?>
    <div id="overlay" class="overlay">
        <div class="popup">
            <div class="popup-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="75" height="75" viewBox="0 0 24 24">
                    <path fill="#0DAE17" d="m10.6 16.6l7.05-7.05l-1.4-1.4l-5.65 5.65l-2.85-2.85l-1.4 1.4zM12 22q-2.075 0-3.9-.788t-3.175-2.137T2.788 15.9T2 12t.788-3.9t2.137-3.175T8.1 2.788T12 2t3.9.788t3.175 2.137T21.213 8.1T22 12t-.788 3.9t-2.137 3.175t-3.175 2.138T12 22m0-2q3.35 0 5.675-2.325T20 12t-2.325-5.675T12 4T6.325 6.325T4 12t2.325 5.675T12 20m0-8" />
                </svg>
            </div>
            <div class="popup-title">Berhasil Tersimpan</div>
            <div class="popup-subtitle">Data Praktek Kerja Lapangan</div>
            <button onclick="ClosePopup()">OK</button>
        </div>
    </div>
<?php endif; ?>


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
</script>