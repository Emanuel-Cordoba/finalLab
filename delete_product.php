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

$id = $_POST['id'] ?? null;

if ($id) {
    // Revisar si el producto ha sido vendido
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM sale_items WHERE product_id = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Redirigir con un mensaje de error si el producto ha sido vendido
        header("Location: manage_products.php?error=product_sold");
        exit;
    } else {
        try {
            // Eliminar el producto si no ha sido vendido
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);

            header("Location: manage_products.php?success=product_deleted");
            exit;
        } catch (PDOException $e) {
            $error_message = "Error al eliminar el producto: " . $e->getMessage();
        }
    }
} else {
    header("Location: manage_products.php?error=no_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Producto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Eliminar Producto</h1>
        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <button class="back-button" onclick="history.back()">Volver</button>
    </div>
</body>
</html>
