<?php
session_start();
session_unset();
// Session variables
$_SESSION["passwords_no_not_match"] = FALSE;
$_SESSION["login_exists"] = FALSE;
$_SESSION["user_registered"] = FALSE;
// POST variables
$login = $_POST["login"];
$password1 = $_POST["password1"];
$password2 = $_POST["password2"];

if ($password1 != $password2) {
    $_SESSION["passwords_no_not_match"] = TRUE;
    header('Location: index.php');
    exit();
}

require "connect.php";

$sql = $mysqli->prepare('SELECT login FROM users WHERE BINARY login LIKE ?');
$sql->bind_param('s', $login);
$sql->execute();
$result = $sql->get_result();
if ($result->num_rows != 0) {
    $_SESSION["login_exists"] = TRUE;
    $result->close();
    $mysqli->close();
    header('Location: index.php');
    exit();
}

$password = password_hash($password1, PASSWORD_DEFAULT);
$sql = $mysqli->prepare('INSERT INTO `users` VALUES (NULL, ?, ?)');
$sql->bind_param('ss', $login, $password);
$sql->execute();
if ($sql->errno != 0) {
    echo "Unable to add user to database! <br />
          Error: " . $sql->errno;
    $result->close();
    $mysqli->close();
    exit();
}

$result->close();
$mysqli->close();
$_SESSION["user_registered"] = TRUE;
header('Location: index.php');
