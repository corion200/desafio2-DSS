<?php
session_start();
require_once 'conexion.php';

// Indicamos que la respuesta será JSON
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $usuario_input = trim($_POST['usuario_email'] ?? '');
    $contrasena_input = $_POST['contrasena'] ?? '';

    if (empty($usuario_input) || empty($contrasena_input)) {
        echo json_encode(['success' => false, 'message' => "Todos los campos son obligatorios."]);
        exit;
    }

    try {
        // Buscar usuario por usuario O correo
        $sql = "SELECT id, nombre, usuario, correo, contrasena, rol FROM usuarios WHERE usuario = :user OR correo = :mail LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':user' => $usuario_input, ':mail' => $usuario_input]);
        
        $usuario_db = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar contraseña
        if ($usuario_db && password_verify($contrasena_input, $usuario_db['contrasena'])) {
            
            // Credenciales correctas: Guardar sesión
            $_SESSION['user_id'] = $usuario_db['id'];
            $_SESSION['user_nombre'] = $usuario_db['nombre'];
            $_SESSION['user_usuario'] = $usuario_db['usuario'];
            $_SESSION['user_rol'] = $usuario_db['rol'];
            
            // Opcional: Actualizar último login (si ya agregaste la columna en la BD)
            // $update_sql = "UPDATE usuarios SET ultimo_login = NOW() WHERE id = :id";
            // $stmt_update = $conexion->prepare($update_sql);
            // $stmt_update->execute([':id' => $usuario_db['id']]);

            // Devolvemos JSON de éxito y la URL a donde ir
            echo json_encode([
                'success' => true, 
                'message' => '¡Bienvenido, ' . $usuario_db['nombre'] . '!',
                'redirect' => 'dashboard.php'
            ]);
            exit;

        } else {
            // Credenciales incorrectas
            echo json_encode(['success' => false, 'message' => "Usuario o contraseña incorrectos."]);
            exit;
        }

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Error del servidor."]);
        exit;
    }

} else {
    echo json_encode(['success' => false, 'message' => "Método no permitido."]);
    exit;
}
?>