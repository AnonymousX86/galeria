<?php
session_start();
session_unset();
$_SESSION["logged_in"] = FALSE;
$_SESSION["login"] = NULL;
$_SESSION["user_id"] = NULL;
$_SESSION["invalid_data"] = FALSE;

$login = $_POST["login"];
$password = $_POST["password"];

require_once "connect.php";
$mysqli = @new mysqli($db_host, $db_user, $db_passwd, $db_database);
if (!$mysqli)
    exit("Database error: " . $mysqli->connect_errno);
$sql = $mysqli->prepare('
    SELECT `id`, `login`, `password`
    FROM `users`
    WHERE `login` LIKE ?'
);
$sql->bind_param('s', $login);
$sql->execute();
$result = $sql->get_result();
if ($result->num_rows == 0) {
    $_SESSION["invalid_data"] = TRUE;
    $result->close();
    $mysqli->close();
    header('Location: index.php');
    exit();
}
while ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row["password"])) {
        $_SESSION["logged_in"] = TRUE;
        $_SESSION["login"] = $row["login"];
        $_SESSION["user_id"] = $row["id"];
    } else {
        $_SESSION["invalid_data"] = TRUE;
        header('Location: index.php');
    }
}
header('Location: panel.php');
$result->close();
$mysqli->close();
