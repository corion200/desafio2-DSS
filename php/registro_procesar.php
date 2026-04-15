<?php
session_start();

// 1. Indicamos que la respuesta será JSON
header('Content-Type: application/json; charset=utf-8');

require_once 'conexion.php';

// 2. Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// 3. Recibir datos
 $nombre = trim($_POST['nombre'] ?? '');
 $usuario = trim($_POST['usuario'] ?? '');
 $correo = trim($_POST['correo'] ?? '');
 $contrasena = $_POST['contrasena'] ?? '';

// 4. Validaciones
 $errores = [];

if (empty($nombre) || strlen($nombre) > 100) {
    $errores[] = "El nombre es obligatorio.";
}
if (empty($usuario) || strlen($usuario) > 50) {
    $errores[] = "El usuario es obligatorio.";
}
if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "El correo no es válido.";
}
if (empty($contrasena) || strlen($contrasena) < 6) {
    $errores[] = "La contraseña debe tener al menos 6 caracteres.";
}

// Si hay errores, devolverlos en JSON
if (!empty($errores)) {
    echo json_encode(['success' => false, 'message' => implode("<br>", $errores)]);
    exit;
}

try {
    // Verificar si ya existe
    $sql_check = "SELECT id FROM usuarios WHERE usuario = :usuario OR correo = :correo";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([':usuario' => $usuario, ':correo' => $correo]);

    if ($stmt_check->fetch()) {
        echo json_encode(['success' => false, 'message' => "El usuario o correo ya están registrados."]);
        exit;
    }

    // Cifrar contraseña
    $hash_contrasena = password_hash($contrasena, PASSWORD_DEFAULT);

    // Insertar
    $sql_insert = "INSERT INTO usuarios (nombre, usuario, correo, contrasena) VALUES (:nombre, :usuario, :correo, :contrasena)";
    $stmt_insert = $conexion->prepare($sql_insert);
    
    if ($stmt_insert->execute([
        ':nombre' => $nombre,
        ':usuario' => $usuario,
        ':correo' => $correo,
        ':contrasena' => $hash_contrasena
    ])) {
        // ÉXITO: Devolvemos JSON, NO redirigimos con header()
        echo json_encode(['success' => true, 'message' => "¡Registro exitoso! Redirigiendo..."]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => "Error al guardar en la base de datos."]);
    }

} catch (PDOException $e) {
    error_log("Error BD: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => "Error del servidor."]);
}
?>