<!DOCTYPE html>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<html>
<head>
    <title>Almacén</title>

    <!-- Librería para escanear código de barras -->
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body>

<h2>📦 Transferir Producto</h2>

<form method="POST" action="transferir.php">

    Código de barras:<br>
    <input type="text" name="codigo" id="codigo" required>
    <br><br>

    <button type="button" onclick="iniciarScanner()">📱 Escanear Código</button>
    <br><br>

    <!-- Cámara -->
    <div id="reader" style="width:300px;"></div>
    <br>

    Enviar a:<br>
    <select name="destino" required>
        <option value="">Seleccionar</option>
        <option value="2">Tienda 1</option>
        <option value="3">Tienda 2</option>
    </select><br><br>

    <button type="submit">Transferir</button>

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
            detenerScanner();
        }
    ).catch(err => {
        alert("Error al abrir cámara: " + err);
    });
}

function detenerScanner() {
    if (scanner) {
        scanner.stop().then(() => {
            document.getElementById("reader").innerHTML = "";
        });
    }
}
</script>

</body>
</html>
