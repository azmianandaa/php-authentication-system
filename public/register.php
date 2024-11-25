<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once "../src/database/connection.php";

$username_error = "";
$email_error = "";
$password_error = "";

$username = $email = $password = $confirm_password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $username = mysqli_real_escape_string($conn, trim($_POST["username"]));
    $email = mysqli_real_escape_string($conn, trim($_POST["email"]));
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $confirm_password = mysqli_real_escape_string($conn, $_POST["c_password"]);

    // username
    if (empty($username)) {
        $username_error = "Nama Pengguna tidak boleh kosong!";
    } elseif (strlen($username) < 3) {
        $username_error = "Nama Pengguna harus terdiri minimal 3 karakter!";
    } elseif (!preg_match('/^[a-zA-Z ]+$/', $username)) {
        $username_error = "Nama Pengguna hanya boleh mengandung huruf dan spasi!";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $username_error = "Nama Pengguna sudah terdaftar!";
        }
        $stmt->close();
    }

    // email
    if (empty($email)) {
        $email_error = "E-mail tidak boleh kosong!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Format e-mail tidak valid!";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $email_error = "E-mail sudah terdaftar!";
        }
        $stmt->close();
    }

    // password
    if (empty($password)) {
        $password_error = "Kata Sandi tidak boleh kosong!";
    } elseif (strlen($password) < 8) {
        $password_error = "Kata Sandi minimal 8 karakter!";
    } elseif (!preg_match("/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,}$/", $password)) {
        $password_error = "Kata Sandi harus mengandung huruf dan angka!";
    } elseif ($password !== $confirm_password) {
        $password_error = "Kata Sandi tidak sesuai!";
    }

    if (empty($username_error) && empty($email_error) && empty($password_error)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            echo "<script>alert('Akun berhasil dibuat.'); window.location.href = 'login.php';</script>";
        }

        $stmt->close();
        $conn->close();
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Formulir Pendaftaran">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
    rel="stylesheet">
  <link rel="shortcut icon" href="../assets/images/profile.png" type="image/x-icon">
  <link rel="stylesheet" href="../assets/css/register.css">
  <title>Form Register - Azmi Ananda</title>
</head>

<body>
  <form action="" method="post" autocomplete="off">
    <div class="logo">
      <a href="https://www.google.com" target="_blank">
        <div><img src="../assets/images/search.png" alt="google" class="google">Lanjutkan dengan Google</div>
      </a>
      <a href="https://www.microsoft.com" target="_blank">
        <div><img src="../assets/images/microsoft.png" alt="microsoft" class="microsof">Lanjutkan dengan Microsoft
          Account</div>
      </a>
    </div>
    <hr style="border: none; border-bottom: 1px solid rgb(138, 138, 138); margin: 3rem 0; ">
    <div class="atau">atau</div>
    <fieldset class="username">
      <label for="username">Nama Pengguna</label>
      <input type="text" id="username" name="username" value="<?=htmlspecialchars($username)?>" autofocus>
      <i class="fa-solid fa-user"></i>
    </fieldset>
    <?php if ($username_error != "") {?>
    <div class="error"><?=$username_error?></div>
    <?php }?>
    <fieldset class="email">
      <label for="email">E-mail</label>
      <input type="text" id="email" name="email" value="<?=htmlspecialchars($email)?>">
      <i class="fa-solid fa-envelope"></i>
    </fieldset>
    <?php if ($email_error != "") {?>
    <div class="error"><?=$email_error?></div>
    <?php }?>
    <fieldset class="password">
      <label for="password">Kata Sandi</label>
      <input type="password" id="password" name="password" value="<?=htmlspecialchars($password)?>">
      <i class="fa-solid fa-lock"></i>
      <i class="fa-solid fa-eye-slash"></i>
    </fieldset>
    <?php if ($password_error != "") {?>
    <div class="error"><?=$password_error?></div>
    <?php }?>
    <fieldset class="confirm-password">
      <label for="c_password">Konfirmasi Kata Sandi</label>
      <input type="password" id="c_password" name="c_password" value="<?=htmlspecialchars($confirm_password)?>">
      <i class="fa-solid fa-unlock"></i>
      <i class="fa-solid fa-eye-slash"></i>
    </fieldset>
    <?php if ($password_error != "") {?>
    <div class="error"><?=$password_error?></div>
    <?php }?>
    <div class="submit">
      <button type="submit" name="submit">Mendaftar</button>
    </div>
    <h3>Sudah punya akun?<a href="login.php"> Masuk</a></h3>
  </form>
</body>

</html>