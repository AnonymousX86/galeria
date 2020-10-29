<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header('Location: index.php');
    exit();
} elseif ($_SESSION["logged_in"] == FALSE) {
    header('Location: index.php');
    exit();
}
if (!isset($_SESSION["invalid_password"]))
    $_SESSION["invalid_password"] = FALSE;
elseif ($_SESSION["invalid_password"] == FALSE)
    if(isset($_POST["confirm_password"])) {
        $confirmed = FALSE;

        require "connect.php";
        $sql = $mysqli->prepare('SELECT password FROM users WHERE id = ?');
        $sql->bind_param('i', $_SESSION["user_id"]);
        $sql->execute();
        $result = $sql->get_result();
        while ($row = $result->fetch_assoc())
            if (password_verify($_POST["confirm_password"], $row["password"]))
                $confirmed = TRUE;
        $result->close();

        if ($confirmed == TRUE) {
            $sql = $mysqli->prepare('DELETE FROM users WHERE id = ?');
            $sql->bind_param('i', $_SESSION["user_id"]);
            $sql->execute();
            if ($sql->affected_rows == 0)
                exit("Unable to delete account ".$_SESSION["user_id"]);
        }

        $mysqli->close();
        header('Location: logout.php');
        exit();
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
        <div class="col-12">
            <form
                action="delete_profile.php"
                method="post"
                onsubmit="return confirm('Czy jesteś pewien, że chcesz na zawsze usunąć swoje konto wraz ze swoimi zdjęciami?')"
            >
                <label>
                    Potwierdź swoje hasło, <strong><?= $_SESSION["login"] ?></strong>:
                    <input type="password" name="confirm_password" required/>
                </label><br/>
                <input type="submit" value="Usuń konto" class="btn btn-danger"/>
            </form>
        </div>
        <?php
        if (isset($_SESSION["invalid_password"]))
            if ($_SESSION["invalid_password"] == TRUE)
                echo "<div class='col-12'><div class='alert alert-warning'>Niepoprawne hasło.</div></div>";
        ?>
    </div>
</div>
</body>
</html>
