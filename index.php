<?php
// 1. Incluimos la conexión que está dentro de la carpeta config
require_once 'config/db.php'; 

// 2. Un mensaje de prueba para ver que todo carga bien en el host
echo "<h1>¡DocuHuella en línea!</h1>";
echo "<p>Si ves esto, el servidor está leyendo tu carpeta htdocs correctamente.</p>";

// 3. Redirigir al login 
 header("Location: views/login.php"); 
?>