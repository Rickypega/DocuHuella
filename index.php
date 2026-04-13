<?php
// index.php
session_start();
require_once 'config/db.php';
define('APP_PATH', __DIR__);

// 1. Obtener la URL limpia
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';

// 2. Cargar el mapa de rutas
$routes = require_once 'routes/web.php';

// 3. Verificar si la ruta existe
if (array_key_exists($url, $routes)) {
    $controllerName = $routes[$url]['controller'];
    $methodName = $routes[$url]['method'];

    // Cargar el archivo del controlador
    $controllerPath = "controllers/" . str_replace("/", DIRECTORY_SEPARATOR, $controllerName) . ".php";
    
    if (file_exists($controllerPath)) {
        require_once $controllerPath;
        $controllerClass = basename($controllerName); // Obtener solo el nombre de la clase
        $controller = new $controllerClass();
        $controller->$methodName();
    } else {
        die("Error: El controlador $controllerName no existe.");
    }
} else {
    // Si la ruta no existe (404), mandamos al inicio o login
    header("Location: " . URL_BASE . "/login");
}