<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$url = getenv("DATABASE_URL");

if (!$url) {
    die("No se encontró DATABASE_URL");
}

$parts = parse_url($url);

$host = $parts['host'];
$user = $parts['user'];
$pass = $parts['pass'];
$db   = ltrim($parts['path'],'/');

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

echo "✅ Conectado correctamente a la base de datos";

?>
