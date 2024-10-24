<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_vembi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// Memastikan user sudah login
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

// Menangani penambahan data
if (isset($_POST['add'])) {
  $nisn = $_POST['nisn'];
  $nama = $_POST['nama'];
  $alamat = $_POST['alamat'];
  $jurusan = $_POST['jurusan'];

  $sql = "INSERT INTO tb_siswa (nisn, nama, alamat, jurusan) VALUES ('$nisn', '$nama', '$alamat', '$jurusan')";

  if ($conn->query($sql) === FALSE) {
    echo "Error: " . $conn->error;
  }
}

// Menangani pembaruan data
if (isset($_POST['update'])) {
  $nisn = $_POST['nisn'];
  $nama = $_POST['nama'];
  $alamat = $_POST['alamat'];
  $jurusan = $_POST['jurusan'];

  $sql = "UPDATE tb_siswa SET nama='$nama', alamat='$alamat', jurusan='$jurusan' WHERE nisn='$nisn'";

  if ($conn->query($sql) === FALSE) {
    echo "Error: " . $conn->error;
  }
}

// Menghapus data
if (isset($_GET['delete'])) {
  $nisn = $_GET['delete'];
  $sql = "DELETE FROM tb_siswa WHERE nisn='$nisn'";

  if ($conn->query($sql) === FALSE) {
    echo "Error: " . $conn->error;
  }
}

// Mengambil data
$sql = "SELECT * FROM tb_siswa";
$result = $conn->query($sql);

// Variabel untuk edit
$nisn_edit = '';
$nama_edit = '';
$alamat_edit = '';
$jurusan_edit = '';

// Mengambil data untuk edit
if (isset($_GET['edit'])) {
  $nisn = $_GET['edit'];
  $sql = "SELECT * FROM tb_siswa WHERE nisn='$nisn'";
  $edit_result = $conn->query($sql);

  if ($edit_result->num_rows > 0) {
    $row = $edit_result->fetch_assoc();
    $nisn_edit = $row['nisn'];
    $nama_edit = $row['nama'];
    $alamat_edit = $row['alamat'];
    $jurusan_edit = $row['jurusan'];
  }
}

// Logout functionality
if (isset($_POST['logout'])) {
  session_destroy();
  header("Location: login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Table Management</title>
  <link rel="stylesheet" href="Vembi.css">
</head>

<body>
  <div class="container">
    <h1>Data Table</h1>
    <h2>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

    <form action="vembi.php" method="post" class="add-form">
      <input type="text" name="nisn" placeholder="NISN" value="<?php echo htmlspecialchars($nisn_edit); ?>" required>
      <input type="text" name="nama" placeholder="NAMA" value="<?php echo htmlspecialchars($nama_edit); ?>" required>
      <input type="text" name="alamat" placeholder="ALAMAT" value="<?php echo htmlspecialchars($alamat_edit); ?>" required>
      <input type="text" name="jurusan" placeholder="JURUSAN" value="<?php echo htmlspecialchars($jurusan_edit); ?>" required>
      <?php if ($nisn_edit): ?>
        <button type="submit" name="update">Update</button>
      <?php else: ?>
        <button type="submit" name="add">Tambah</button>
      <?php endif; ?>
    </form>

    <!-- Tabel Data -->
    <table>
      <thead>
        <tr>
          <th>NISN</th>
          <th>NAMA</th>
          <th>ALAMAT</th>
          <th>JURUSAN</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($row["nisn"]); ?></td>
              <td><?php echo htmlspecialchars($row["nama"]); ?></td>
              <td><?php echo htmlspecialchars($row["alamat"]); ?></td>
              <td><?php echo htmlspecialchars($row["jurusan"]); ?></td>
              <td>
                <a class="edit-button" href="vembi.php?edit=<?php echo urlencode($row["nisn"]); ?>">Edit</a>
                <a class="delete-button" href="vembi.php?delete=<?php echo urlencode($row["nisn"]); ?>">Hapus</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="5">Tidak ada data yang tersedia</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <form action="vembi.php" method="post" class="logout-form">
      <button type="submit" name="logout" class="logout-button">Logout</button>
    </form>
  </div>
</body>

</html>

<?php $conn->close(); ?>