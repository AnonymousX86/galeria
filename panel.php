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
<body>
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
                        <a href="logout.php">
                            <button class="btn btn-warning">Wyloguj się</button>
                        </a>
                        <a href="delete_profile.php">
                            <button class="btn btn-danger">Usuń konto</button>
                        </a>
                    </div>
                </div>
                <div class="col-12">
                    <p style="opacity: 0.9">Twoje ID Gościa: <?= $_SESSION["user_id"] ?></p>
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
            if (isset($_SESSION["picture_deleted"])) {
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
                    WHERE u.id = ? AND pictures.collection_id IS NULL
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
                    foreach ($pictures as $file) {
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
                                        onsubmit='return confirm(\"Czy na pewno chcesz usunąć \\\"" . $file["filename"] . "\\\"?\")'
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
                                <div class='col-12'>
                                    <form action='add_pic_to_coll.php' method='post' class='w-100 mt-2'>
                                        <input type='hidden' name='picture_id' value='" . $file["id"] . "'/>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <select name='collection_id' class='form-control w-100' required>    
                                                    <option selected hidden disabled>-</option>
                                            ";
                        require "connect.php";
                        $sql = $mysqli->prepare('SELECT id, name FROM collections WHERE owner_id = ?');
                        $sql->bind_param('i', $_SESSION["user_id"]);
                        $sql->execute();
                        $result = $sql->get_result();
                        while ($row = $result->fetch_assoc())
                            echo "<option value='".$row["id"]."'>".$row["name"]."</option>";
                        echo "
                                            </select>
                                        </div>
                                            <div class='col-12'>
                                                <input type='submit' value='Dodaj do kolekcji' class='btn btn-primary w-100 mt-1'/>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>";
                    }
                $result->close();
                $mysqli->close();
                ?>
            </div>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-12">
            <h2>Twoje kolekcje</h2>
        </div>
        <div class="col-12">
            <?php
            require "connect.php";
            $sql = $mysqli->prepare('SELECT id, name FROM collections WHERE owner_id = ?');
            $sql->bind_param('i', $_SESSION["user_id"]);
            $sql->execute();
            $result = $sql->get_result();
            $collections = array();
            while ($row = $result->fetch_assoc())
                $collections[] = array(
                    "id" => $row["id"],
                    "name" => $row["name"]
                );
            $result->close();
            $mysqli->close();
            if ($collections == [])
                echo "<p>Nie posiadasz żadnych kolekcji</p>";
            else {
                echo "<div class='row'>";
                foreach ($collections as $c) {
                    echo "
                        <div class='col-12'>
                            <h3>" . $c["name"] . "</h3>
                            <div class='row'>";
                    require "connect.php";
                    $sql = $mysqli->prepare('
                        SELECT pictures.id as p_id, name, hash
                        FROM pictures 
                            INNER JOIN collections c ON pictures.collection_id = c.id 
                        WHERE pictures.owner_id = ? AND c.id = ?');
                    $sql->bind_param('ii', $_SESSION["user_id"], $c["id"]);
                    $sql->execute();
                    $result = $sql->get_result();
                    while ($row = $result->fetch_assoc())
                        echo "
                            <div class='col-4'>
                                <img class='img-fluid rounded mb-1 w-100' src='/pai/galeria/pictures/" . htmlentities($row["hash"]) . "' alt='" . $row["name"] . "'/>
                                <a href='del_pic_from_coll.php?picture_id=".$row["p_id"]."'><button class='btn btn-danger'>Usuń z kolekcji</button></a>
                            </div>";
                    $result->close();
                    echo "
                        </div>
                            <p class='mt-3'>
                                <a href='del_coll.php?collection_id=" . $c["id"] . "'>
                                    <button class='btn btn-danger'>Usuń kolekcję</button>
                                </a>
                            </p>
                        </div>";
                }
                echo "</div>";
            }
            ?>
        </div>
        <div class="col-12">
            <h3>Stwórz nową kolekcję</h3>
            <form action="add_coll.php" method="post">
                <div class="row">
                    <div class="col-6">
                        <label for="collection_name">Nazwa</label>
                    </div>
                    <div class="col-6">
                        <input type="text" name="collection_name" id="collection_name" required/>
                    </div>
                    <div class="col-12">
                        <input type="submit" value="Stwórz" class="btn btn-primary"/>
                    </div>
                </div>
            </form>
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
