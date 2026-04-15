<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Blog Espacial</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@300;400;500;600;700&family=Orbitron:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Fondo animado de estrellas -->
    <div class="stars-container">
        <div class="stars"></div>
        <div class="stars"></div>
        <div class="stars"></div>
    </div>
    <div class="nebula"></div>
    
    <!-- Decoraciones espaciales -->
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
                    <h1 class="form-title">Crear Cuenta</h1>
                    <p class="form-subtitle">Unete a nuestra comunidad espacial</p>
                </div>
                
                <!-- Contenedor de alertas (para JS) -->
                <div id="alertContainer">
                    <!-- Mensajes PHP (solo se muestran si hay error de servidor previo) -->
                    <?php if(isset($_SESSION['error'])): ?>
                        <div class="alert alert-error">
                            <span class="alert-icon">&#9888;</span>
                            <span><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Formulario con ID para JS -->
                <!-- Nota: Eliminamos 'action' y 'method' porque JS controlará el envío -->
                <form id="registroForm" class="form" novalidate>
                    <div class="form-group fade-in delay-1">
                        <label for="nombre" class="form-label">Nombre Completo</label>
                        <input 
                            type="text" 
                            id="nombre" 
                            name="nombre" 
                            class="form-input" 
                            placeholder="Tu nombre completo"
                            required
                            autocomplete="name"
                        >
                        <span class="input-glow"></span>
                    </div>
                    
                    <div class="form-group fade-in delay-2">
                        <label for="usuario" class="form-label">Nombre de Usuario</label>
                        <input 
                            type="text" 
                            id="usuario" 
                            name="usuario" 
                            class="form-input" 
                            placeholder="Elige un usuario unico"
                            required
                            autocomplete="username"
                        >
                        <span class="input-glow"></span>
                    </div>
                    
                    <div class="form-group fade-in delay-3">
                        <label for="correo" class="form-label">Correo Electronico</label>
                        <input 
                            type="email" 
                            id="correo" 
                            name="correo" 
                            class="form-input" 
                            placeholder="tu@correo.com"
                            required
                            autocomplete="email"
                        >
                        <span class="input-glow"></span>
                    </div>
                    
                    <div class="form-group fade-in delay-4">
                        <label for="contrasena" class="form-label">Contrasena</label>
                        <input 
                            type="password" 
                            id="contrasena" 
                            name="contrasena" 
                            class="form-input" 
                            placeholder="Minimo 6 caracteres"
                            required
                            autocomplete="new-password"
                        >
                        <span class="input-glow"></span>
                    </div>
                    
                    <div class="form-group fade-in delay-5">
                        <!-- Botón con ID para controlar estado -->
                        <button type="submit" id="btnRegistrar" class="btn btn-primary">
                            <span class="btn-text">Registrarse</span>
                        </button>
                    </div>
                </form>
                
                <div class="form-footer fade-in delay-5">
                    <p class="form-footer-text">
                        Ya tienes una cuenta? 
                        <a href="login.php" class="form-footer-link">Inicia sesion aqui</a>
                    </p>
                </div>
            </div>
        </div>
    </main>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registroForm');
            const alertContainer = document.getElementById('alertContainer');
            const btnRegistrar = document.getElementById('btnRegistrar');
            
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
                    btnRegistrar.classList.add('btn-loading');
                    btnRegistrar.innerHTML = '<span class="spinner"></span><span>Procesando...</span>';
                    btnRegistrar.disabled = true;
                } else {
                    btnRegistrar.classList.remove('btn-loading');
                    btnRegistrar.innerHTML = '<span class="btn-text">Registrarse</span>';
                    btnRegistrar.disabled = false;
                }
            }
            
            function validateField(field) {
                const value = field.value.trim();
                let isValid = true;
                
                field.classList.remove('invalid');
                
                switch(field.name) {
                    case 'nombre':
                        if (!value || value.length < 3) isValid = false;
                        break;
                    case 'usuario':
                        if (!value || !/^[a-zA-Z0-9_]{3,50}$/.test(value)) isValid = false;
                        break;
                    case 'correo':
                        if (!value || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) isValid = false;
                        break;
                    case 'contrasena':
                        if (!value || value.length < 6) isValid = false;
                        break;
                }
                
                if (!isValid) {
                    field.classList.add('invalid');
                    field.style.borderColor = '#ff4757';
                } else {
                    field.style.borderColor = '';
                }
                
                return isValid;
            }

            // 3. VALIDACIÓN EN TIEMPO REAL
            const inputs = form.querySelectorAll('.form-input');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this);
                });
                
                input.addEventListener('input', function() {
                    if (this.classList.contains('invalid')) {
                        validateField(this);
                    }
                });
            });

            // 4. ENVÍO DEL FORMULARIO (FETCH)
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                alertContainer.innerHTML = '';
                
                let isValid = true;
                inputs.forEach(input => {
                    if (!validateField(input)) isValid = false;
                });
                
                if (!isValid) {
                    showAlert('Por favor completa todos los campos correctamente', 'error');
                    return;
                }
                
                setLoading(true);
                const formData = new FormData(form);
                
                try {
                    const response = await fetch('php/registro_procesar.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        showAlert(data.message, 'success');
                        setTimeout(() => {
                            // Asumimos que login.php también estará en la raíz
                            window.location.href = 'login.php'; 
                        }, 2000);
                    } else {
                        showAlert(data.message || 'Ocurrió un error', 'error');
                    }
                    
                } catch (error) {
                    console.error('Error:', error);
                    showAlert('Error de conexion. Intenta nuevamente.', 'error');
                } finally {
                    setLoading(false);
                }
            });
        });
    </script>
</body>
</html>