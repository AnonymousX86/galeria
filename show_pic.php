<?php
session_start();
$name = '';
$picture = '';
$can_see = FALSE;
if (isset($_GET["picture"])) {
    require "connect.php";
    $sql = $mysqli->prepare('SELECT `id`, `owner_id`, `filename`, `hash` FROM `pictures` WHERE `id` = ?');
    $sql->bind_param('i', $_GET["picture"]);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows == 1) {
        while ($row = $result->fetch_assoc()) {
            $picture_id = $row["id"];
            $filename = $row["filename"];
            $hash = $row["hash"];
            if (isset($_SESSION["user_id"]))
                if ($_SESSION["user_id"] == $row["owner_id"])
                    $can_see = TRUE;
        }
    }
    $result->close();

    if ($can_see == FALSE) {
        $sql = $mysqli->prepare('SELECT user_id FROM shares WHERE picture_id = ? AND user_id = ?');
        $user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : -1;
        $sql->bind_param('ii', $_GET["picture"], $user_id);
        $sql->execute();
        $result = $sql->get_result();
        if ($result->num_rows == 1)
            $can_see = TRUE;
    }

    $result->close();
    $mysqli->close();
    if ($can_see == TRUE)
        $picture = '<img
            src="/pai/galeria/pictures/' . $hash . '"
            alt=""
            class="rounded"
            />';
}
?>
<!doctype html>
<html lang="pl">
<!--suppress HtmlRequiredTitleElement -->
<head>
    <?php require_once "head.html" ?>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>Galeria</h1>
        </div>
        <div class='col-12'>
        <?php
        if ($picture == '')
            echo "<p>No można wyświetlić tego zdjęcia.</p>";
        else
            echo "<p>$filename</p>".$picture;
        ?>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <a href="panel.php"><button class="btn btn-secondary w-25 mb-4">Wróć</button></a>
        <?= $picture != '' ? "
            <form action='share_pic.php' method='post'>
                <input type='hidden' name='picture_id' value='".$picture_id."'/>
                <label>
                    ID gościa: <input type='number' min='1' max='99999999999' name='guest_id'/>
                </label><br/>
                <input type='submit' class='btn btn-primary w-25' value='Udostępnij'/>
            </form>" : ""
        ?>
        </div>
        <div class='col-12 mt-3'>
        <?php
        if (isset($_SESSION["user_id_exists"]))
            if ($_SESSION["user_id_exists"] == FALSE)
                echo "<div class='alert alert-warning mb-2'>Użytkownik o takim ID nie istnieje.</div>";
        elseif (isset($_SESSION["picture_shared"]))
            if ($_SESSION["picture_shared"] == TRUE)
                echo "<div class='alert alert-success'>Zdjęcie zostało udostępnione.</div>";
            else
                echo "<div class='alert alert-warning'>Nie można udostępnić zdjęcia.</div>";
        ?>
        </div>
    </div>
</div>
<?php require_once "scripts.html" ?>
</body>
</html>
