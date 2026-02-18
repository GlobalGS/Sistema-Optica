<?php
include("conexion.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_POST['codigo']) || !isset($_POST['destino'])) {
    die("❌ Datos incompletos");
}

$codigo = $_POST['codigo'];
$destino_id = intval($_POST['destino']);
$almacen_id = 1;

// 1️⃣ Buscar producto
$stmt = $db->prepare("SELECT id, nombre FROM productos WHERE codigo_barras = ?");
$stmt->bind_param("s", $codigo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("❌ Producto no existe");
}

$producto = $result->fetch_assoc();
$producto_id = $producto['id'];

// 2️⃣ Verificar stock en almacén
$stmt = $db->prepare("SELECT cantidad FROM inventario WHERE producto_id = ? AND sucursal_id = ?");
$stmt->bind_param("ii", $producto_id, $almacen_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    die("❌ Producto no existe en almacén");
}

$inv = $res->fetch_assoc();

if ($inv['cantidad'] <= 0) {
    die("❌ Sin stock en almacén");
}

// 3️⃣ Restar en almacén
$db->query("UPDATE inventario 
            SET cantidad = cantidad - 1 
            WHERE producto_id = $producto_id 
            AND sucursal_id = $almacen_id");

// 4️⃣ Sumar en destino
$db->query("
INSERT INTO inventario (producto_id, sucursal_id, cantidad)
VALUES ($producto_id, $destino_id, 1)
ON DUPLICATE KEY UPDATE cantidad = cantidad + 1
");

// 5️⃣ Registrar movimiento
$db->query("
INSERT INTO movimientos (producto_id, sucursal_origen, sucursal_destino, cantidad)
VALUES ($producto_id, $almacen_id, $destino_id, 1)
");

echo "✅ Producto transferido correctamente";
?>
