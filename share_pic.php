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
$_SESSION["picture_shared"] = FALSE;

$guest_id = $_POST["guest_id"];
$picture_id = $_POST["picture_id"];

require_once "connect.php";
$mysqli = @new mysqli($db_host, $db_user, $db_passwd, $db_database);
if (!$mysqli)
    exit("Database error: " . $mysqli->connect_errno);

$sql = $mysqli->prepare('SELECT id FROM users WHERE id = ?');
$sql->bind_param('i', $guest_id);
$sql->execute();
$result = $sql->get_result();
if ($result->num_rows == 0) {
    $_SESSION['user_id_exists'] = FALSE;
    header("Location: show_pic.php?picture=$picture_id");
    $result->close();
    $mysqli->close();
    exit();
}

$sql = $mysqli->prepare('
    SELECT users.id AS owner
    FROM users INNER JOIN pictures p on users.id = p.owner_id
    WHERE p.id = ?
');
$sql->bind_param('i', $picture_id);
$sql->execute();
$result = $sql->get_result();
if ($result->num_rows == 0) {
    $result->close();
    $mysqli->close();
    exit("Unable to share picture $picture_id.");
}
while ($row = $result->fetch_assoc())
    $is_owner = $row["owner"] == $_SESSION["user_id"];
$result->close();
if ($is_owner == TRUE) {
    $sql = $mysqli->prepare('SELECT * FROM shares WHERE user_id = ? AND picture_id = ?');
    $sql->bind_param('ii', $guest_id, $picture_id);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows == 0) {
        $sql = $mysqli->prepare('INSERT INTO shares VALUES (?, ?)');
        $sql->bind_param('ii', $guest_id, $picture_id);
        $sql->execute();
    }
    $_SESSION["picture_shared"] = TRUE;
    $result->close();
}

$result->close();
$mysqli->close();
header("Location: show_pic.php?picture=$picture_id");
