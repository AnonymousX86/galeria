<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header('Location: index.php');
    exit();
} elseif ($_SESSION["logged_in"] == FALSE) {
    header('Location: index.php');
    exit();
}
$_SESSION["user_id_exists"] = TRUE;
$_SESSION["collection_shared"] = FALSE;

$guest_id = $_POST["guest_id"];
$collection_id = $_POST["collection_id"];

require "connect.php";

$sql = $mysqli->prepare('SELECT id FROM users WHERE id = ?');
$sql->bind_param('i', $guest_id);
$sql->execute();
$result = $sql->get_result();
if ($result->num_rows == 0) {
    $_SESSION['user_id_exists'] = FALSE;
    $result->close();
    $mysqli->close();
    header("Location: panel.php");
    exit();
}

$sql = $mysqli->prepare('
    SELECT users.id AS owner
    FROM users INNER JOIN collections c on users.id = c.owner_id
    WHERE c.id = ?
');
$sql->bind_param('i', $collection_id);
$sql->execute();
$result = $sql->get_result();
if ($result->num_rows == 0) {
    $result->close();
    $mysqli->close();
    exit("Unable to share picture $collection_id.");
}
while ($row = $result->fetch_assoc())
    $is_owner = $row["owner"] == $_SESSION["user_id"];
$result->close();
if ($is_owner == TRUE) {
    $sql = $mysqli->prepare('SELECT * FROM shares_colls WHERE user_id = ? AND collection_id = ?');
    $sql->bind_param('ii', $guest_id, $collection_id);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows == 0) {
        $sql = $mysqli->prepare('INSERT INTO shares_colls VALUES (?, ?)');
        $sql->bind_param('ii', $guest_id, $collection_id);
        $sql->execute();
    }
    $_SESSION["collection_shared"] = TRUE;
    $result->close();
}

$result->close();
$mysqli->close();
header("Location: panel.php");
