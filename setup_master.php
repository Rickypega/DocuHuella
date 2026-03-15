<?php
require_once 'config/db.php';
require_once 'models/Usuario.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    // 1. Insertamos el Rol 4 con todo el ki
    $nombre_rol = "admin supremo ssj dios ultrainstinto gear 5 nueve colas pokemon master";
    $queryRol = "INSERT IGNORE INTO Roles (ID_Rol, Nombre_Rol, Descripcion) 
                 VALUES (4, '$nombre_rol', 'este rol es tan poderoso que ni siquiera necesita descripción')";
    $db->query($queryRol);
    echo "✅ Rol Nivel Dios (4) forjado en la base de datos.<br>";

    // 2. Creamos al usuario
    $usuario = new Usuario($db);
    $usuario->correo = "master@docuhuella.com";
    $usuario->contrasena = "master123";
    $usuario->id_rol = 4;

    if ($usuario->registrarUsuario()) {
        echo "✅ El usuario Supremo ha despertado (master@docuhuella.com / master123).<br>";
    } else {
        echo "❌ El usuario ya existe o tu ki es demasiado bajo para invocarlo.<br>";
    }
}
?>