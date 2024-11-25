<?php
session_start();

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

setcookie('login', '', time() - 3600, '/');
setcookie('remember_me_user', '', time() - 3600, '/');

session_destroy();

header("Location: login.php");
exit();
