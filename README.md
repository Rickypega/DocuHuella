DocuHuella - Sistema de Gestión Veterinaria
DocuHuella es una solución tecnológica diseñada para la gestión integral de expedientes clínicos veterinarios. Este proyecto ha sido desarrollado bajo la arquitectura MVC (Modelo-Vista-Controlador), lo que permite una separación clara entre la lógica de datos, las interfaces de usuario y el control de procesos.

🚀 Características del Sistema
Control de Acceso Seguro: Sistema de login con roles diferenciados para Administradores, Veterinarios y Cuidadores.

Expediente Clínico Digital: Registro completo de consultas que incluye motivo, diagnóstico presuntivo y tratamiento recomendado.

Gestión de Pacientes: Base de datos detallada de mascotas con seguimiento de peso, rasgos físicos y estado de esterilización.

Perfiles Profesionales: Registro de veterinarios con validación de credenciales como Execuatur y Colegiatura (COLVET).

Panel Administrativo: Herramientas para la gestión de usuarios y generación de reportes estadísticos mensuales.

🛠️ Stack Tecnológico
Lenguaje de Servidor: PHP

Gestor de Base de Datos: MySQL

Frontend: HTML5, CSS3 (Bootstrap) y JavaScript

Entorno de Desarrollo: XAMPP / Visual Studio Code

Despliegue: 000webhost (Hosting gratuito)

📂 Estructura de Directorios
/models: Contiene las clases de PHP que interactúan con la base de datos (Usuario, Mascota, Veterinario, etc.).

/views: Archivos de interfaz de usuario, formularios y vistas de reportes.

/controllers: Lógica intermedia que procesa las solicitudes del usuario.

/config: Configuración de la conexión a la base de datos mediante PDO.

/public: Carpeta de recursos estáticos como hojas de estilo (CSS), scripts (JS) y el logo oficial.

👥 Participantes

Ricardo Peña García (Ricky)

Eddual Rafael Corniel

============================================================================================================
Notas de Instalación
Clonar el repositorio en la carpeta htdocs de XAMPP.

Importar el archivo SQL (próximamente disponible) en phpMyAdmin.

Configurar las credenciales de acceso en el archivo config/db.php.
