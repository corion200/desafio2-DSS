-- --------------------------------------------------------
-- Blog Personal Espacial - Script de Base de Datos
-- --------------------------------------------------------

-- Eliminar base de datos si existe y crearla de nuevo
DROP DATABASE IF EXISTS blog_personal;
CREATE DATABASE blog_personal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blog_personal;

-- --------------------------------------------------------
-- Estructura de la tabla 'usuarios'
-- --------------------------------------------------------
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    correo VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    ultimo_login DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Estructura de la tabla 'publicaciones'
-- --------------------------------------------------------
CREATE TABLE publicaciones (
    id_post INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    informacion TEXT NOT NULL,
    id_usuario INT NOT NULL,
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_publicacion_usuario
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Datos: Usuario Administrador
-- Contraseña hasheada proporcionada por el usuario
-- --------------------------------------------------------
INSERT INTO usuarios (id, nombre, usuario, correo, contrasena, rol, ultimo_login) 
VALUES (
    1, 
    'nelson molina', 
    'corion200', 
    'cori@gmail.com', 
    '$2y$10$zqYUgeelypxljAZLVT2Nm.n4Z355.wFIM5ENbOemV7zftIbH/OlLK', 
    'admin',
    NOW()
);

-- --------------------------------------------------------
-- Datos: Publicaciones de ejemplo (Temática Espacial)
-- Asociadas al usuario con ID 1 (Admin)
-- --------------------------------------------------------
INSERT INTO publicaciones (titulo, informacion, id_usuario, fecha_publicacion) VALUES
('El Misterio de la Materia Oscura', 'Los científicos han detectado variaciones inusuales en la dispersión de la materia oscura en el sector 7G. Las sondas deep-space han enviado datos que sugieren una acumulación masiva invisible que distorsiona la luz de las estrellas de fondo.', 1, NOW() - INTERVAL 7 DAY),
('Descubrimiento de Exoplaneta Habitabl', 'El telescopio espacial "Stellar View" ha confirmado la existencia de un planeta rocoso en la zona habitable de la estrella Kepler-442b. Las condiciones atmosféricas preliminares sugieren presencia de agua líquida y temperaturas aptas para la vida.', 1, NOW() - INTERVAL 6 DAY),
('Lanzamiento Exitoso del Satélite Ar', 'Esta mañana, el vehículo de lanzamiento pesado "Titan V" despegó exitosamente desde la base espacial llevando una carga de equipos de telecomunicaciones para la colonia marciana. Todo el proceso fue transmitido en vivo a nivel global.', 1, NOW() - INTERVAL 5 DAY),
('Amenaza de Tormenta Solar', 'El observatorio solar ha emitido una alerta de nivel 3 debido a una eyección de masa coronal (CME) dirigida hacia la Tierra. Se espera que las comunicaciones de radio se vean afectadas en las próximas 48 horas.', 1, NOW() - INTERVAL 4 DAY),
('El Cinturón de Kuiper: Frontera Fina', 'Nuevas regulaciones espaciales proponen la minería controlada de asteroides en el Cinturón de Kuiper. Las empresas privadas ya están compitiendo por los primeros permisos de extracción de metales raros.', 1, NOW() - INTERVAL 3 DAY),
('Vida en la Estación Internaciona', 'El experimento "Bio-Lab" a bordo de la estación espacial internacional ha logrado cultivar plantas resistentes a la radiación cósmica. Este es un paso crucial para futuros viajes de larga distancia a Marte y más allá.', 1, NOW() - INTERVAL 2 DAY),
('Agujeros Negros: Fábricas de Tiempo', 'Una nueva teoría propuesta por el Instituto de Astrofísica sugiere que los agujeros negros supermasivos no solo consumen materia, sino que podrían actuar como estabilizadores gravitacionales para la formación de nuevas galaxias.', 1, NOW() - INTERVAL 1 DAY),
('Misión de Mantenimiento al Telescop', 'La tripulación de la nave "Apollo XI-X" ha completado la caminata espacial para reemplazar los paneles solares del telescopio orbital. Se espera un incremento del 20% en la eficiencia energética de los instrumentos.', 1, NOW());