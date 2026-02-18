<?php
include("conexion.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ====== TRANSFERIR PRODUCTO ======
if (isset($_POST['transferir'])) {
    $codigo = $_POST['codigo'] ?? '';
    $destino_id = intval($_POST['destino'] ?? 0);
    $almacen_id = 1; // ID del almacén

    if (!$codigo || !$destino_id) {
        $msg = "❌ Datos incompletos para transferencia";
    } else {
        // Buscar producto
        $stmt = $db->prepare("SELECT id, nombre FROM productos WHERE codigo_barras = ?");
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $msg = "❌ Producto no existe";
        } else {
            $producto = $result->fetch_assoc();
            $producto_id = $producto['id'];

            // Verificar stock en almacén
            $stmt = $db->prepare("SELECT cantidad FROM inventario WHERE producto_id=? AND sucursal_id=?");
            $stmt->bind_param("ii", $producto_id, $almacen_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $inv = $res->fetch_assoc();

            if (!$inv || $inv['cantidad'] <= 0) {
                $msg = "❌ Sin stock en almacén";
            } else {
                // Restar en almacén
                $db->query("UPDATE inventario SET cantidad=cantidad-1 WHERE producto_id=$producto_id AND sucursal_id=$almacen_id");

                // Sumar en tienda destino
                $db->query("INSERT INTO inventario (producto_id, sucursal_id, cantidad)
                    VALUES ($producto_id, $destino_id, 1)
                    ON DUPLICATE KEY UPDATE cantidad = cantidad + 1");

                // Registrar movimiento
                $db->query("INSERT INTO movimientos (producto_id, sucursal_origen, sucursal_destino, cantidad)
                    VALUES ($producto_id, $almacen_id, $destino_id, 1)");

                $msg = "✅ Producto transferido correctamente";
            }
        }
    }
}

// ====== INGRESAR PRODUCTO AL ALMACÉN ======
if (isset($_POST['ingresar'])) {
    $codigo = $_POST['codigo'] ?? '';
    $cantidad = intval($_POST['cantidad'] ?? 0);
    $almacen_id = 1;

    if (!$codigo || $cantidad <= 0) {
        $msg = "❌ Datos incompletos para ingreso";
    } else {
        // Buscar producto
        $stmt = $db->prepare("SELECT id FROM productos WHERE codigo_barras=?");
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $msg = "❌ Producto no existe";
        } else {
            $producto = $result->fetch_assoc();
            $producto_id = $producto['id'];

            // Insertar o actualizar inventario
            $db->query("INSERT INTO inventario (producto_id, sucursal_id, cantidad)
                VALUES ($producto_id, $almacen_id, $cantidad)
                ON DUPLICATE KEY UPDATE cantidad = cantidad + $cantidad");

            $msg = "✅ Producto ingresado al almacén";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Almacén</title>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        body { font-family: Arial; padding: 20px; }
        input, select, button { padding: 10px; margin: 5px 0; width: 100%; }
        button { background: #28a745; color: white; border: none; cursor: pointer; }
        #reader { width: 300px; margin-top: 10px; }
        .msg { font-weight: bold; margin: 10px 0; }
    </style>
</head>
<body>

<h2>📦 Transferir Producto</h2>

<?php if(isset($msg)) echo "<p class='msg'>$msg</p>"; ?>

<form method="POST">
    Código de barras:<br>
    <input type="text" name="codigo" id="codigo" required>
    <button type="button" onclick="iniciarScanner()">📱 Escanear Código</button>
    <div id="reader"></div>
    <br>
    Enviar a:<br>
    <select name="destino" required>
        <option value="">Seleccionar</option>
        <option value="2">Tienda 1</option>
        <option value="3">Tienda 2</option>
    </select><br>
    <button type="submit" name="transferir">Transferir</button>
</form>

<hr>

<h2>➕ Ingresar Producto al Almacén</h2>

<form method="POST">
    Código de barras:<br>
    <input type="text" name="codigo" id="codigo_ingreso" required>
    <button type="button" onclick="iniciarScannerIngreso()">📱 Escanear Código</button>
    <div id="reader_ingreso"></div>
    <br>
    Cantidad:<br>
    <input type="number" name="cantidad" required>
    <button type="submit" name="ingresar">Guardar en Almacén</button>
</form>

<script>
let scanner;

function iniciarScanner() {
    scanner = new Html5Qrcode("reader");
    scanner.start(
        { facingMode: "environment" },
        {
            fps: 10,
            qrbox: { width: 250, height: 150 },
            formatsToSupport: [
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.EAN_8,
                Html5QrcodeSupportedFormats.UPC_A,
                Html5QrcodeSupportedFormats.UPC_E
            ]
        },
        (decodedText) => {
            document.getElementById("codigo").value = decodedText;
            scanner.stop().then(()=>{ document.getElementById("reader").innerHTML = ""; });
        }
    );
}

let scannerIngreso;
function iniciarScannerIngreso() {
    scannerIngreso = new Html5Qrcode("reader_ingreso");
    scannerIngreso.start(
        { facingMode: "environment" },
        {
            fps: 10,
            qrbox: { width: 250, height: 150 },
            formatsToSupport: [
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.EAN_8,
                Html5QrcodeSupportedFormats.UPC_A,
                Html5QrcodeSupportedFormats.UPC_E
            ]
        },
        (decodedText) => {
            document.getElementById("codigo_ingreso").value = decodedText;
            scannerIngreso.stop().then(()=>{ document.getElementById("reader_ingreso").innerHTML = ""; });
        }
    );
}
</script>

</body>
</html>
