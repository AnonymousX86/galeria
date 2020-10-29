<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header('Location: index.php');
    exit();
} elseif ($_SESSION["logged_in"] == FALSE) {
    header('Location: index.php');
    exit();
}
?>
<!doctype html>
<html lang="pl">
<!--suppress HtmlRequiredTitleElement -->
<head>
    <?php require "head.html" ?>
</head>
<body class="bg-light">
<div class="container mt-2 mb-5">
    <div class="row">
        <div class="col">
            <h1>Galeria</h1>
            <div class="row">
                <div class="col-6">
                    <p>Dzień dobry, <strong><?= $_SESSION["login"] ?></strong>!</p>
                </div>
                <div class="col-6">
                    <div class="float-right">
                        <a href="logout.php"><button class="btn btn-warning">Wyloguj się</button></a>
                        <a href="delete_profile.php"><button class="btn btn-danger">Usuń konto</button></a>
                    </div>
                </div>
                <div class="col-12">
                    <p class="">Twoje ID Gościa: <?= $_SESSION["user_id"] ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <h2>Twoje zdjęcia</h2>
        </div>
        <div class="col-12">
            <?php
            if(isset($_SESSION["picture_deleted"])) {
                if ($_SESSION["picture_deleted"] == TRUE)
                    echo "
                        <div class='alert alert-success'>
                            <span>Zdjęcie zostało usunięte.</span>
                        </div>";
                else
                    echo "
                        <div class='alert alert-danger'>
                            <span>Nie można usunąć zdjęcia.</span>
                        </div>";
                unset($_SESSION["picture_deleted"]);
            }
            ?>
        </div>
        <div class="col-12">
            <div class="row pictures">
                <?php
                require "connect.php";
                $sql = $mysqli->prepare('
                    SELECT pictures.`id`, `filename`, `hash`
                    FROM pictures
                    INNER JOIN users u on pictures.owner_id = u.id
                    WHERE u.id = ?
                ');
                $sql->bind_param('i', $_SESSION["user_id"]);
                $sql->execute();
                $result = $sql->get_result();
                $pictures = array();
                while ($row = $result->fetch_assoc())
                    $pictures[] = array(
                        "id" => $row["id"],
                        "filename" => $row["filename"],
                        "hash" => $row["hash"]
                    );
                if (count($pictures) == 0)
                    echo "<div class='col-12'><p>Nie posiadasz żadnych zdjęć.</p></div>";
                else
                    foreach ($pictures as $file)
                        echo "
                        <div class='col-6 col-md-4 col-lg-3 my-3'>
                            <p class='mb-1'>" . $file["filename"] . "</p>
                            <img 
                                src='/pai/galeria/pictures/" . htmlentities($file["hash"]) . "' alt='' 
                                class='img-fluid rounded w-100'
                            />
                            <div class='row'>
                                <div class='col-6'>
                                    <form 
                                        action='del_pic.php' 
                                        method='post' 
                                        onsubmit='return confirm(\"Czy na pewno chcesz usunąć \\\"".$file["filename"]."\\\"?\")'
                                    >
                                        <input type='hidden' name='id' value='" . $file["id"] . "' />
                                        <input type='submit' value='Usuń' class='btn btn-danger mt-1 w-100' />
                                    </form>
                                </div>
                                <div class='col-6'>
                                    <form action='show_pic.php' method='get'>
                                        <input type='hidden' name='picture' value='" . $file["id"] . "'>
                                        <input type='submit' value='Pokaż' class='btn btn-secondary mt-1 w-100' />
                                    </form>
                                </div>
                            </div>
                        </div>";
                $result->close();
                $mysqli->close();
                ?>
            </div>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-12">
            <h3>Prześlij nowe zdjęcie</h3>
        </div>
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <form action="add_pic.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="max_file_size" value="512000"/>
                        <input type="hidden" name="user_id" value="<?= $_SESSION["user_id"] ?>">
                        <input type="file" name="file_uploaded" required/>
                        <input type="submit" value="Prześlij zdjęcie" class="btn btn-primary"/>
                    </form>
                </div>
                <div class="col-12">
                    <div class="errors">
                        <?php
                        $text = '';
                        if (isset($_SESSION["invalid_picture"]))
                            if ($_SESSION["invalid_picture"] == TRUE)
                                $text = 'Błędny format zdjęcia!';
                            elseif (isset($_SESSION["file_exists"]))
                                if ($_SESSION["file_exists"] == TRUE)
                                    $text = 'Plik o takiej nazwie już istnieje!';
                        if ($text != '')
                            echo "<div class='alert alert-danger mt-2'>$text</div>";
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-12">
            <h2>Udostępnione zdjęcia</h2>
        </div>
        <div class="col-12">
            <div class="row">
                <?php
                require "connect.php";
                $sql = $mysqli->prepare('
                    SELECT id, filename, hash
                    FROM pictures INNER JOIN shares s ON pictures.id = s.picture_id
                    WHERE s.user_id = ?
                ');
                $sql->bind_param('i', $_SESSION["user_id"]);
                $sql->execute();
                $result = $sql->get_result();
                $pictures = array();
                if ($result->num_rows > 0)
                    while ($row = $result->fetch_assoc())
                        $pictures[] = array(
                            "id" => $row["id"],
                            "filename" => $row["filename"],
                            "hash" => $row["hash"]
                        );
                if (count($pictures) == 0)
                    echo "<div class='col-12'><p>Nie posiadasz żadnych udostępnionych zdjęć.</p></div>";
                else
                    foreach ($pictures as $file)
                        echo "
                            <div class='col-6 col-md-4 col-lg-3 my-3'>
                                <p class='mb-1'>" . $file["filename"] . "</p>
                                <img 
                                    src='/pai/galeria/pictures/" . htmlentities($file["hash"]) . "' alt='' 
                                    class='img-fluid rounded w-100'
                                />
                                <div class='row'>
                                    <div class='col-6'>
                                        <form action='show_pic.php' method='get'>
                                            <input type='hidden' name='picture' value='" . $file["id"] . "'>
                                            <input type='submit' value='Pokaż' class='btn btn-secondary mt-1 w-100'/>
                                        </form>
                                    </div>
                                </div>
                            </div>";
                ?>
            </div>
        </div>
    </div>
</div>
<?php require_once "scripts.html" ?>
</body>
</html>
