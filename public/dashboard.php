<?php
session_start();
if (!$_SESSION["user_id"]) {
    header("Location: login.php");
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Dashboard">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
    rel="stylesheet">
  <link rel="shortcut icon" href="../assets/images/profile.png" type="image/x-icon">
  <title>Dashboard - azmi ananda</title>
</head>
<style>
body {
  width: 100%;
  height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  font-family: "Poppins", sans-serif;
  overflow: hidden;
}

img {
  width: 450px;
  height: 450px;
}

h1 {
  font-size: 20px;
  font-weight: 500;
}

h3 {
  font-weight: 500;
}

h3 a {
  color: rgb(57, 134, 182);
  text-decoration: none;
}
</style>

<body>
  <img src="../assets/images/200.png" alt="OK">
  <h1>Autentikasi telah berhasil diselesaikan. Selamat datang di halaman utama👋👋</h1>
  <h3><a href="logout.php">&lt;&lt; Keluar</a></h3>
</body>

</html>
