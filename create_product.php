<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
$host = '127.0.0.1';
$db = 'pharmacy_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $description, $price, $stock]);

    echo "Producto creado exitosamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Crear Producto</h1>
        <form method="POST" action="create_product.php">
            <label for="name">Nombre del Producto:</label>
            <input type="text" id="name" name="name" required>
            <label for="description">Descripción:</label>
            <input type="text" id="description" name="description" required>
            <label for="price">Precio:</label>
            <input type="number" id="price" name="price" step="0.01" required>
            <label for="stock">Stock:</label>
            <input type="number" id="stock" name="stock" required>
            <div class="button-group">
                <button type="submit">Crear Producto</button>
                <button type="button" onclick="window.history.back()">Volver</button>
            </div>
        </form>
    </div>
</body>
</html>
