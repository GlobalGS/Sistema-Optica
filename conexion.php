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
$port = $parts['port'] ?? 3306;

// 🔥 Esta línea corregida
$db = isset($parts['path']) ? ltrim($parts['path'], '/') : '';

if (empty($db)) {
    die("No se pudo obtener el nombre de la base de datos");
}

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

echo "✅ Conectado correctamente a la base de datos: " . $db;

?>
