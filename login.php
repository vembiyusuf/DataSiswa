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
  $password = md5($_POST['password']);  // Pastikan password di-hash sama dengan yang ada di database

  $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // Login berhasil, set session
    $_SESSION['username'] = $username;

    // Redirect dengan animasi loading
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelector('.container').style.display = 'none';
                document.getElementById('loading').style.display = 'flex';

                setTimeout(function() {
                    window.location.href = 'vembi.php';
                }, 3000);  // 3 detik
            });
        </script>";
  } else {
    // Jika username atau password salah
    $error = "Username atau password salah!";
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