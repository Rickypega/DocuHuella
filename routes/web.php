<?php
// routes/web.php

return [
    ''              => ['controller' => 'UsuariosController', 'method' => 'index'],      // Landing Page
    'login'         => ['controller' => 'UsuariosController', 'method' => 'showLogin'],  // Formulario login
    'registro'      => ['controller' => 'UsuariosController', 'method' => 'showRegistro'], // Formulario registro
    'auth'          => ['controller' => 'UsuariosController', 'method' => 'login'],      // Procesar login
    'logout'        => ['controller' => 'UsuariosController', 'method' => 'logout'],     // Cerrar sesión
    'privacidad'    => ['controller' => 'UsuariosController', 'method' => 'privacidad'], // Privacidad
    'terminos'      => ['controller' => 'UsuariosController', 'method' => 'terminos'],   // Terminos

    'superadmin/dashboard' => ['controller' => 'superadmin/DashboardController', 'method' => 'index'], // Dashboard SuperAdmin
    'admin/dashboard'       => ['controller' => 'admin/DashboardController', 'method' => 'ver'],     // Dashboard Administrador
    'veterinario/dashboard' => ['controller' => 'veterinario/VeterinarioController', 'method' => 'index'], // Dashboard Veterinario
    'cuidador/dashboard'    => ['controller' => 'cuidador/CuidadorController', 'method' => 'index'],    // Dashboard Cuidador
    
    'perfil/actualizar'     => ['controller' => 'PerfilController', 'method' => 'actualizar'], // Controlador global de Pefil
];