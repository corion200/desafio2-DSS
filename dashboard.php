<?php
// 1. Iniciar sesión y verificar acceso
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'php/conexion.php';
 $es_admin = ($_SESSION['user_rol'] === 'admin');
 $user_name = $_SESSION['user_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Stellar Blog</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@300;400;500;600;700&family=Orbitron:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="style.css">
    
    <style>
        /* --- ESTILOS ESPECÍFICOS DEL DASHBOARD --- */
        
        /* Grid de publicaciones */
        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            padding: 2rem 0;
            position: relative;
            z-index: 2;
        }

        /* Tarjeta de publicación */
        .post-card {
            background: var(--bg-card);
            border: 1px solid var(--metallic-dark);
            border-radius: 12px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(5px);
            opacity: 0; /* Oculto inicialmente para la animación */
            transform: translateX(-100px); /* Inicia fuera de pantalla */
            transition: all 0.6s cubic-bezier(0.25, 1, 0.5, 1);
        }

        /* Animación de entrada tipo "cometa" */
        .post-card.visible {
            opacity: 1;
            transform: translateX(0);
        }

        /* Estela de la cometa (pseudo-elemento) */
        .post-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: skewX(-20deg);
            transition: right 0.5s ease;
            pointer-events: none;
        }

        .post-card:hover::before {
            right: 150%; /* Mueve el brillo al pasar el mouse */
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 0.5rem;
        }

        .post-author {
            font-family: var(--font-display);
            font-size: 0.9rem;
            color: var(--metallic-light);
        }

        .post-date {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .post-title {
            font-family: var(--font-display);
            font-size: 1.2rem;
            color: var(--neon-white);
            margin-bottom: 0.5rem;
        }

        .post-body {
            font-size: 0.95rem;
            color: var(--text-primary);
            line-height: 1.5;
        }

        /* Botón eliminar (Solo Admin) */
        .btn-delete-post {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 71, 87, 0.2);
            border: 1px solid var(--error);
            color: var(--error);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            opacity: 0;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .post-card:hover .btn-delete-post {
            opacity: 1;
        }

        .btn-delete-post:hover {
            background: var(--error);
            color: white;
        }

        /* Botón flotante para crear */
        .btn-float {
            position: fixed;
            bottom: 40px;
            right: 40px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #2a2a3a 0%, #1a1a2a 100%);
            border: 2px solid var(--neon-white);
            border-radius: 50%;
            color: var(--neon-white);
            font-size: 2rem;
            cursor: pointer;
            z-index: 100;
            box-shadow: 0 0 20px rgba(255,255,255,0.2);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-float:hover {
            transform: scale(1.1) rotate(90deg);
            box-shadow: 0 0 30px rgba(255,255,255,0.4);
        }

        /* Modal (Ventana Emergente) */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(5px);
            z-index: 200;
            display: none; /* Oculto por defecto */
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background: var(--bg-secondary);
            border: 1px solid var(--metallic-dark);
            border-radius: 12px;
            padding: 2rem;
            width: 90%;
            max-width: 500px;
            transform: scale(0.8);
            transition: transform 0.3s ease;
            position: relative;
        }

        .modal-overlay.active .modal-content {
            transform: scale(1);
        }

        .close-modal {
            position: absolute;
            top: 10px;
            right: 15px;
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        /* Ajuste para el contenedor principal en esta vista */
        .dashboard-main {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .section-title {
            font-family: var(--font-display);
            font-size: 1.5rem;
            color: var(--neon-white);
            margin-bottom: 1rem;
            text-shadow: 0 0 10px rgba(255,255,255,0.3);
            text-align: center;
            margin-top: 100px; /* Espacio para el header */
        }

    </style>
</head>
<body>
    <!-- Fondo animado -->
    <div class="stars-container">
        <div class="stars"></div>
        <div class="stars"></div>
        <div class="stars"></div>
    </div>
    <div class="nebula"></div>
    
    <!-- Navegación -->
    <header class="nav-header">
        <nav class="nav-content">
            <a href="dashboard.php" class="logo">Stellar Blog</a>
            <div class="nav-links">
                <span style="color: var(--metallic-light); margin-right: 1rem;">Hola, <?php echo htmlspecialchars($user_name); ?> (<?php echo $es_admin ? 'Admin' : 'User'; ?>)</span>
                <a href="php/logout.php" class="nav-link">Cerrar Sesion</a>
            </div>
        </nav>
    </header>
    
    <!-- Contenido principal -->
    <main class="dashboard-main">
        <h2 class="section-title">Bitácora de la Estación</h2>
        
        <!-- Contenedor de publicaciones -->
        <div id="postsContainer" class="posts-grid">
            <!-- Las publicaciones se cargarán aquí con JS -->
        </div>
    </main>

    <!-- Botón flotante para crear publicación -->
    <button class="btn-float" id="btnNewPost" title="Nueva Publicación">+</button>

    <!-- Modal para Nueva Publicación -->
    <div class="modal-overlay" id="modalNewPost">
        <div class="modal-content">
            <button class="close-modal" id="closeModal">&times;</button>
            <div class="form-header">
                <h3 class="form-title">Nueva Transmisión</h3>
            </div>
            <form id="formNewPost">
                <div class="form-group">
                    <label class="form-label">Título</label>
                    <input type="text" id="postTitulo" name="titulo" class="form-input" placeholder="Título de tu mensaje..." required>
                </div>
                <div class="form-group">
                    <label class="form-label">Información</label>
                    <textarea id="postInfo" name="informacion" class="form-input" rows="5" placeholder="¿Qué estás observando hoy?" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Enviar Transmisión</button>
            </form>
        </div>
    </div>

    <script>
        // --- LÓGICA DEL DASHBOARD ---

        const postsContainer = document.getElementById('postsContainer');
        const btnNewPost = document.getElementById('btnNewPost');
        const modalNewPost = document.getElementById('modalNewPost');
        const closeModal = document.getElementById('closeModal');
        const formNewPost = document.getElementById('formNewPost');
        const esAdmin = <?php echo $es_admin ? 'true' : 'false'; ?>;

        // 1. ABRIR/CERRAR MODAL
        btnNewPost.addEventListener('click', () => {
            modalNewPost.classList.add('active');
        });

        closeModal.addEventListener('click', () => {
            modalNewPost.classList.remove('active');
        });

        modalNewPost.addEventListener('click', (e) => {
            if (e.target === modalNewPost) {
                modalNewPost.classList.remove('active');
            }
        });

        // 2. FUNCIÓN PARA CARGAR PUBLICACIONES
        async function cargarPublicaciones() {
            try {
                const response = await fetch('php/user_actions.php?action=getPosts');
                const data = await response.json();
                
                if (data.success && data.posts) {
                    renderPosts(data.posts);
                    observarElementos(); // Activar animación de entrada
                } else {
                    postsContainer.innerHTML = '<p style="text-align:center; color:var(--text-muted);">No hay transmisiones aún. ¡Sé el primero!</p>';
                }
            } catch (error) {
                console.error("Error cargando posts:", error);
            }
        }

        // 3. RENDERIZAR POSTS EN HTML
        function renderPosts(posts) {
            postsContainer.innerHTML = '';
            
            posts.forEach(post => {
                const card = document.createElement('div');
                card.classList.add('post-card');
                
                // Formatear fecha
                const fecha = new Date(post.fecha_publicacion).toLocaleDateString('es-ES', {
                    year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
                });

                let adminBtn = '';
                if (esAdmin) {
                    adminBtn = `<button class="btn-delete-post" onclick="eliminarPost(${post.id_post})" title="Eliminar">X</button>`;
                }

                card.innerHTML = `
                    ${adminBtn}
                    <div class="post-header">
                        <span class="post-author">${post.autor_nombre}</span>
                        <span class="post-date">${fecha}</span>
                    </div>
                    <h3 class="post-title">${post.titulo}</h3>
                    <p class="post-body">${post.informacion}</p>
                `;
                
                postsContainer.appendChild(card);
            });
        }

        // 4. INTERSECTION OBSERVER (Animación Cometa al hacer scroll)
        function observarElementos() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.post-card').forEach(card => {
                observer.observe(card);
            });
        }

        // 5. CREAR NUEVA PUBLICACIÓN (FORMULARIO)
        formNewPost.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(formNewPost);
            
            try {
                const response = await fetch('php/user_actions.php?action=createPost', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    modalNewPost.classList.remove('active');
                    formNewPost.reset();
                    cargarPublicaciones(); // Recargar lista
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert("Error al enviar la transmisión.");
            }
        });

        // 6. ELIMINAR POST (SOLO ADMIN)
        async function eliminarPost(id) {
            if (!confirm("¿Seguro que deseas eliminar esta transmisión?")) return;

            try {
                const formData = new FormData();
                formData.append('id_post', id);

                const response = await fetch('php/admin_actions.php?action=deletePost', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    cargarPublicaciones();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert("Error al eliminar.");
            }
        }

        // Inicializar
        cargarPublicaciones();
    </script>
</body>
</html>