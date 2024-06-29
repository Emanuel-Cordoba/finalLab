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

if (isset($_GET['sale_id'])) {
    $saleId = $_GET['sale_id'];
    
    $stmt = $pdo->prepare("SELECT * FROM sales WHERE id = ?");
    $stmt->execute([$saleId]);
    $sale = $stmt->fetch();
    
    $stmt = $pdo->prepare("SELECT * FROM sale_items WHERE sale_id = ?");
    $stmt->execute([$saleId]);
    $items = $stmt->fetchAll();
    
    echo "<h1>Ticket de Venta</h1>";
    echo "<p>Fecha: " . $sale['sale_date'] . "</p>";
    echo "<ul>";
    foreach ($items as $item) {
        $stmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
        $stmt->execute([$item['product_id']]);
        $productName = $stmt->fetchColumn();
        
        echo "<li>$productName - Cantidad: " . $item['quantity'] . " - Precio: $" . $item['price'] . "</li>";
    }
    echo "</ul>";
}
?>

