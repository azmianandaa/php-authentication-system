<?php
session_start();
require_once "../src/database/connection.php";

if (isset($_COOKIE["login"])) {
    $cookie_token = $_COOKIE["login"];

    if (isset($_SESSION["login_token"]) && $_SESSION["login_token"] === $cookie_token) {
        header("Location: dashboard.php");
        exit();
    }
}

if (isset($_COOKIE["remember_me_user"]) && !isset($_SESSION["login"])) {
    $username_or_email = $_COOKIE["remember_me_user"];

    if (filter_var($username_or_email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
    } else {
        $stmt = $conn->prepare("SELECT id, username FROM users WHERE username = ?");
    }

    $stmt->bind_param("s", $username_or_email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username);
        $stmt->fetch();

        $_SESSION["user_id"] = $id;
        $_SESSION["username"] = $username;
        $_SESSION["login"] = true;
        $_SESSION["login_token"] = bin2hex(random_bytes(16));

        // Set token ke cookie 'login'
        setcookie('login', $_SESSION["login_token"], time() + (30 * 24 * 60), '/');
        header("Location: dashboard.php");
        exit();
    }
    $stmt->close();
}

if (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_COOKIE["login"])) {
    $cookie_token = $_COOKIE["login"];

    if (isset($_SESSION["login_token"]) && $_SESSION["login_token"] === $cookie_token) {
        header("Location: dashboard.php");
        exit();
    }
}

$error_username_or_email = "";
$error_password = "";
$username_or_email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $username_or_email = mysqli_real_escape_string($conn, trim($_POST["username_or_email"]));
    $password = mysqli_real_escape_string($conn, $_POST["password"]);

    if (empty($username_or_email)) {
        $error_username_or_email = "Nama Pengguna atau E-mail wajib diisi!";
    }

    if (empty($password)) {
        $error_password = "Kata Sandi wajib diisi!";
    }

    if (empty($error_username_or_email) && empty($error_password)) {
        if (filter_var($username_or_email, FILTER_VALIDATE_EMAIL)) {
            $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
        } else {
            $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        }

        $stmt->bind_param("s", $username_or_email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $username, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION["login"] = true;

                $_SESSION["login_token"] = bin2hex(random_bytes(16));

                if (isset($_POST["remember-me"])) {
                    setcookie('remember_me_user', $username_or_email, time() + (30 * 24 * 60 * 60), '/');
                } else {
                    setcookie('remember_me_user', '', time() - 3600, '/');
                }

                header("Location: dashboard.php");
                exit();
            } else {
                $error_password = "Kata Sandi yang Anda masukkan salah!";
            }
        } else {
            $error_username_or_email = "Nama Pengguna atau E-mail belum terdaftar!";
        }

        $stmt->close();
    }

    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
    rel="stylesheet">
  <link rel="shortcut icon" href="../assets/images/profile.png" type="image/x-icon">
  <link rel="stylesheet" href="../assets/css/login.css">
  <title>Form Login - Azmi Ananda</title>
</head>

<body>
  <form action="" method="post" autocomplete="off">
    <div class="logo">
      <a href="https://www.google.com">
        <div><img src="../assets/images/search.png" alt="google" class="google">Masuk dengan Google</div>
      </a>
      <a href="https://www.microsoft.com">
        <div><img src="../assets/images/microsoft.png" alt="microsoft" class="microsof">Masuk dengan Microsoft Account
        </div>
      </a>
    </div>
    <hr style="border: none; border-bottom: 1px solid rgb(138, 138, 138); margin: 3rem 0; ">
    <div class="atau">atau</div>
    <fieldset class="username-or-email">
      <label for="username_or_email">E-mail atau Nama Pengguna</label>
      <input type="text" id="username_or_email" name="username_or_email" autofocus
        value="<?=htmlspecialchars($username_or_email)?>">
      <i class="fa-solid fa-user"></i>
    </fieldset>
    <?php if ($error_username_or_email != "") {?>
    <div class="error"><?=$error_username_or_email;?></div>
    <?php }?>
    <fieldset class="password">
      <label for="password">Kata Sandi</label>
      <input type="password" id="password" name="password">
      <i class="fa-solid fa-lock"></i>
      <i class="fa-solid fa-eye-slash"></i>
    </fieldset>
    <?php if ($error_password != "") {?>
    <div class="error"><?=$error_password;?></div>
    <?php }?>
    <fieldset class="remember-me">
      <label for="remember-me">
        <input type="checkbox" id="remember-me" name="remember-me">Ingat saya
      </label>
    </fieldset>
    <div class="submit">
      <button type="submit" name="submit">Masuk</button>
    </div>
    <div class="account-actions">
      <h3><a href="reset.php">Lupa kata sandi?</a></h3>
      <h3>Belum punya akun?<a href="register.php"> Daftar</a></h3>
    </div>
  </form>
</body>

</html>