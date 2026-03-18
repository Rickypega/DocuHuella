<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cuidador - DocuHuella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
       
       /* Ocultar el ojo nativo de los navegadores */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }

       :root {
            --dh-beige: #EADAC1;
            --dh-navy: #1A2D40;
            --dh-light-gray: #F8F9FA;
        }

        body, html {
            height: 100%;
            margin: 0;
            background-color: var(--dh-navy);
        }

        .left-panel {
            background-color: var(--dh-navy);
            color: white;
            height: 100vh;
            overflow-y: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
        }

        .left-panel::-webkit-scrollbar { width: 8px; }
        .left-panel::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }

        .form-content {
            width: 100%;
            max-width: 600px;
        }

        .right-panel {
            background-color: var(--dh-beige);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .main-logo {
            max-width: 70%;
            position: relative;
            z-index: 10;
            mix-blend-mode: darken; 
        }

        .form-label {
            font-size: 0.9rem;
            margin-bottom: 0.2rem;
            color: rgba(255, 255, 255, 0.85);
        }

        .form-control, .form-select {
            background-color: var(--dh-light-gray);
            border-radius: 20px;
            padding: 8px 15px;
            border: none;
            font-size: 0.95rem;
        }

        .btn-registrar {
            background-color: var(--dh-beige);
            color: var(--dh-navy);
            font-weight: bold;
            border-radius: 20px;
            padding: 10px 30px;
            border: none;
            transition: 0.3s;
        }

        .btn-registrar:hover {
            background-color: white;
        }

        .btn-registrar:disabled {
            background-color: #555;
            color: #999;
            cursor: not-allowed;
        }

        .huella-animada {
            position: absolute;
            bottom: -50px;
            color: var(--dh-navy);
            z-index: 1;
            animation: flotarHaciaArriba linear forwards;
        }

        @keyframes flotarHaciaArriba {
            0% { transform: translateY(0) rotate(0deg); opacity: 0; }
            10% { opacity: 0.15; }
            90% { opacity: 0.15; }
            100% { transform: translateY(-120vh) rotate(25deg); opacity: 0; }
        }
    </style>
</head>
<body>

    <div class="container-fluid g-0">
        <div class="row g-0 h-100">
            
            <div class="col-md-7 left-panel">
                <div class="form-content">
                    
                    <div class="mb-4 text-center">
                        <h2 class="fw-bold">Registro de Cuidador</h2>
                        <p class="text-white opacity-75 small">Crea tu cuenta para gestionar el expediente de tu mascota.</p>
                    </div>

                    <?php if(isset($_GET['error'])): ?>
                        <?php if($_GET['error'] == 'correo_ya_existe'): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>¡Ups!</strong> Este correo ya está registrado en DocuHuella.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php elseif($_GET['error'] == 'pass_no_coincide'): ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>¡Cuidado!</strong> Las contraseñas no coinciden.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php elseif($_GET['error'] == 'correo_no_coincide'): ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>¡Cuidado!</strong> Los correos no coinciden.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <form action="../controllers/RegistroController.php?action=registrar_cuidador" method="POST">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nombre</label>
                                <input type="text" class="form-control" name="nombre" required 
                                       value="<?php echo isset($_SESSION['datos_temporales']['nombre']) ? htmlspecialchars($_SESSION['datos_temporales']['nombre']) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Apellido</label>
                                <input type="text" class="form-control" name="apellido" required
                                       value="<?php echo isset($_SESSION['datos_temporales']['apellido']) ? htmlspecialchars($_SESSION['datos_temporales']['apellido']) : ''; ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Cédula</label>
                                <input type="text" class="form-control" name="cedula" placeholder="000-0000000-0" required
                                       value="<?php echo isset($_SESSION['datos_temporales']['cedula']) ? htmlspecialchars($_SESSION['datos_temporales']['cedula']) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Teléfono</label>
                                <input type="tel" class="form-control" name="telefono" required
                                       value="<?php echo isset($_SESSION['datos_temporales']['telefono']) ? htmlspecialchars($_SESSION['datos_temporales']['telefono']) : ''; ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" name="fecha_nacimiento" required
                                       value="<?php echo isset($_SESSION['datos_temporales']['fecha_nacimiento']) ? htmlspecialchars($_SESSION['datos_temporales']['fecha_nacimiento']) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Sexo</label>
                                <?php $sexo_temp = isset($_SESSION['datos_temporales']['sexo']) ? $_SESSION['datos_temporales']['sexo'] : ''; ?>
                                <select class="form-select" name="sexo" required>
                                    <option value="" <?php echo ($sexo_temp == '') ? 'selected' : ''; ?> disabled>Seleccione...</option>
                                    <option value="M" <?php echo ($sexo_temp == 'M') ? 'selected' : ''; ?>>Masculino</option>
                                    <option value="F" <?php echo ($sexo_temp == 'F') ? 'selected' : ''; ?>>Femenino</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Dirección</label>
                                <input type="text" class="form-control" name="direccion" required
                                       value="<?php echo isset($_SESSION['datos_temporales']['direccion']) ? htmlspecialchars($_SESSION['datos_temporales']['direccion']) : ''; ?>">
                            </div>
                        </div>

                        <hr class="border-secondary my-4">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Correo Electrónico</label>
                                <input type="email" class="form-control" name="correo" required
                                       value="<?php echo isset($_SESSION['datos_temporales']['correo']) ? htmlspecialchars($_SESSION['datos_temporales']['correo']) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirmar Correo</label>
                                <input type="email" class="form-control" name="confirmar_correo" required
                                       value="<?php echo isset($_SESSION['datos_temporales']['confirmar_correo']) ? htmlspecialchars($_SESSION['datos_temporales']['confirmar_correo']) : ''; ?>">
                            </div>
                        </div>

                      <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="contrasena" id="pass-input" required style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                    <button class="btn btn-outline-secondary d-flex align-items-center justify-content-center" type="button" id="btn-ojo-pass" style="border-top-right-radius: 20px; border-bottom-right-radius: 20px; background-color: var(--dh-light-gray); border: none; padding: 0 15px;">
                                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 13 Q 12 5 19 13" />
                                            <circle cx="12" cy="14" r="2.5" />
                                            <line id="linea-pass" x1="4" y1="4" x2="20" y2="20" style="display: none;" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="progress mt-2" style="height: 5px; background-color: var(--dh-navy);">
                                    <div id="barra-fuerza" class="progress-bar bg-danger" role="progressbar" style="width: 0%;"></div>
                                </div>
                                <small id="texto-fuerza" class="text-white opacity-50" style="font-size: 0.8rem;">Mínimo 6 caracteres</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirmar Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="confirmar_contrasena" id="pass-confirm-input" required style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                    <button class="btn btn-outline-secondary d-flex align-items-center justify-content-center" type="button" id="btn-ojo-confirm" style="border-top-right-radius: 20px; border-bottom-right-radius: 20px; background-color: var(--dh-light-gray); border: none; padding: 0 15px;">
                                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 13 Q 12 5 19 13" />
                                            <circle cx="12" cy="14" r="2.5" />
                                            <line id="linea-confirm" x1="4" y1="4" x2="20" y2="20" style="display: none;" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="login.php" class="text-white text-decoration-none opacity-75 small"><i class="fas fa-arrow-left me-1"></i> Volver al Login</a>
                            <button type="submit" class="btn btn-registrar" id="btn-submit" disabled>Registrar</button>
                        </div>

                    </form>
                </div>
            </div>

            <div class="col-md-5 right-panel d-none d-md-flex" id="panel-huellas">
                <img src="../public/images/DH.jpg" alt="Logo DocuHuella" class="main-logo">
            </div>

        </div>
    </div>

    <script>
        // Animación de huellas
        function crearHuella() {
            const panel = document.getElementById('panel-huellas');
            if(!panel) return;
            const huella = document.createElement('i');
            huella.classList.add('fas', 'fa-paw', 'huella-animada');
            huella.style.left = Math.random() * 90 + '%';
            const tamaño = Math.random() * 2.5 + 1;
            huella.style.fontSize = tamaño + 'rem';
            const duracion = Math.random() * 8 + 7;
            huella.style.animationDuration = duracion + 's';
            panel.appendChild(huella);
            setTimeout(() => { huella.remove(); }, duracion * 1000);
        }
        setInterval(crearHuella, 800);

        // Lógica del Medidor de Contraseñas
        document.getElementById('pass-input').addEventListener('input', function() {
            const pass = this.value;
            const barra = document.getElementById('barra-fuerza');
            const texto = document.getElementById('texto-fuerza');
            const btnSubmit = document.getElementById('btn-submit');
            
            let fuerza = 0;

            if (pass.length >= 6) fuerza += 1; 
            if (pass.length >= 8) fuerza += 1; 
            if (/[A-Z]/.test(pass)) fuerza += 1; 
            if (/[0-9]/.test(pass) && /[a-zA-Z]/.test(pass)) fuerza += 1; 

            if (pass.length === 0) {
                barra.style.width = '0%';
                texto.innerText = 'Mínimo 6 caracteres';
                texto.className = 'text-white opacity-50';
                btnSubmit.disabled = true;
            } else if (fuerza <= 1) {
                barra.style.width = '25%';
                barra.className = 'progress-bar bg-danger';
                texto.innerText = 'Débil (Usa al menos 8 caracteres)';
                texto.className = 'text-danger fw-bold';
                btnSubmit.disabled = true; 
            } else if (fuerza === 2) {
                barra.style.width = '50%';
                barra.className = 'progress-bar bg-warning';
                texto.innerText = 'Intermedia (Válida, pero podría ser más segura con mayúsculas, números o símbolos)';
                texto.className = 'text-warning fw-bold';
                btnSubmit.disabled = false; 
            } else {
                barra.style.width = '100%';
                barra.className = 'progress-bar bg-success';
                texto.innerText = 'Fuerte (Excelente)';
                texto.className = 'text-success fw-bold';
                btnSubmit.disabled = false;
            }

        });

               // Lógica para mostrar/ocultar contraseña
        function toggleVisibilidad(inputId, lineId) {
            const input = document.getElementById(inputId);
            const linea = document.getElementById(lineId);
            
            if (input.type === 'password') {
                input.type = 'text';
                linea.style.display = 'block'; // Muestra la raya diagonal cruzando el ojo
            } else {
                input.type = 'password';
                linea.style.display = 'none';  // Oculta la raya
            }
        }

        // Asignar los clics a los botones
        document.getElementById('btn-ojo-pass').addEventListener('click', function() {
            toggleVisibilidad('pass-input', 'linea-pass');
        });

        document.getElementById('btn-ojo-confirm').addEventListener('click', function() {
            toggleVisibilidad('pass-confirm-input', 'linea-confirm');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php unset($_SESSION['datos_temporales']); ?>
</body>
</html>