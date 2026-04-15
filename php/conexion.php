<?php
 $host = "localhost";
 $dbname = "blog_personal";
 $user = "root";
 $pass = "";

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si falla la conexión, devolvemos JSON para que el JS lo entienda
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la Base de Datos']);
    exit;
}
?>