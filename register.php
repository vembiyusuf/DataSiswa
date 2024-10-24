<?php
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

if (isset($_POST['register'])) {
  $username = $_POST['username'];
  $password = md5($_POST['password']);  // Menggunakan MD5 untuk hash password
  $role = $_POST['role'];  // Menangkap role yang dipilih

  // Cek apakah username sudah ada
  $checkUser = $conn->query("SELECT * FROM users WHERE username='$username'");
  if ($checkUser->num_rows > 0) {
    $error = "Username sudah terdaftar!";
  } else {
    // Menambahkan role ke dalam query SQL
    $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
    if ($conn->query($sql) === TRUE) {
      header("Location: login.php");
      exit;
    } else {
      $error = "Pendaftaran gagal: " . $conn->error;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="login.css">
</head>

<body>
  <div class="container">
    <h1>Register</h1>
    <?php if (isset($error)): ?>
      <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="register.php" method="post" class="auth-form">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>

      <!-- Dropdown untuk memilih role saat register -->
      <select name="role" required>
        <option value="">Pilih Role</option>
        <option value="admin">Admin</option>
        <option value="user">User</option>
      </select>

      <button type="submit" name="register">Register</button>
    </form>
    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
  </div>
</body>

</html>