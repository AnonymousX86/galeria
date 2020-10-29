<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header('Location: index.php');
    exit();
} elseif ($_SESSION["logged_in"] == FALSE) {
    header('Location: index.php');
    exit();
}

$name = $_POST["collection_name"];
require "connect.php";
$sql = $mysqli->prepare('INSERT INTO collections VALUES (NULL, ?, ?)');
$sql->bind_param('is', $_SESSION["user_id"], $name);
$sql->execute();
$mysqli->close();;
header('Location: panel.php');
