<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_vembi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// Insert Data
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

// Edit Data
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

// Delete Data
if (isset($_GET['delete'])) {
  $nisn = $_GET['delete'];
  $sql = "DELETE FROM tb_siswa WHERE nisn='$nisn'";

  if ($conn->query($sql) === FALSE) {
    echo "Error: " . $conn->error;
  }
}

// Get Selected Data for Edit
$selected_nisn = '';
$selected_nama = '';
$selected_alamat = '';
$selected_jurusan = '';

if (isset($_GET['edit'])) {
  $nisn = $_GET['edit'];
  $sql = "SELECT * FROM tb_siswa WHERE nisn='$nisn'";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $selected_nisn = $row['nisn'];
    $selected_nama = $row['nama'];
    $selected_alamat = $row['alamat'];
    $selected_jurusan = $row['jurusan'];
  }
}

$sql = "SELECT * FROM tb_siswa";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Table Management</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="container">
    <h1>Data Table</h1>
    <form action="vembi.php" method="post" class="add-form">
      <input type="text" name="nisn" placeholder="NISN" value="<?php echo htmlspecialchars($selected_nisn); ?>">
      <input type="text" name="nama" placeholder="Nama" value="<?php echo htmlspecialchars($selected_nama); ?>" required>
      <input type="text" name="alamat" placeholder="Alamat" value="<?php echo htmlspecialchars($selected_alamat); ?>" required>
      <input type="text" name="jurusan" placeholder="Jurusan" value="<?php echo htmlspecialchars($selected_jurusan); ?>" required>
      <?php if ($selected_nisn): ?>
        <button type="submit" name="update">Update</button>
      <?php else: ?>
        <button type="submit" name="add">Tambah</button>
      <?php endif; ?>
    </form>

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
  </div>
</body>

</html>

<?php $conn->close(); ?>