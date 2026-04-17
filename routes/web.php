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
    'contactos'     => ['controller' => 'UsuariosController', 'method' => 'contactos'],  // Contactos

    'superadmin/dashboard' => ['controller' => 'superadmin/DashboardController', 'method' => 'index'], // Dashboard SuperAdmin
    'superadmin/catalogos' => ['controller' => 'superadmin/CatalogosController', 'method' => 'index'], // Gestión de Catálogos
    'superadmin/catalogos/guardar' => ['controller' => 'superadmin/CatalogosController', 'method' => 'guardar'], // Guardar Catálogo
    'superadmin/catalogos/eliminar' => ['controller' => 'superadmin/CatalogosController', 'method' => 'eliminar'], // Eliminar Catálogo
    'admin/dashboard'       => ['controller' => 'admin/DashboardController', 'method' => 'ver'],     // Dashboard Administrador
    'veterinario/dashboard' => ['controller' => 'veterinario/VeterinarioController', 'method' => 'index'], // Dashboard Veterinario
    'veterinario/pacientes' => ['controller' => 'veterinario/VeterinarioController', 'method' => 'pacientes'], // Gestión de Pacientes
    'veterinario/paciente/ver' => ['controller' => 'veterinario/VeterinarioController', 'method' => 'pacienteVer'], // Ver Perfil Mascota
    'veterinario/consultas' => ['controller' => 'veterinario/VeterinarioController', 'method' => 'consultas'], // Consultas Médicas
    'veterinario/consulta/registrar' => ['controller' => 'veterinario/VeterinarioController', 'method' => 'registrarConsulta'], // Registrar Consulta
    'veterinario/consulta/detalle' => ['controller' => 'veterinario/VeterinarioController', 'method' => 'consultaDetalle'], // Detalle Consulta (API)
    'veterinario/consulta/exportar' => ['controller' => 'veterinario/VeterinarioController', 'method' => 'exportarConsulta'], // Exportar Consulta
    'veterinario/citas'     => ['controller' => 'veterinario/VeterinarioController', 'method' => 'citas'], // Gestión de Citas
    'veterinario/cita/agendar' => ['controller' => 'veterinario/VeterinarioController', 'method' => 'agendarCita'], // Agendar Cita
    'veterinario/cita/editar' => ['controller' => 'veterinario/VeterinarioController', 'method' => 'editarCita'], // Editar Cita
    'veterinario/cita/eliminar' => ['controller' => 'veterinario/VeterinarioController', 'method' => 'eliminarCita'], // Eliminar Cita
    'veterinario/buscar-cuidador' => ['controller' => 'veterinario/VeterinarioController', 'method' => 'buscarCuidadorPorCedula'], // Lookup AJAX
    'veterinario/obtener-razas' => ['controller' => 'veterinario/VeterinarioController', 'method' => 'obtenerRazas'], // Lookup Razas AJAX
    'veterinario/paciente/actualizar' => ['controller' => 'veterinario/VeterinarioController', 'method' => 'actualizarPaciente'], // Actualizar datos mascota
    'veterinario/paciente/exportar-expediente' => ['controller' => 'veterinario/VeterinarioController', 'method' => 'exportarExpediente'], // Exportar todo el historial
    'veterinario/vacunas' => ['controller' => 'veterinario/VeterinarioController', 'method' => 'vacunas'], // Historial Vacunas
    'veterinario/vacuna/registrar' => ['controller' => 'veterinario/VeterinarioController', 'method' => 'registrarVacunacion'], // Registrar Vacuna
    'veterinario/vacuna/detalle' => ['controller' => 'veterinario/VeterinarioController', 'method' => 'vacunaDetalle'], // Detalle Vacuna (API)
    'cuidador/dashboard'    => ['controller' => 'cuidador/CuidadorController', 'method' => 'index'],    // Dashboard Cuidador
    'cuidador/mis-mascotas' => ['controller' => 'cuidador/MascotaController',   'method' => 'index'],    // Lista de Mascotas (Grid)
    'cuidador/mascota/ver'  => ['controller' => 'cuidador/MascotaController',   'method' => 'ver'],      // Detalle de Mascota
    'cuidador/mascota/registrar' => ['controller' => 'cuidador/MascotaController', 'method' => 'registrar'], // Registro de mascota (AJAX)
    'cuidador/mascota/actualizar-foto' => ['controller' => 'cuidador/MascotaController', 'method' => 'actualizarFoto'], // Actualizar foto (AJAX)
    'cuidador/mascota/consulta-detalle' => ['controller' => 'cuidador/MascotaController', 'method' => 'consultaDetalle'], // Detalle Consulta (AJAX)
    'cuidador/mascota/vacuna-detalle' => ['controller' => 'cuidador/MascotaController', 'method' => 'vacunaDetalle'], // Detalle Vacuna (AJAX)
    'cuidador/mascota/exportar-expediente' => ['controller' => 'cuidador/MascotaController', 'method' => 'exportarExpediente'], // Exportar PDF
    
    'perfil/actualizar'     => ['controller' => 'PerfilController', 'method' => 'actualizar'], // Controlador global de Pefil
    'notas/api'             => ['controller' => 'NotasController',  'method' => 'handle'],     // API de Notas Personales
];