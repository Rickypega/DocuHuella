<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DocuHuella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Paleta de colores extraída del mockup (image_7.png) */
        :root {
            --dh-beige: #EBDCCB; /* Fondo izquierdo */
            --dh-navy: #1A2D40;  /* Fondo derecho, texto botones, etc. */
            --dh-light-gray: #F8F9FA; /* Fondos de input, texto secundario */
            --dh-white: #FFFFFF; /* Botón principal, texto claro */
        }

        body, html {
            height: 100%;
            margin: 0;
            overflow: hidden; /* Evita scroll en pantalla completa */
        }

        .login-container {
            height: 100vh;
        }

        /* Sección Izquierda: Beige con Logo */
        .left-panel {
            background-color: var(--dh-beige);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-logo {
            max-width: 80%; /* Ajuste responsivo */
            height: auto;
        }

        /* Sección Derecha: Azul Oscuro con Formulario */
        .right-panel {
            background-color: var(--dh-navy);
            color: var(--dh-white);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .form-content {
            width: 100%;
            max-width: 350px; /* Ancho cómodo del formulario */
        }

        /* Estilos del Formulario */
        .form-label {
            font-weight: 500;
        }

        .form-control {
            background-color: var(--dh-light-gray);
            border-radius: 20px; /* Borde sutilmente redondeado como el mockup */
            padding: 10px 15px;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
            border-color: #ddd;
        }

        /* Botón "Entrar" estilizado */
        .btn-entrar {
            background-color: var(--dh-white);
            color: var(--dh-navy);
            font-weight: bold;
            border-radius: 20px;
            padding: 10px;
            width: 100%;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-entrar:hover {
            background-color: #f0f0f0;
            color: #101c29;
        }

        /* Enlace de registro claro */
        .register-link {
            color: var(--dh-white) !important;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link:hover {
            text-decoration: underline;
        }

        /* Alerta de Error translúcida contra el fondo azul */
        .dh-alert-error {
            background-color: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 10px;
        }
    </style>
</head>
<body>

    <div class="container-fluid g-0 login-container">
        <div class="row h-100 g-0">
            
            <div class="col-md-6 left-panel d-none d-md-flex">
                <img src="../public/images/DH.jpg" alt="Logo Completo DocuHuella" class="main-logo">
            </div>

            <div class="col-md-6 right-panel">
                <div class="form-content">
                    
                    <h2 class="text-center mb-5 fw-bold">Iniciar Sesión</h2>

                    <?php if (isset($_GET['error']) && $_GET['error'] == 'credenciales'): ?>
                        <div class="alert alert-danger dh-alert-error p-2 text-center" role="alert">
                            <small>Correo o contraseña incorrectos.</small>
                        </div>
                    <?php endif; ?>

                    <form action="../controllers/UsuariosController.php?action=login" method="POST">
                        
                        <div class="mb-4">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" required placeholder="admin@docuhuella.com">
                        </div>
                        
                        <div class="mb-5">
                            <label for="contrasena" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="contrasena" name="contrasena" required placeholder="••••••••">
                        </div>
                        
                        <button type="submit" class="btn-entrar text-uppercase">Entrar</button>
                    </form>
                    
                    <div class="mt-4 text-center">
                        <small class="text-white opacity-75">¿No tienes una cuenta aún?</small> <br> 
                        <a href="registro.php" class="register-link fw-bold">Registrarse</a>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>