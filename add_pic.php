<?php
session_start();
$_SESSION["invalid_picture"] = FALSE;
$_SESSION["file_exists"] = FALSE;
$_SESSION["picture_uploaded"] = FALSE;

$user_id = isset($_POST["user_id"]) ? $_POST["user_id"] : $_SESSION["user_id"];

$filename = $_FILES["file_uploaded"]["name"];
$tmp_name = $_FILES["file_uploaded"]["tmp_name"];
$hash = md5(date('dmYHis')).".".pathinfo($filename)["extension"];
$upload_dir = $_SERVER["DOCUMENT_ROOT"]."/pai/galeria/pictures";

if (!exif_imagetype($tmp_name))
    $_SESSION["invalid_picture"] = TRUE;
else {
    require "connect.php";
    if (move_uploaded_file($tmp_name, "$upload_dir/$hash")) {
        $sql = $mysqli->prepare('INSERT INTO `pictures` VALUES (NULL, ?, ?, ?, DEFAULT, NULL)');
        $sql->bind_param('ssi', $filename, $hash, $user_id);
        $sql->execute();
        $_SESSION["picture_uploaded"] = $sql->errno == 0;
            if ($sql->affected_rows == 0)
                exit("Error: " . $sql->errno . " " . $sql->error);
    } else
        exit("Can\'t upload file");
    $mysqli->close();
}
header('Location: panel.php');
