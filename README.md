============================================================================

DocuHuella - Sistema de Gestión Veterinaria

============================================================================

DocuHuella es una solución tecnológica diseñada para la gestión integral de expedientes clínicos veterinarios. Este proyecto ha sido desarrollado bajo la arquitectura MVC (Modelo-Vista-Controlador), lo que permite una separación clara entre la lógica de datos, las interfaces de usuario y el control de procesos.

============================================================================

🚀 Características del Sistema

============================================================================
Control de Acceso Seguro: Sistema de login con roles diferenciados para Administradores, Veterinarios y Cuidadores.

Expediente Clínico Digital: Registro completo de consultas que incluye motivo, diagnóstico presuntivo y tratamiento recomendado.

Gestión de Pacientes: Base de datos detallada de mascotas con seguimiento de peso, rasgos físicos y estado de esterilización.

Perfiles Profesionales: Registro de veterinarios con validación de credenciales como Execuatur y Colegiatura (COLVET).

Panel Administrativo: Herramientas para la gestión de usuarios y generación de reportes estadísticos mensuales.

============================================================================

🛠️ Stack Tecnológico

============================================================================
Lenguaje de Servidor: PHP

Gestor de Base de Datos: MySQL

Frontend: HTML5, CSS3 (Bootstrap) y JavaScript

Entorno de Desarrollo: XAMPP / Visual Studio Code

Despliegue: 000webhost (Hosting gratuito)

============================================================================

📂 Estructura de Directorios

============================================================================
/models: Contiene las clases de PHP que interactúan con la base de datos (Usuario, Mascota, Veterinario, etc.).

/views: Archivos de interfaz de usuario, formularios y vistas de reportes.

/controllers: Lógica intermedia que procesa las solicitudes del usuario.

/config: Configuración de la conexión a la base de datos mediante PDO.

/public: Carpeta de recursos estáticos como hojas de estilo (CSS), scripts (JS) y el logo oficial.

============================================================================

👥 Participantes

============================================================================

Ricardo Peña García (Ricky)

Eddual Rafael Corniel

Lerinson Samuel Volquez

============================================================================

Notas de Instalación

============================================================================
Clonar el repositorio en la carpeta htdocs de XAMPP.

Importar el archivo SQL (próximamente disponible) en phpMyAdmin.

Configurar las credenciales de acceso en el archivo config/db.php.

## 🛠️ Configuración del Entorno Local

Para que el proyecto funcione en tu computadora, sigue estos pasos:

### 1. Base de Datos
1. Abre **XAMPP** y activa Apache y MySQL.
2. Ve a [http://localhost:8080/phpmyadmin/](http://localhost:8080/phpmyadmin/) (o al puerto que tengas configurado).
3. Crea una base de datos llamada `Docuhuella`.
4. Importa el archivo `DocuhuellaDB.sql` que está en la raíz del proyecto.

### 2. Inicialización de Datos (Roles y Admin)
Una vez importada la base de datos, debes ejecutar el script de inicialización para crear los roles (Admin, Veterinario, Cuidador) y el usuario maestro:
- Entra a: `http://localhost:8080/DocuHuella/setup.php`
- Si ves los mensajes de éxito, los roles y el primer admin (`admin@docuhuella.com` / `admin123`) ya estarán listos en tu DB.

> **Nota sobre el puerto:** Si tu XAMPP da error en el puerto 80, cámbialo al **8080** en el archivo `httpd.conf` de Apache y usa las URLs mencionadas arriba.