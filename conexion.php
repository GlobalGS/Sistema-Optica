<?php
// 🚨 session_start siempre al inicio
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Variables de entorno de Railway
$host = getenv("MYSQLHOST");
$user = getenv("MYSQLUSER");
$pass = getenv("MYSQLPASSWORD");
$dbname = getenv("MYSQLDATABASE");
$port = getenv("MYSQLPORT");

// Conexión MySQL
$db = new mysqli($host, $user, $pass, $dbname, $port);

if ($db->connect_error) {
    die("❌ Error de conexión a la base de datos: " . $db->connect_error);
}

// ✅ Conexión OK
// echo "✅ Conectado correctamente a la base de datos";
?>
