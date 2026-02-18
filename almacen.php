<!DOCTYPE html>
<html>
<head>
    <title>Almacén</title>
</head>
<body>

<h2>📦 Transferir Producto</h2>

<form method="POST" action="transferir.php">
    Código de barras:<br>
    <input type="text" name="codigo" required><br><br>

    Enviar a:<br>
    <select name="destino" required>
        <option value="">Seleccionar</option>
        <option value="2">Tienda 1</option>
        <option value="3">Tienda 2</option>
    </select><br><br>

    <button type="submit">Transferir</button>
</form>

</body>
</html>
