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

    echo "<!DOCTYPE html>";
    echo "<html lang='es'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>Ticket de Venta</title>";
    echo "<link rel='stylesheet' href='style.css'>";
    echo "</head>";
    echo "<body>";
    echo "<div class='ticket'>";
    echo "<h1>Ticket de Venta</h1>";
    echo "<p>Fecha: " . $sale['sale_date'] . "</p>";
    echo "<ul>";

    $total = 0;
    foreach ($items as $item) {
        if ($item['quantity'] > 0) {
            $stmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
            $stmt->execute([$item['product_id']]);
            $productName = $stmt->fetchColumn();

            $itemTotal = $item['quantity'] * $item['price'];
            $total += $itemTotal;

            echo "<li><span>$productName - Cantidad: " . $item['quantity'] . "</span><span>Precio: $" . $item['price'] . " - Total: $" . $itemTotal . "</span></li>";
        }
    }
    echo "</ul>";
    echo "<p class='total'>Total de la Compra: $" . $total . "</p>";
    echo "<button onclick='history.back()'>Volver</button>";
    echo "</div>";
    echo "</body>";
    echo "</html>";
} else {
    echo "ID de venta no proporcionado.";
}
?>
