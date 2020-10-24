<?php
session_start();
if (isset($_SESSION["logged_in"]))
    if ($_SESSION["logged_in"] == TRUE) {
        header('Location: panel.php');
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
            <p>Prosimy o zalogowanie się.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="row no-gutters">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title">Zaloguj</h2>
                            <form action="login.php" method="post">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="login-login">Login:</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="text" maxlength="40" name="login" id="login-login" title="Login" required/>
                                    </div>
                                    <div class="col-6">
                                        <label for="login-password">Hasło:</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="password" maxlength="40" name="password" id="login-password" title="Hasło" required/>
                                    </div>
                                    <div class="col-12">
                                        <input
                                                type="submit"
                                                value="Zaloguj się"
                                                class="btn btn-primary"
                                                title="Kliknij aby się zalogować"
                                        />
                                    </div>
                                </div>
                            </form>
                            <?php
                            if (isset($_SESSION["invalid_data"])) {
                                if ($_SESSION["invalid_data"] == TRUE) {
                                    echo "
                                        <div class='mt-3 alert alert-warning'>
                                        <span>Błędny login lub hasło!</span>
                                        </div>";
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row no-gutters mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title">Zarejestruj</h2>
                            <form action="register.php" method="post">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="register-login">Login:</label>
                                    </div>
                                    <div class="col-6">
                                        <input
                                                type="text"
                                                maxlength="40"
                                                name="login"
                                                id="register-login"
                                                title="Login"
                                                required
                                        />
                                    </div>
                                    <div class="col-6">
                                        <label for="register-password1">Hasło:</label>
                                    </div>
                                    <div class="col-6">
                                        <input
                                                type="password"
                                                maxlength="40"
                                                name="password1"
                                                id="register-password1"
                                                title="Hasło"
                                                required
                                        />
                                    </div>
                                    <div class="col-6">
                                        <label for="register-password2">Potwierdź hasło:</label>
                                    </div>
                                    <div class="col-6">
                                        <input
                                                type="password"
                                                maxlength="40"
                                                name="password2"
                                                id="register-password2"
                                                title="Potwierdź hasło"
                                                required
                                        />
                                    </div>
                                    <div class="col-12">
                                        <input
                                                type="submit"
                                                value="Zarejestruj się"
                                                class="btn btn-primary"
                                                title="Kliknij aby się zarejestrować"
                                        />
                                    </div>
                                </div>
                            </form>
                                <?php
                                if (isset($_SESSION["passwords_no_not_match"])) {
                                    if ($_SESSION["passwords_no_not_match"] == TRUE) {
                                        echo "
                                            <div class='mt-3 alert alert-warning'>
                                            <span>Hasła nie pasują do siebie!</span>
                                            </div>";
                                    }
                                } elseif (isset($_SESSION["login_exists"])) {
                                    if ($_SESSION["login_exists"] == TRUE) {
                                        echo "
                                            <div class='mt-3 alert alert-warning'>
                                            <span>Taki użytkownik już istnieje!</span>
                                            </div>";
                                    }
                                }
                                if (isset($_SESSION["user_registered"])) {
                                    if ($_SESSION["user_registered"] == TRUE) {
                                        echo "
                                            <div class='mt-3 alert alert-success'>
                                            <span>Dziękujemy za rejestrację! Teraz możesz się zalogować.</span>
                                            </div>";
                                    }
                                }
                                ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col mt-4 mt-lg-0">
            <div class="card">
                <div class="card-header">
                    Autor strony
                </div>
                <div class="card-body">
                    <h5 class="card-title">Jakub Suchenek</h5>
                </div>
                <img class="card-img-bottom" src="https://i.imgur.com/tl3VCwA.jpg" alt=""/>
            </div>
        </div>
    </div>
</div>
<?php require_once "scripts.html" ?>
</body>
</html>
