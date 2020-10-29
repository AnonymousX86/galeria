<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header('Location: index.php');
    exit();
} elseif ($_SESSION["logged_in"] == FALSE) {
    header('Location: index.php');
    exit();
}
$picture_id = $_POST["picture_id"];
$collection_id = $_POST["collection_id"];
require "connect.php";
$sql = $mysqli->prepare('UPDATE pictures SET collection_id = ? WHERE id = ?');
$sql->bind_param('ii', $collection_id, $picture_id);
$sql->execute();
$mysqli->close();
header('Location: panel.php');
