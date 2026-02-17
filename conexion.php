<?php

$host = $_ENV['mysql.railway.internal'];      // mysql.railway.internal
$user = $_ENV['root'];      // root
$pass = $_ENV['DlRenYKhIPxKaVNlNlgUOuxRuHldHPzW'];  // contraseña
$db   = $_ENV['railway'];  // railway
$port = $_ENV['3306'];      // 3306

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

echo "Conectado a Railway correctamente";
?>
