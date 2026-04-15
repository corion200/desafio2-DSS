<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Blog Espacial</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@300;400;500;600;700&family=Orbitron:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Fondo animado -->
    <div class="stars-container">
        <div class="stars"></div>
        <div class="stars"></div>
        <div class="stars"></div>
    </div>
    <div class="nebula"></div>
    
    <!-- Decoraciones -->
    <div class="space-decoration planet-1"></div>
    <div class="space-decoration planet-2"></div>
    
    <!-- Navegación -->
    <header class="nav-header">
        <nav class="nav-content">
            <a href="index.php" class="logo">Stellar Blog</a>
            <div class="nav-links">
                <a href="login.php" class="nav-link">Iniciar Sesion</a>
                <a href="registro.php" class="nav-link">Registro</a>
            </div>
        </nav>
    </header>
    
    <!-- Contenido principal -->
    <main class="main-container">
        <div class="card-container fade-in">
            <div class="form-card">
                <div class="form-header">
                    <h1 class="form-title">Iniciar Sesion</h1>
                    <p class="form-subtitle">Accede a tu cuenta espacial</p>
                </div>
                
                <!-- Contenedor de alertas -->
                <div id="alertContainer">
                    <?php if(isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <span class="alert-icon">&#10003;</span>
                            <span><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Formulario -->
                <form id="loginForm" class="form" novalidate>
                    <div class="form-group fade-in delay-1">
                        <label for="usuario_email" class="form-label">Usuario o Correo</label>
                        <input 
                            type="text" 
                            id="usuario_email" 
                            name="usuario_email" 
                            class="form-input" 
                            placeholder="Ingresa tu usuario o correo"
                            required
                        >
                        <span class="input-glow"></span>
                    </div>
                    
                    <div class="form-group fade-in delay-2">
                        <label for="contrasena" class="form-label">Contrasena</label>
                        <input 
                            type="password" 
                            id="contrasena" 
                            name="contrasena" 
                            class="form-input" 
                            placeholder="Tu contrasena"
                            required
                        >
                        <span class="input-glow"></span>
                    </div>
                    
                    <div class="form-group fade-in delay-3">
                        <button type="submit" id="btnLogin" class="btn btn-primary">
                            <span class="btn-text">Ingresar</span>
                        </button>
                    </div>
                </form>
                
                <div class="form-footer fade-in delay-4">
                    <p class="form-footer-text">
                        No tienes una cuenta? 
                        <a href="registro.php" class="form-footer-link">Registrate aqui</a>
                    </p>
                </div>
            </div>
        </div>
    </main>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const alertContainer = document.getElementById('alertContainer');
            const btnLogin = document.getElementById('btnLogin');
            
            // 1. EVITAR ENVÍO CON ENTER
            form.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                }
            });

            // 2. FUNCIONES AUXILIARES
            function showAlert(message, type) {
                alertContainer.innerHTML = `
                    <div class="alert alert-${type}">
                        <span class="alert-icon">${type === 'error' ? '&#9888;' : '&#10003;'}</span>
                        <span>${message}</span>
                    </div>
                `;
            }
            
            function setLoading(loading) {
                if (loading) {
                    btnLogin.classList.add('btn-loading');
                    btnLogin.innerHTML = '<span class="spinner"></span><span>Verificando...</span>';
                    btnLogin.disabled = true;
                } else {
                    btnLogin.classList.remove('btn-loading');
                    btnLogin.innerHTML = '<span class="btn-text">Ingresar</span>';
                    btnLogin.disabled = false;
                }
            }

            // 3. ENVÍO DEL FORMULARIO
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                alertContainer.innerHTML = '';
                
                const usuario_email = document.getElementById('usuario_email').value.trim();
                const contrasena = document.getElementById('contrasena').value;
                
                if (!usuario_email || !contrasena) {
                    showAlert('Por favor completa todos los campos.', 'error');
                    return;
                }
                
                setLoading(true);
                
                const formData = new FormData(form);
                
                try {
                    const response = await fetch('php/login_procesar.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    // Verificamos si la respuesta HTTP es correcta (200-299)
                    if (!response.ok) {
                        throw new Error('Error de red o servidor no disponible.');
                    }

                    // Intentamos leer el JSON
                    const data = await response.json();
                    
                    if (data.success) {
                        showAlert(data.message, 'success');
                        setTimeout(() => {
                            // Usamos la URL que nos devuelve el PHP o dashboard.php por defecto
                            window.location.href = data.redirect || 'dashboard.php';
                        }, 1500);
                    } else {
                        showAlert(data.message, 'error');
                        setLoading(false); // Restauramos el botón si hay error de datos
                    }
                    
                } catch (error) {
                    console.error('Error:', error);
                    showAlert('Error de conexión o respuesta inválida del servidor.', 'error');
                    setLoading(false);
                }
                // Nota: No ponemos 'finally' aquí porque si es exitoso, preferimos dejar el botón en "cargando" hasta que cambie la página.
            });
        });
    </script>
</body>
</html>