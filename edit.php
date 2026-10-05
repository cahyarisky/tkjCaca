<?php
include 'koneksi.php';

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM siswa WHERE id='$id'"));

if (isset($_POST['update'])) {
    $nama = $_POST['nama_pendaftar'];
    $nisn = $_POST['nisn'];
    $jurusan = $_POST['jurusan'];

    // Cek jika ada berkas baru yang diunggah
    if ($_FILES['berkas']['name'] != "") {
        $filename = $_FILES['berkas']['name'];
        $tmp_name = $_FILES['berkas']['tmp_name'];
        $new_filename = time() . '_' . $filename;
        
        // Hapus berkas lama
        if (!empty($data['berkas'])) {
            unlink("uploads/" . $data['berkas']);
        }
        
        // Upload berkas baru
        move_uploaded_file($tmp_name, "uploads/" . $new_filename);
        $berkas_query = ", berkas='$new_filename'";
    } else {
        $berkas_query = "";
    }

    $query = "UPDATE siswa SET nama_pendaftar='$nama', nisn='$nisn', jurusan='$jurusan' $berkas_query WHERE id='$id'";
    mysqli_query($koneksi, $query);
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Pendaftar</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f4f4f9; }
        .card { background: white; padding: 20px; border-radius: 8px; max-width: 500px; margin: auto; }
        input[type=text], select, input[type=file] { width: 100%; padding: 8px; margin: 5px 0 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>

<div class="card">
    <h3>Edit Data Pendaftar</h3>
    <form action="" method="POST" enctype="multipart/form-data">
        <label>Nama Pendaftar:</label>
        <input type="text" name="nama_pendaftar" value="<?= $data['nama_pendaftar']; ?>" required>

        <label>NISN:</label>
        <input type="text" name="nisn" value="<?= $data['nisn']; ?>" required>

        <label>Jurusan:</label>
        <select name="jurusan" required>
            <option value="TKJ" <?= $data['jurusan'] == 'TKJ' ? 'selected' : ''; ?>>Teknik Komputer & Jaringan (TKJ)</option>
            <option value="RPL" <?= $data['jurusan'] == 'RPL' ? 'selected' : ''; ?>>Rekayasa Perangkat Lunak (RPL)</option>
            <option value="MM" <?= $data['jurusan'] == 'MM' ? 'selected' : ''; ?>>Multimedia</option>
        </select>

        <label>Ganti Berkas (Kosongkan jika tidak diganti):</label>
        <input type="file" name="berkas">

        <button type="submit" name="update">Simpan Perubahan</button>
        <a href="index.php">Batal</a>
    </form>
</div>

</body>
</html>
