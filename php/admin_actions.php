<?php
session_start();
require_once 'conexion.php';
header('Content-Type: application/json');

// Verificar si es admin
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
    exit;
}

 $action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'deletePost':
            $id_post = $_POST['id_post'] ?? null;

            if (!$id_post) {
                echo json_encode(['success' => false, 'message' => 'ID no proporcionado.']);
                exit;
            }

            // Eliminar el post
            $sql = "DELETE FROM publicaciones WHERE id_post = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':id' => $id_post]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Publicación eliminada.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se encontró la publicación.']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción inválida.']);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de BD.']);
}
?>