<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'employee' && $_SESSION['role'] != 'admin')) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("INSERT INTO sales (user_id, sale_date) VALUES (?, NOW())");
    $stmt->execute([$userId]);
    $saleId = $pdo->lastInsertId();
    
    foreach ($_POST['products'] as $productId => $quantity) {
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $price = $stmt->fetchColumn();
        
        $stmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$saleId, $productId, $quantity, $price * $quantity]);
        
        $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$quantity, $productId]);
    }

    $pdo->commit();
    header("Location: ticket.php?sale_id=$saleId");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Venta</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function filterProducts() {
            const searchQuery = document.getElementById('search').value.toLowerCase();
            const products = document.querySelectorAll('.product-item');

            products.forEach(product => {
                const productName = product.querySelector('label').innerText.toLowerCase();
                if (productName.includes(searchQuery)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }

        function goBack() {
            window.history.back();
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Crear Venta</h1>
        <div class="search-bar">
            <label for="search">Buscar Producto:</label>
            <input type="text" id="search" onkeyup="filterProducts()" placeholder="Buscar producto...">
        </div>
        <form method="POST" action="create_sale.php">
            <?php foreach ($products as $product): ?>
                <div class="product-item">
                    <label for="product_<?php echo $product['id']; ?>"><?php echo $product['name']; ?>:</label>
                    <input type="number" id="product_<?php echo $product['id']; ?>" name="products[<?php echo $product['id']; ?>]" value="0" min="0">
                </div>
            <?php endforeach; ?>
            <div class="button-group">
                <button type="submit">Crear Venta</button>
                <button type="button" onclick="goBack()">Volver</button>
            </div>
        </form>
    </div>
</body>
</html>
