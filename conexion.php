<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$DB_HOST = $_ENV["DB_HOST"];
$DB_USER = $_ENV["DB_USER"];
$DB_PASSWORD = $_ENV["DB_PASSWORD"];
$DB_NAME = $_ENV["DB_NAME"];
$DB_PORT = $_ENV["DB_PORT"];

$db = mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME, $DB_PORT);

if (!$db) {
    echo "❌ Error de conexión: " . mysqli_connect_error();
} else {
    echo "✅ Conectado correctamente a la base de datos";
}

?>
