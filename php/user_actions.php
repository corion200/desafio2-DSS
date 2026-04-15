<?php
session_start();
require_once 'conexion.php';
header('Content-Type: application/json');

// Verificar sesión activa
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

 $action = $_GET['action'] ?? '';
 $id_user = $_SESSION['user_id'];
 $es_admin = ($_SESSION['user_rol'] === 'admin');

try {
    switch ($action) {
        case 'getPosts':
            // Si es admin, ve todos. Si es user, ve solo los suyos.
            if ($es_admin) {
                $sql = "SELECT p.*, u.nombre as autor_nombre 
                        FROM publicaciones p 
                        JOIN usuarios u ON p.id_usuario = u.id 
                        ORDER BY p.fecha_publicacion DESC";
                $stmt = $conexion->query($sql);
            } else {
                $sql = "SELECT p.*, u.nombre as autor_nombre 
                        FROM publicaciones p 
                        JOIN usuarios u ON p.id_usuario = u.id 
                        WHERE p.id_usuario = :id 
                        ORDER BY p.fecha_publicacion DESC";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([':id' => $id_user]);
            }
            
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'posts' => $posts]);
            break;

        case 'createPost':
            $titulo = trim($_POST['titulo'] ?? '');
            $info = trim($_POST['informacion'] ?? '');

            if (empty($titulo) || empty($info)) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos.']);
                exit;
            }

            $sql_insert = "INSERT INTO publicaciones (titulo, informacion, id_usuario) VALUES (:t, :i, :u)";
            $stmt = $conexion->prepare($sql_insert);
            $stmt->execute([':t' => $titulo, ':i' => $info, ':u' => $id_user]);

            echo json_encode(['success' => true, 'message' => 'Publicación creada.']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción inválida.']);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de BD: ' . $e->getMessage()]);
}
?>