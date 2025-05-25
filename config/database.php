<?php
require 'config/constants.php';

$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($connection->connect_error) {
    die("Błąd połączenia: " . $connection->connect_error);
}