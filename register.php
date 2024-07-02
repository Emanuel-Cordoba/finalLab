<?php
session_start();

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = '127.0.0.1';
$db = 'pharmacy_db';
$email ='';
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
    $pdo = new PDO($dsn, $user, $pass , $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = htmlspecialchars($_POST['email']);
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = htmlspecialchars($_POST['role']);

    $stmt = $pdo->prepare("INSERT INTO users (email ,username, password, role) VALUES (?,?, ?, ?)");
    $stmt->execute([ $email ,$username, $password, $role]);

    $mail = new PHPMailer(true);

    try {
       
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'emanuelcordoba002@gmail.com'; 
        $mail->Password = 'nfvrxxfjebzluqqe'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        
        $mail->setFrom('emanuelcordoba002@gmail.com', 'Emanuel');
        $mail->addAddress($email, $username);

        $mail->isHTML(true);
        $mail->Subject = 'Confirmación de Registro';
        $mail->Body    = "Hola $username,<br><br>Gracias por registrarte. Por favor, haz clic en el siguiente enlace para confirmar tu registro:<br><br>";
        $mail->Body   .= "<a href='http://localhost/farmacia/confirm.php?email=$email'>Confirmar Registro</a>";

        $mail->send();
        echo 'Usuario registrado exitosamente. Por favor, revisa tu correo para confirmar tu registro.';
    } catch (Exception $e) {
        echo "Usuario registrado exitosamente, pero no se pudo enviar el correo de confirmación. Error: {$mail->ErrorInfo}";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Registro</h1>
        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Rol:</label>
                <input type="text" id="role" name="role" required>
            </div>
            <button type="submit" class="btn">Registrar</button>
        </form>
    </div>
</body>
</html>
