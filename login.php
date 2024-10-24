<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_vembi";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

if (isset($_POST['login'])) {
  $username = $_POST['username'];
  $password = md5($_POST['password']);
  $role = $_POST['role'];  // Menangkap role yang dipilih

  // Menambahkan pengecekan role dalam query SQL
  $sql = "SELECT * FROM users WHERE username='$username' AND password='$password' AND role='$role'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Set session berdasarkan username dan role
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $row['role'];

    // Cek role user yang dipilih
    if ($role == 'admin') {
      // Jika admin, redirect ke halaman admin.php
      echo "<script>
              document.addEventListener('DOMContentLoaded', function() {
                  document.querySelector('.container').style.display = 'none';
                  document.getElementById('loading').style.display = 'flex';

                  setTimeout(function() {
                      window.location.href = 'admin.php';
                  }, 2000); 
              });
          </script>";
    } else {
      // Jika user, redirect ke halaman vembi.php
      echo "<script>
              document.addEventListener('DOMContentLoaded', function() {
                  document.querySelector('.container').style.display = 'none';
                  document.getElementById('loading').style.display = 'flex';

                  setTimeout(function() {
                      window.location.href = 'vembi.php';
                  }, 2000); 
              });
          </script>";
    }
  } else {
    // Jika username, password, atau role salah
    $error = "Username, password, atau role salah!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="login.css">
</head>

<body>
  <div class="container">
    <h1>Login</h1>
    <?php if (isset($error)): ?>
      <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="login.php" method="post" class="auth-form">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>

      <!-- Dropdown untuk memilih role -->
      <select name="role" required>
        <option value="">Pilih Role</option>
        <option value="admin">Admin</option>
        <option value="user">User</option>
      </select>

      <button type="submit" name="login">Login</button>
    </form>
    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
  </div>

  <!-- Loading Animation -->
  <div id="loading" class="loading" style="display: none;">
    <div class="spinner"></div>
    <p>Loading, please wait...</p>
  </div>
</body>

</html>