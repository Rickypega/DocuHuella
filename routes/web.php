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
    'cuidador/mis-mascotas' => ['controller' => 'cuidador/MascotaController',   'method' => 'index'],    // Lista de Mascotas (Grid)
    'cuidador/mascota/ver'  => ['controller' => 'cuidador/MascotaController',   'method' => 'ver'],      // Detalle de Mascota
    'cuidador/mascota/registrar' => ['controller' => 'cuidador/MascotaController', 'method' => 'registrar'], // Registro de mascota (AJAX)
    'cuidador/mascota/actualizar-foto' => ['controller' => 'cuidador/MascotaController', 'method' => 'actualizarFoto'], // Actualizar foto (AJAX)
    
    'perfil/actualizar'     => ['controller' => 'PerfilController', 'method' => 'actualizar'], // Controlador global de Pefil
    'notas/api'             => ['controller' => 'NotasController',  'method' => 'handle'],     // API de Notas Personales
];