<?php
$mysqli = @new mysqli("localhost", "root", "", "pai__galeria");
if (!$mysqli)
    exit("Database error: " . $mysqli->connect_errno);
