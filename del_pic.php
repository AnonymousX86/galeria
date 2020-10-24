<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header('Location: index.php');
    exit();
} elseif ($_SESSION["logged_in"] == FALSE) {
    header('Location: index.php');
    exit();
} elseif (!isset($_POST["id"])) {
    header('Location: index.php');
    exit();
}
$_SESSION["picture_deleted"] = FALSE;
$picture_id = $_POST["id"];
require_once "connect.php";
$mysqli = @new mysqli($db_host, $db_user, $db_passwd, $db_database);
if (!$mysqli)
    exit("Database error: " . $mysqli->connect_errno);
$sql = $mysqli->prepare('SELECT `owner_id` FROM `pictures` WHERE `id` = ?');
$sql->bind_param('i', $picture_id);
$sql->execute();
$result = $sql->get_result();
$is_owner = FALSE;
if ($result->num_rows == 1)
    while ($row = $result->fetch_assoc())
        if ($_SESSION["user_id"] == $row["owner_id"])
            $is_owner = TRUE;
$result->close();
if ($is_owner == TRUE) {
    $sql = $mysqli->prepare('SELECT hash from pictures WHERE id = ?');
    $sql->bind_param('i', $picture_id);
    $sql->execute();
    $result = $sql->get_result();
    while ($row = $result->fetch_assoc())
        if (unlink($_SERVER["DOCUMENT_ROOT"]."/pai/galeria/pictures/".$row["hash"])) {
            $_SESSION["picture_deleted"] = TRUE;
            $sql = $mysqli->prepare('DELETE FROM pictures WHERE id = ?');
            $sql->bind_param('i', $picture_id);
            $sql->execute();
        }
    $result->close();
}
$mysqli->close();
header('Location: panel.php');
