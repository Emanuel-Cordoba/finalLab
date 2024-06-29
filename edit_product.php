<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
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

$id = $_GET['id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id']; 
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?");
    $stmt->execute([$name, $description, $price, $stock, $id]);

    header("Location: manage_products.php");
    exit;
} else {
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        
        if (!$product) {
           
            header("Location: manage_products.php");
            exit;
        }
    } else {
       
        header("Location: manage_products.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Editar Producto</h1>
        <form method="POST" action="edit_product.php">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            <br>
            <label for="description">Descripción:</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            <br>
            <label for="price">Precio:</label>
            <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            <br>
            <label for="stock">Stock:</label>
            <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
            <br>
            <button type="submit">Guardar Cambios</button>
        </form>
        <button type="button" onclick="window.location.href='manage_products.php'">Cancelar</button>
    </div>
</body>
</html>
