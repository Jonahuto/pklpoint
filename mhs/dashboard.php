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
$query = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
$user = $query->fetch_assoc();
$query = $conn->query("SELECT * FROM pkl_data WHERE user_id = $user_id");
$pkl_data = $query->fetch_assoc();
// Ambil data dari database
$perusahaan = $pkl_data['nama_perusahaan'] ?? '';
$tanggal_mulai = $pkl_data['tanggal_mulai'] ?? '';
$tanggal_selesai = $pkl_data['tanggal_selesai'] ?? '';
$pembimbing = $pkl_data['pembimbing'] ?? '';
$pembimbing_id = $pkl_data['pembimbing'] ?? null;
if (!empty($pembimbing_id)) {
    $result_pembimbing = $conn->query("SELECT nama_lengkap FROM users WHERE user_id = $pembimbing_id AND role = 'dosen'");
    if ($result_pembimbing && $result_pembimbing->num_rows > 0) {
        $row = $result_pembimbing->fetch_assoc();
        $nama_pembimbing = $row['nama_lengkap'];
    }
}
// Cek jika kosong, tampilkan "-"
$nama_pembimbing = $nama_pembimbing ?? '-';
$perusahaan = !empty($perusahaan) ? $perusahaan : '-';
$periode = (!empty($tanggal_mulai) && !empty($tanggal_selesai)) ? date('F Y', strtotime($tanggal_mulai)) . ' - ' . date('F Y', strtotime($tanggal_selesai)) : '-';
$pembimbing = !empty($pembimbing) ? $pembimbing : '-';



$sql = "SELECT * FROM laporan_akhir WHERE id_user = $user_id ORDER BY tanggal DESC LIMIT 1";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKL Point Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="../css/mhs/dashboard.css">
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
                <a href="dashboard.php" class="active"><svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#000000" alt="Logo Home">
                        <path d="M240-200h120v-240h240v240h120v-360L480-740 240-560v360Zm-80 80v-480l320-240 320 240v480H520v-240h-80v240H160Zm320-350Z" />
                    </svg> Dashboard</a>
                <a href="isidata.php"><svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#000000" alt="Logo Isi Data">
                        <path d="M200-440h240v-160H200v160Zm0-240h560v-80H200v80Zm0 560q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v252q-19-8-39.5-10.5t-40.5.5q-21 4-40.5 13.5T684-479l-39 39-205 204v116H200Zm0-80h240v-160H200v160Zm320-240h125l39-39q16-16 35.5-25.5T760-518v-82H520v160Zm0 360v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T863-300L643-80H520Zm300-263-37-37 37 37ZM580-140h38l121-122-37-37-122 121v38Zm141-141-19-18 37 37-18-19Z" />
                    </svg> Isi Data PKL</a>
                <a href="kegiatan.php"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000" alt="Logo Kegiatan PKL">
                        <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h167q11-35 43-57.5t70-22.5q40 0 71.5 22.5T594-840h166q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560h-80v120H280v-120h-80v560Zm280-560q17 0 28.5-11.5T520-800q0-17-11.5-28.5T480-840q-17 0-28.5 11.5T440-800q0 17 11.5 28.5T480-760Z" />
                    </svg> Kegiatan PKL</a>
                <a href="laporanakhir.php"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000" alt="Logo Laporan Akhir">
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

                        <?php if ($result->num_rows > 0): ?>
                            <?php $laporan = $result->fetch_assoc(); ?>
                            <div class="notification-item">
                                <p class="notif-title">Laporan Akhir Terbaru</p>
                                <p class="notif-message">
                                    <i><?php echo htmlspecialchars($laporan['komentar']); ?></i>
                                </p>
                                <p class="notif-time"><?php echo date('d F Y, H:i', strtotime($laporan['tanggal_komentar'])); ?></p>
                            </div>
                        <?php else: ?>
                            <div class="notification-item">
                                <p class="notif-title">Belum Ada Laporan Akhir</p>
                                <p class="notif-message"><i>Segera upload laporan akhir Anda.</i></p>
                                <p class="notif-time"><?php echo date('d F Y, H:i'); ?></p>
                            </div>
                        <?php endif; ?>
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
                <h2>Selamat datang, <?php echo $user['nama_lengkap'] ?></h2>
                <div class="info-card">
                    <p>Perusahaan: <?= ($perusahaan); ?></p>
                    <p>Periode: <?= ($periode); ?></p>
                    <p>Pembimbing: <?= ($nama_pembimbing); ?></p>
                </div>
                <div class="tasks">
                    <h3>Langkah Berikutnya</h3>
                    <ul>
                        <li>
                            <div class="langkah-1"><a href="isidata.php">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                        <path fill="#000" d="m9 20.42l-6.21-6.21l2.83-2.83L9 14.77l9.88-9.89l2.83 2.83z" />
                                    </svg>
                                    Isi Data PKL</a></div>
                        </li>
                        <li>
                            <div class="langkah-2"><a href="kegiatan.php"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                        <g fill="none" stroke="#000" stroke-width="1.5">
                                            <path d="m18.18 8.04l.463-.464a1.966 1.966 0 1 1 2.781 2.78l-.463.464M18.18 8.04s.058.984.927 1.853s1.854.927 1.854.927M18.18 8.04l-4.26 4.26c-.29.288-.434.433-.558.592q-.22.282-.374.606c-.087.182-.151.375-.28.762l-.413 1.24l-.134.401m8.8-5.081l-4.26 4.26c-.29.29-.434.434-.593.558q-.282.22-.606.374c-.182.087-.375.151-.762.28l-1.24.413l-.401.134m0 0l-.401.134a.53.53 0 0 1-.67-.67l.133-.402m.938.938l-.938-.938" />
                                            <path stroke-linecap="round" d="M8 13h2.5M8 9h6.5M8 17h1.5M19.828 3.172C18.657 2 16.771 2 13 2h-2C7.229 2 5.343 2 4.172 3.172S3 6.229 3 10v4c0 3.771 0 5.657 1.172 6.828S7.229 22 11 22h2c3.771 0 5.657 0 6.828-1.172c.944-.943 1.127-2.348 1.163-4.828" />
                                        </g>
                                    </svg> Upload Kegiatan</a></div>
                        </li>
                        <li>
                            <div class="langkah-3"><a href="laporanakhir.php"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                        <path fill="#000" d="M6 20q-.825 0-1.412-.587T4 18v-2q0-.425.288-.712T5 15t.713.288T6 16v2h12v-2q0-.425.288-.712T19 15t.713.288T20 16v2q0 .825-.587 1.413T18 20zm5-12.15L9.125 9.725q-.3.3-.712.288T7.7 9.7q-.275-.3-.288-.7t.288-.7l3.6-3.6q.15-.15.325-.212T12 4.425t.375.063t.325.212l3.6 3.6q.3.3.288.7t-.288.7q-.3.3-.712.313t-.713-.288L13 7.85V15q0 .425-.288.713T12 16t-.712-.288T11 15z" />
                                    </svg> Upload Kegiatan Akhir</a></div>
                        </li>
                    </ul>
                </div>
                <!--<div class="notification">
                    <span class="icon">❗</span> Tinjau kembali data PKL untuk detail lebih lanjut
                </div> -->
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
</script>