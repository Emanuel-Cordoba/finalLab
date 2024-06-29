<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
        <h1>Bienvenido a FarmaTuc</h1>
        <?php if ($role == 'admin'): ?>
            <a href="create_product.php" class="btn">Crear Producto</a><br>
            <a href="manage_products.php" class="btn">Gestionar Productos</a><br>
        <?php endif; ?>
        <a href="create_sale.php" class="btn">Crear Venta</a><br>
        <a href="view_sales.php" class="btn">Historial de Ventas</a><br>
        <a href="logout.php" class="btn logout-btn">Cerrar Sesión</a>
    </div>
</body>
</html>
