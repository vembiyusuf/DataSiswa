<?php
session_start();

// Mengecek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
  header("Location: login.php");
  exit;
}

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_vembi";
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// Menyimpan pesan untuk alert
$message = "";
if (isset($_SESSION['message'])) {
  $message = $_SESSION['message'];
  unset($_SESSION['message']); // Menghapus pesan setelah ditampilkan
}

// Menghapus akun pengguna
if (isset($_POST['delete_user'])) {
  $userId = $_POST['user_id'];
  $sql = "DELETE FROM users WHERE id='$userId'";
  if ($conn->query($sql) === TRUE) {
    $_SESSION['message'] = 'Akun berhasil dihapus!';
    header("Location: admin.php");
    exit;
  } else {
    $_SESSION['message'] = 'Error menghapus akun: ' . $conn->error;
    header("Location: admin.php");
    exit;
  }
}

// Menambahkan akun baru
if (isset($_POST['add_user'])) {
  $username = $_POST['new_username'];
  $password = md5($_POST['new_password']);
  $role = 'user'; // Set role user secara default

  $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
  if ($conn->query($sql) === TRUE) {
    $_SESSION['message'] = 'Akun baru berhasil ditambahkan!';
    header("Location: admin.php");
    exit;
  } else {
    $_SESSION['message'] = 'Error menambahkan akun: ' . $conn->error;
    header("Location: admin.php");
    exit;
  }
}

// Mengambil daftar pengguna dengan role 'user' dari database
$sql = "SELECT * FROM users WHERE role='user'";
$result = $conn->query($sql);

// Inisialisasi variabel untuk form edit pengguna
$editUserId = null;
$editUsername = '';
$editRole = '';

// Mengambil data pengguna yang ingin diedit
if (isset($_GET['edit_user'])) {
  $editUserId = $_GET['edit_user'];
  $sql = "SELECT * FROM users WHERE id='$editUserId'";
  $userResult = $conn->query($sql);
  if ($userResult->num_rows > 0) {
    $user = $userResult->fetch_assoc();
    $editUsername = $user['username'];
    $editRole = $user['role'];
  }
}

// Mengupdate data pengguna dan password
if (isset($_POST['update_user'])) {
  $updateUserId = $_POST['user_id'];
  $updateUsername = $_POST['username'];
  $updateRole = $_POST['role'];
  $newPassword = !empty($_POST['new_password']) ? md5($_POST['new_password']) : null;

  $sql = "UPDATE users SET username='$updateUsername', role='$updateRole'" . ($newPassword ? ", password='$newPassword'" : "") . " WHERE id='$updateUserId'";
  if ($conn->query($sql) === TRUE) {
    $_SESSION['message'] = 'Akun berhasil diperbarui!';
    header("Location: admin.php");
    exit;
  } else {
    $_SESSION['message'] = 'Error memperbarui akun: ' . $conn->error;
    header("Location: admin.php");
    exit;
  }
}

// Menghapus akun siswa berdasarkan NISN
if (isset($_POST['delete_siswa'])) {
  $nisn = $_POST['nisn'];
  $sql = "DELETE FROM tb_siswa WHERE nisn='$nisn'";
  if ($conn->query($sql) === TRUE) {
    $_SESSION['message'] = 'Data siswa berhasil dihapus!';
    header("Location: admin.php");
    exit;
  } else {
    $_SESSION['message'] = 'Error menghapus data siswa: ' . $conn->error;
    header("Location: admin.php");
    exit;
  }
}

// Menangani penambahan data siswa
if (isset($_POST['add_siswa'])) {
  $nisn = $_POST['nisn'];
  $nama = $_POST['nama'];
  $alamat = $_POST['alamat'];
  $jurusan = $_POST['jurusan'];

  $sql = "INSERT INTO tb_siswa (nisn, nama, alamat, jurusan) VALUES ('$nisn', '$nama', '$alamat', '$jurusan')";

  if ($conn->query($sql) === FALSE) {
    $_SESSION['message'] = 'Error menambahkan siswa: ' . $conn->error;
  } else {
    $_SESSION['message'] = 'Siswa berhasil ditambahkan!';
  }
}

// Mengambil data siswa
$sql = "SELECT * FROM tb_siswa";
$siswaResult = $conn->query($sql);

// Inisialisasi variabel untuk form edit siswa
$editSiswaNISN = '';
$editSiswaNama = '';
$editSiswaAlamat = '';
$editSiswaJurusan = '';

// Mengambil data siswa yang ingin diedit
if (isset($_GET['edit_siswa'])) {
  $editSiswaNISN = $_GET['edit_siswa'];
  $sql = "SELECT * FROM tb_siswa WHERE nisn='$editSiswaNISN'";
  $siswaResultEdit = $conn->query($sql);
  if ($siswaResultEdit->num_rows > 0) {
    $siswa = $siswaResultEdit->fetch_assoc();
    $editSiswaNama = $siswa['nama'];
    $editSiswaAlamat = $siswa['alamat'];
    $editSiswaJurusan = $siswa['jurusan'];
  }
}

