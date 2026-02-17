
<?php
$url = getenv("DATABASE_URL");

$parts = parse_url($url);

$host = $parts['mysql.railway.internal'];
$user = $parts['root'];
$pass = $parts['DlRenYKhIPxKaVNlNlgUOuxRuHldHPzW'];
$db   = ltrim($parts['railway'],'/');

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error: " . $conn->connect_error);
}

echo "Conectado correctamente";
?>
