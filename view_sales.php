<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

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

// Borrar el historial de ventas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_sales'])) {
    $pdo->beginTransaction();

    try {
        $pdo->query("DELETE FROM sale_items");

        $pdo->query("DELETE FROM sales");

        $pdo->commit();

        header("Location: view_sales.php");
        exit;
    } catch (\PDOException $e) {
        $pdo->rollBack();
        throw $e;
    }
}

// Obtener todas las ventas
$stmt = $pdo->query("SELECT * FROM sales");
$sales = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Ventas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Historial de Ventas</h1>
        <table class="sales-table">
            <tr>
                <th>ID</th>
                <th>ID Usuario</th>
                <th>Fecha de Venta</th>
                <th>Ver Ticket</th>
            </tr>
            <?php foreach ($sales as $sale): ?>
                <tr>
                    <td><?php echo $sale['id']; ?></td>
                    <td><?php echo $sale['user_id']; ?></td>
                    <td><?php echo $sale['sale_date']; ?></td>
                    <td><a href="sale_details.php?sale_id=<?php echo $sale['id']; ?>" class="details-link">Ver Detalles</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <button class="back-button" onclick="history.back()">Volver</button>
        <form method="post" action="">
            <button class="delete-button" type="submit" name="delete_sales">Borrar Historial de Ventas</button>
        </form>
    </div>
</body>
</html>
