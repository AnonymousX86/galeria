<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header('Location: index.php');
    exit();
} elseif ($_SESSION["logged_in"] == FALSE) {
    header('Location: index.php');
    exit();
}

$id = $_GET["collection_id"];
require "connect.php";
$sql = $mysqli->prepare('DELETE FROM collections WHERE id = ?');
$sql->bind_param('i', $id);
$sql->execute();
$mysqli->close();;
header('Location: panel.php');
