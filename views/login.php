<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DocuHuella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
       
       /* Ocultar el ojo nativo de los navegadores */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }

        :root {
            --dh-beige: #EADAC1; /* Fondo izquierdo */
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
            position: relative; 
            overflow: hidden;   
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Estilos de las huellas flotantes */
        .huella-animada {
            position: absolute;
            bottom: -50px; 
            color: var(--dh-navy); 
            z-index: 1;
            animation: flotarHaciaArriba linear forwards;
        }

        /* La animación CSS */
        @keyframes flotarHaciaArriba {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 0.15; 
            }
            90% {
                opacity: 0.15;
            }
            100% {
                transform: translateY(-120vh) rotate(25deg); 
                opacity: 0;
            }
        }

        .main-logo {
            max-width: 80%; 
            height: auto;
            position: relative;
            z-index: 10;
            mix-blend-mode: darken;
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
            
            <div class="col-md-6 left-panel d-none d-md-flex" id="panel-huellas">
                <img src="../public/images/DH.jpg" alt="Logo DocuHuella" class="main-logo position-relative" style="z-index: 10;">
            </div>

            <div class="col-md-6 right-panel">
                <div class="form-content">
                    
                    <h2 class="text-center mb-5 fw-bold">Iniciar Sesión</h2>

                    <?php if (isset($_GET['error']) && $_GET['error'] == 'credenciales'): ?>
                        <div class="alert alert-danger dh-alert-error p-2 text-center" role="alert">
                            <small>Correo o contraseña incorrectos.</small>
                        </div>
                    <?php endif; ?>

                    <?php if(isset($_GET['exito']) && $_GET['exito'] == 'registrado'): ?>
                        <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                            <strong>¡Excelente! 🐾</strong> Tu cuenta ha sido creada. Ya puedes iniciar sesión.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="../controllers/UsuariosController.php?action=login" method="POST">
                        
                       <div class="mb-4">
                            <label for="correo" class="form-label fw-semibold">Correo Electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background-color: var(--dh-light-gray); border: none; border-top-left-radius: 20px; border-bottom-left-radius: 20px; padding-left: 20px;">
                                    <i class="fas fa-envelope" style="color: #1a1a1a;"></i>
                                </span>
                                
                                <input type="email" class="form-control" id="correo" name="correo" required placeholder="ejemplo@mail.com" style="background-color: var(--dh-light-gray); border: none; border-top-right-radius: 20px; border-bottom-right-radius: 20px; padding-left: 10px;">
                            </div>
                        </div>
                        
                        <div class="mb-5">
                            <label for="contrasena" class="form-label fw-semibold">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background-color: var(--dh-light-gray); border: none; border-top-left-radius: 20px; border-bottom-left-radius: 20px; padding-left: 20px;">
                                    <i class="fas fa-lock" style="color: #1a1a1a;"></i>
                                </span>
                                
                                <input type="password" class="form-control" id="pass-login" name="contrasena" required placeholder="••••••••" style="background-color: var(--dh-light-gray); border: none; border-radius: 0; padding-left: 10px;">
                                
                                <button class="btn btn-outline-secondary d-flex align-items-center justify-content-center" type="button" id="btn-ojo-login" style="border-top-right-radius: 20px; border-bottom-right-radius: 20px; background-color: var(--dh-light-gray); border: none; padding: 0 20px;">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 13 Q 12 5 19 13" />
                                        <circle cx="12" cy="14" r="2.5" />
                                        <line id="linea-login" x1="4" y1="4" x2="20" y2="20" style="display: none;" />
                                    </svg>
                                </button>
                            </div>
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
    <script>
        function crearHuella() {
            const panel = document.getElementById('panel-huellas');
            const huella = document.createElement('i');
            huella.classList.add('fas', 'fa-paw', 'huella-animada');

            // 1. Posición horizontal aleatoria (0% a 90% para que no se pegue al borde derecho)
            huella.style.left = Math.random() * 90 + '%';

            // 2. Tamaño aleatorio (entre 1rem y 3.5rem)
            const tamaño = Math.random() * 2.5 + 1;
            huella.style.fontSize = tamaño + 'rem';

            // 3. Velocidad aleatoria (entre 7 y 15 segundos en subir)
            const duracion = Math.random() * 8 + 7;
            huella.style.animationDuration = duracion + 's';

            panel.appendChild(huella);

            // 4. Eliminar la huella cuando termine la animación para no saturar la memoria
            setTimeout(() => {
                huella.remove();
            }, duracion * 1000);
        }

        // Crear una nueva huella cada 800 milisegundos
        setInterval(crearHuella, 800);

        // Lógica para mostrar/ocultar contraseña
        document.getElementById('btn-ojo-login').addEventListener('click', function() {
            const input = document.getElementById('pass-login');
            const linea = document.getElementById('linea-login');
            
            if (input.type === 'password') {
                input.type = 'text';
                linea.style.display = 'block'; // Muestra la raya diagonal
            } else {
                input.type = 'password';
                linea.style.display = 'none';  // Oculta la raya
            }
        });
    </script>
</body>
</html>