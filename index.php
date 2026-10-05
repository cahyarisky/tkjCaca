<?php
session_start();

// Proteksi Halaman Login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

// --- PROSES CREATE & UPLOAD BERKAS ---
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_pendaftar'];
    $nisn = $_POST['nisn'];
    $jurusan = $_POST['jurusan'];

    $filename = $_FILES['berkas']['name'];
    $tmp_name = $_FILES['berkas']['tmp_name'];
    
    $new_filename = time() . '_' . $filename;
    $target_dir = "uploads/" . $new_filename;

    if (move_uploaded_file($tmp_name, $target_dir)) {
        $query = "INSERT INTO siswa (nama_pendaftar, nisn, jurusan, berkas) VALUES ('$nama', '$nisn', '$jurusan', '$new_filename')";
        mysqli_query($koneksi, $query);
        header("Location: index.php");
    } else {
        echo "<script>alert('Gagal mengunggah berkas! Periksa izin folder uploads.');</script>";
    }
}

// --- PROSES DELETE ---
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    $get_file = mysqli_query($koneksi, "SELECT berkas FROM siswa WHERE id='$id'");
    $data_file = mysqli_fetch_assoc($get_file);
    if (!empty($data_file['berkas'])) {
        @unlink("uploads/" . $data_file['berkas']);
    }

    mysqli_query($koneksi, "DELETE FROM siswa WHERE id='$id'");
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Siswa Baru - Cahya Risky</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f3f0f7;
            --primary-purple: #8e7cc3;
            --primary-dark: #6c5ce7;
            --light-purple: #e8e0f0;
            --card-bg: #ffffff;
            --text-main: #2d3436;
            --text-muted: #636e72;
            --accent-green: #00b894;
            --accent-red: #ff7675;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-main); padding: 30px 15px; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 950px; }

        /* Header UI */
        .header-card {
            background: linear-gradient(135deg, #a29bfe 0%, #6c5ce7 100%);
            color: white; padding: 25px 30px; border-radius: 16px;
            box-shadow: 0 10px 20px rgba(108, 92, 231, 0.2); margin-bottom: 25px;
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;
        }
        .header-card h1 { font-size: 22px; font-weight: 600; margin-bottom: 6px; }
        .profile-badge { display: inline-flex; gap: 12px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(5px); padding: 6px 14px; border-radius: 20px; font-size: 13px; }
        .btn-logout { background-color: #ff7675; color: white; text-decoration: none; padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; transition: 0.2s; }
        .btn-logout:hover { background-color: #d63031; }

        /* Card Layout */
        .card { background: var(--card-bg); border-radius: 16px; padding: 25px 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03); margin-bottom: 25px; }
        .card-title { font-size: 18px; font-weight: 600; color: var(--primary-dark); margin-bottom: 20px; }

        /* Form UI */
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 12px 16px; border: 2px solid #e1dbe8; border-radius: 10px; font-size: 14px; transition: all 0.3s ease; background-color: #faf9fc; }
        .form-control:focus { outline: none; border-color: var(--primary-dark); background-color: #fff; box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.1); }
        input[type="file"]::file-selector-button { background-color: var(--light-purple); color: var(--primary-dark); padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; margin-right: 12px; transition: 0.2s ease; }

        /* Buttons */
        .btn-submit { width: 100%; background-color: var(--primary-dark); color: white; border: none; padding: 14px; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(108, 92, 231, 0.3); }
        .btn-submit:hover { background-color: #5b4cc4; transform: translateY(-2px); }

        /* Table UI */
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
        th { padding: 12px 16px; font-size: 13px; font-weight: 600; text-transform: uppercase; color: var(--text-muted); text-align: left; }
        tbody tr { background-color: #faf9fc; transition: transform 0.2s ease; }
        tbody tr:hover { transform: scale(1.005); background-color: #f3f0f7; }
        td { padding: 14px 16px; font-size: 14px; border-top: 1px solid #efeaf4; border-bottom: 1px solid #efeaf4; vertical-align: middle; }
        td:first-child { border-left: 1px solid #efeaf4; border-top-left-radius: 10px; border-bottom-left-radius: 10px; }
        td:last-child { border-right: 1px solid #efeaf4; border-top-right-radius: 10px; border-bottom-right-radius: 10px; }

        /* Image Preview Style */
        .img-preview { width: 65px; height: 65px; object-fit: cover; border-radius: 8px; border: 2px solid #e1dbe8; cursor: pointer; transition: transform 0.2s; }
        .img-preview:hover { transform: scale(1.1); }

        /* Action Badges */
        .badge-action { display: inline-block; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; text-decoration: none; transition: 0.2s ease; }
        .badge-download { background-color: #e3f9e5; color: var(--accent-green); margin-top: 4px; }
        .badge-edit { background-color: #fff4e6; color: #d63031; }
        .badge-delete { background-color: #ffe5e5; color: var(--accent-red); }
    </style>
</head>
<body>

<div class="container">

    <!-- HEADER & LOGOUT -->
    <div class="header-card">
        <div>
            <h1>Sistem Pendaftaran Siswa Baru</h1>
            <div class="profile-badge">
                <span><strong>Nama:</strong> Cahya Risky</span>
                <span>|</span>
                <span><strong>Kelas:</strong> XI TKJ 4</span>
                <span>|</span>
                <span><strong>No. Absen:</strong> 06</span>
            </div>
        </div>
        <a href="logout.php" class="btn-logout" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Logout</a>
    </div>

    <!-- FORM INPUT -->
    <div class="card">
        <div class="card-title">Formulir Pendaftar</div>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_pendaftar" class="form-control" placeholder="Masukkan nama pendaftar" required>
            </div>

            <div class="form-group">
                <label>NISN</label>
                <input type="text" name="nisn" class="form-control" placeholder="Masukkan NISN" required>
            </div>

            <div class="form-group">
                <label>Pilihan Jurusan</label>
                <select name="jurusan" class="form-control" required>
                    <option value="TKJ">Teknik Komputer & Jaringan (TKJ)</option>
                    <option value="RPL">Rekayasa Perangkat Lunak (RPL)</option>
                    <option value="MM">Multimedia</option>
                </select>
            </div>

            <div class="form-group">
                <label>Upload Berkas Siswa (JPG / PNG / PDF)</label>
                <input type="file" name="berkas" class="form-control" required>
            </div>

            <button type="submit" name="tambah" class="btn-submit">Kirim Pendaftaran</button>
        </form>
    </div>

    <!-- TABEL DATA -->
    <div class="card">
        <div class="card-title">Data Siswa Terdaftar</div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NISN</th>
                        <th>Jurusan</th>
                        <th>Berkas (Preview)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $query = mysqli_query($koneksi, "SELECT * FROM siswa ORDER BY id DESC");
                    while ($data = mysqli_fetch_assoc($query)) {
                        $file_ext = strtolower(pathinfo($data['berkas'], PATHINFO_EXTENSION));
                        $is_image = in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><strong><?= htmlspecialchars($data['nama_pendaftar']); ?></strong></td>
                        <td><?= htmlspecialchars($data['nisn']); ?></td>
                        <td><?= htmlspecialchars($data['jurusan']); ?></td>
                        <td>
                            <?php if ($is_image): ?>
                                <a href="uploads/<?= $data['berkas']; ?>" target="_blank">
                                    <img src="uploads/<?= $data['berkas']; ?>" class="img-preview" alt="Berkas Siswa">
                                </a>
                            <?php else: ?>
                                <span style="font-size: 12px; color: #636e72;">[File PDF/Dokumen]</span>
                            <?php endif; ?>
                            <br>
                            <a href="uploads/<?= $data['berkas']; ?>" download class="badge-action badge-download">Unduh File</a>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= $data['id']; ?>" class="badge-action badge-edit">Edit</a>
                            <a href="index.php?hapus=<?= $data['id']; ?>" onclick="return confirm('Yakin ingin menghapus data ini?')" class="badge-action badge-delete">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