// Mengupdate data siswa
if (isset($_POST['update_siswa'])) {
  $updateSiswaNISN = $_POST['nisn'];
  $updateSiswaNama = $_POST['nama'];
  $updateSiswaAlamat = $_POST['alamat'];
  $updateSiswaJurusan = $_POST['jurusan'];

  $sql = "UPDATE tb_siswa SET nama='$updateSiswaNama', alamat='$updateSiswaAlamat', jurusan='$updateSiswaJurusan' WHERE nisn='$updateSiswaNISN'";
  if ($conn->query($sql) === TRUE) {
    $_SESSION['message'] = 'Data siswa berhasil diperbarui!';
    header("Location: admin.php");
    exit;
  } else {
    $_SESSION['message'] = 'Error memperbarui data siswa: ' . $conn->error;
    header("Location: admin.php");
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="admin.css">
</head>

<body>
  <div class="container">
    <h1>Selamat Datang, Admin <?php echo htmlspecialchars($_SESSION['username']); ?></h1>

    <!-- Menampilkan Alert -->
    <?php if (!empty($message)): ?>
      <div id="alert" class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- Daftar Pengguna Terdaftar -->
    <h2>Daftar Pengguna Terdaftar</h2>
    <table>
      <tr>
        <th>Username</th>
        <th>Role</th>
        <th>Aksi</th>
      </tr>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['role']); ?></td>
            <td>
              <form action="admin.php" method="post" style="display: inline-block; margin: 0;">
                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                <button type="submit" name="delete_user" class="delete-btn">Hapus</button>
              </form>
              <a href="?edit_user=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="3">Tidak ada pengguna terdaftar.</td>
        </tr>
      <?php endif; ?>
    </table>

    <!-- Form Edit Pengguna -->
    <?php if ($editUserId !== null): ?>
      <h2>Edit Pengguna</h2>
      <form action="admin.php" method="post">
        <input type="hidden" name="user_id" value="<?php echo $editUserId; ?>">
        <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($editUsername); ?>" required>
        <input type="password" name="new_password" placeholder="Password (Kosongkan jika tidak ingin mengganti)">
        <select name="role">
          <option value="user" <?php echo $editRole == 'user' ? 'selected' : ''; ?>>User</option>
          <option value="admin" <?php echo $editRole == 'admin' ? 'selected' : ''; ?>>Admin</option>
        </select>
        <button type="submit" name="update_user">Update</button>
      </form>
    <?php endif; ?>

    <!-- Form Tambah Pengguna -->
    <h2>Tambah Pengguna Baru</h2>
    <form action="admin.php" method="post">
      <input type="text" name="new_username" placeholder="Username" required>
      <input type="password" name="new_password" placeholder="Password" required>
      <button type="submit" name="add_user">Tambah</button>
    </form>

    <!-- Daftar Siswa -->
    <h2>Daftar Siswa</h2>
    <table>
      <tr>
        <th>NISN</th>
        <th>Nama</th>
        <th>Alamat</th>
        <th>Jurusan</th>
        <th>Aksi</th>
      </tr>
      <?php if ($siswaResult->num_rows > 0): ?>
        <?php while ($siswaRow = $siswaResult->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($siswaRow['nisn']); ?></td>
            <td><?php echo htmlspecialchars($siswaRow['nama']); ?></td>
            <td><?php echo htmlspecialchars($siswaRow['alamat']); ?></td>
            <td><?php echo htmlspecialchars($siswaRow['jurusan']); ?></td>
            <td>
              <form action="admin.php" method="post" style="display: inline-block; margin: 0;">
                <input type="hidden" name="nisn" value="<?php echo $siswaRow['nisn']; ?>">
                <button type="submit" name="delete_siswa" class="delete-btn">Hapus</button>
              </form>
              <a href="?edit_siswa=<?php echo $siswaRow['nisn']; ?>" class="edit-btn">Edit</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="5">Tidak ada siswa terdaftar.</td>
        </tr>
      <?php endif; ?>
    </table>

    <!-- Form Edit Siswa -->
    <?php if (!empty($editSiswaNISN)): ?>
      <h2>Edit Siswa</h2>
      <form action="admin.php" method="post">
        <input type="hidden" name="nisn" value="<?php echo $editSiswaNISN; ?>">
        <input type="text" name="nama" placeholder="Nama" value="<?php echo htmlspecialchars($editSiswaNama); ?>" required>
        <input type="text" name="alamat" placeholder="Alamat" value="<?php echo htmlspecialchars($editSiswaAlamat); ?>" required>
        <input type="text" name="jurusan" placeholder="Jurusan" value="<?php echo htmlspecialchars($editSiswaJurusan); ?>" required>
        <button type="submit" name="update_siswa">Update</button>
      </form>
    <?php endif; ?>

    <!-- Form Tambah Siswa -->
    <h2>Tambah Siswa Baru</h2>
    <form action="admin.php" method="post">
      <input type="text" name="nisn" placeholder="NISN" required>
      <input type="text" name="nama" placeholder="Nama" required>
      <input type="text" name="alamat" placeholder="Alamat" required>
      <input type="text" name="jurusan" placeholder="Jurusan" required>
      <button type="submit" name="add_siswa">Tambah</button>
    </form>

    <a href="logout.php">Logout</a>
  </div>
</body>
<!-- Menambahkan script untuk alert otomatis tertutup -->
<script>
  window.onload = function() {
    const alert = document.getElementById('alert');
    if (alert) {
      setTimeout(() => {
        alert.style.display = 'none'; // Menyembunyikan alert setelah 2 detik
      }, 2000);
    }
  };
</script>


</html>