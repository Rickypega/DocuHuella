<?php if (session_status() === PHP_SESSION_NONE)
    session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cuidador - DocuHuella</title>
    <link rel="icon" href="<?= URL_BASE ?>/public/images/favicon.png" type="image/x-icon">
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

        body,
        html {
            height: 100%;
            margin: 0;
            background-color: var(--dh-navy);
        }

        .left-panel {
            background-color: var(--dh-navy);
            color: white;
            min-height: 100vh;
            overflow-y: auto;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 3rem 2rem;
        }

        .left-panel::-webkit-scrollbar {
            width: 8px;
        }

        .left-panel::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        .form-content {
            width: 100%;
            max-width: 600px;
            margin: auto 0;
            padding-bottom: 2rem;
        }

        .right-panel {
            background-color: var(--dh-beige);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: sticky;
            top: 0;
            overflow: hidden;
        }

        .main-logo {
            max-width: 90%;
            height: auto;
            position: relative;
            z-index: 10;
            mix-blend-mode: darken;
        }

        .form-label {
            font-size: 0.9rem;
            margin-bottom: 0.2rem;
            color: rgba(255, 255, 255, 0.85);
        }

        .form-control,
        .form-select {
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
            background-color: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.3);
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
    </style>
</head>

<body>

    <div class="container-fluid g-0">
        <div class="row g-0 h-100">

            <div class="col-md-7 left-panel">
                <div class="form-content">

                    <div class="mb-4 text-center">
                        <h2 class="fw-bold fs-3 mt-sm-0 mt-3">Registro de Cuidador</h2>
                        <p class="text-white opacity-75 small">Crea tu cuenta para gestionar el expediente de tu
                            mascota.</p>
                    </div>

                    <?php
                    if (isset($_GET['error'])):
                        // Configuraciones por defecto
                        $clase_alerta = 'alert-danger';
                        $titulo = '¡Atención!';
                        $mensaje = 'Ha ocurrido un error inesperado.';

                        // Evaluamos qué error llegó
                        switch ($_GET['error']) {
                            case 'correo_ya_existe':
                                $titulo = '¡Ups!';
                                $mensaje = 'Este correo ya está registrado en DocuHuella.';
                                break;
                            case 'pass_no_coincide':
                                $clase_alerta = 'alert-warning';
                                $titulo = '¡Cuidado!';
                                $mensaje = 'Las contraseñas no coinciden.';
                                break;
                            case 'correo_no_coincide':
                                $clase_alerta = 'alert-warning';
                                $titulo = '¡Cuidado!';
                                $mensaje = 'Los correos no coinciden.';
                                break;
                            case 'menor_de_edad':
                                $titulo = '¡Acceso denegado!';
                                $mensaje = 'Debes ser mayor de 18 años para registrarte como responsable legal de una mascota.';
                                break;
                            case 'cedula_duplicada':
                                $titulo = '¡Cédula en uso!';
                                $mensaje = 'La cédula ingresada ya está registrada en el sistema.';
                                break;
                            case 'perfil_fallo':
                                $titulo = '¡Error de Perfil!';
                                $mensaje = 'No se pudo crear el perfil correctamente. Verifica tus datos e intenta de nuevo.';
                                break;
                        }
                        ?>
                        <div class="alert <?= $clase_alerta ?> alert-dismissible fade show" role="alert">
                            <strong><?= $titulo ?></strong> <?= $mensaje ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['exito']) && $_GET['exito'] == 'registrado'): ?>
                        <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                            <strong>¡Registro Exitoso!</strong> Tu cuenta ha sido creada. Ya puedes iniciar sesión.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form id="form-registro" action="../controllers/RegistroController.php?action=registrar_cuidador" method="POST">

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
                                <input type="text" name="cedula" class="form-control mascara-cedula" maxlength="13"
                                    placeholder="000-0000000-0" inputmode="numeric" required
                                    value="<?php echo isset($_SESSION['datos_temporales']['cedula']) ? htmlspecialchars($_SESSION['datos_temporales']['cedula']) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Teléfono</label>
                                <input type="text" name="telefono" class="form-control mascara-telefono" maxlength="12"
                                    placeholder="809-000-0000" inputmode="numeric" required
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
                                    <option value="" <?php echo ($sexo_temp == '') ? 'selected' : ''; ?> disabled>
                                        Seleccione...</option>
                                    <option value="M" <?php echo ($sexo_temp == 'M') ? 'selected' : ''; ?>>Masculino
                                    </option>
                                    <option value="F" <?php echo ($sexo_temp == 'F') ? 'selected' : ''; ?>>Femenino
                                    </option>
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
                                    <input type="password" class="form-control" name="contrasena" id="pass-input"
                                        required style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                    <button
                                        class="btn btn-outline-secondary d-flex align-items-center justify-content-center"
                                        type="button" id="btn-ojo-pass"
                                        style="border-top-right-radius: 20px; border-bottom-right-radius: 20px; background-color: var(--dh-light-gray); border: none; padding: 0 15px;">
                                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 13 Q 12 5 19 13" />
                                            <circle cx="12" cy="14" r="2.5" />
                                            <line id="linea-pass" x1="4" y1="4" x2="20" y2="20"
                                                style="display: none;" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="progress mt-2" style="height: 5px; background-color: var(--dh-navy);">
                                    <div id="barra-fuerza" class="progress-bar bg-danger" role="progressbar"
                                        style="width: 0%;"></div>
                                </div>
                                <small id="texto-fuerza" class="text-white opacity-50"
                                    style="font-size: 0.8rem;">Recomendado 8 caracteres mínimos</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirmar Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="confirmar_contrasena"
                                        id="pass-confirm-input" required
                                        style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                    <button
                                        class="btn btn-outline-secondary d-flex align-items-center justify-content-center"
                                        type="button" id="btn-ojo-confirm"
                                        style="border-top-right-radius: 20px; border-bottom-right-radius: 20px; background-color: var(--dh-light-gray); border: none; padding: 0 15px;">
                                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 13 Q 12 5 19 13" />
                                            <circle cx="12" cy="14" r="2.5" />
                                            <line id="linea-confirm" x1="4" y1="4" x2="20" y2="20"
                                                style="display: none;" />
                                        </svg>
                                    </button>
                                </div>
                                <small id="texto-coincidencia" class="text-danger fw-bold d-none"
                                    style="font-size: 0.8rem;">Las contraseñas no coinciden</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="<?= URL_BASE ?>/login" class="text-white text-decoration-none opacity-75 small"><i
                                    class="fas fa-arrow-left me-1"></i> Ya Tengo Una Cuenta </a>
                            <button type="submit" class="btn btn-registrar" id="btn-submit" disabled>Registrar
                                Cuidador</button>
                        </div>

                    </form>
                </div>
            </div>

            <div class="col-md-5 right-panel d-none d-md-flex" id="panel-huellas">
                <a href="<?= URL_BASE ?>/"
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 20; display: block; cursor: default;"></a>
                <img src="<?= URL_BASE ?>/public/images/DH.jpg" alt="Logo DocuHuella" class="main-logo"
                    style="position: relative; z-index: 10;">
            </div>

        </div>
    </div>

    <script>
        // Animación de huellas
        function crearHuella() {
            const panel = document.getElementById('panel-huellas');
            if (!panel) return;
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

        // Lógica del Medidor de Contraseñas y Habilitación del Botón
        const passInput = document.getElementById('pass-input');
        const confirmInput = document.getElementById('pass-confirm-input');
        const btnSubmit = document.getElementById('btn-submit');
        const textoCoincidencia = document.getElementById('texto-coincidencia');

        function validarFormulario() {
            const pass = passInput.value;
            const confirmPass = confirmInput.value;
            const barra = document.getElementById('barra-fuerza');
            const texto = document.getElementById('texto-fuerza');

            let fuerza = 0;
            let esFuerte = false;

            // Calcular fuerza evaluando 5 criterios distintos (Máximo 5 puntos)
            if (pass.length >= 8) fuerza += 1;                  // 1. Longitud segura
            if (/[a-z]/.test(pass)) fuerza += 1;                // 2. Tiene letras minúsculas
            if (/[A-Z]/.test(pass)) fuerza += 1;                // 3. Tiene letras mayúsculas
            if (/[0-9]/.test(pass)) fuerza += 1;                // 4. Tiene números
            if (/[^A-Za-z0-9]/.test(pass)) fuerza += 1;         // 5. Tiene símbolos especiales (!@#$%^&*...)

            // Estilos de la barra de progreso
            if (pass.length === 0) {
                barra.style.width = '0%';
                texto.innerText = 'Recomendado 8 caracteres mínimos';
                texto.className = 'text-white opacity-50';
            } else if (fuerza <= 2) {
                barra.style.width = '33%';
                barra.className = 'progress-bar bg-danger';
                texto.innerText = 'Débil (Usa mayúsculas, números y símbolos)';
                texto.className = 'text-danger fw-bold';
            } else if (fuerza === 3 || fuerza === 4) {
                barra.style.width = '66%';
                barra.className = 'progress-bar bg-warning text-dark';
                texto.innerText = 'Intermedia (Agrega un símbolo especial, mayúsculas o números)';
                texto.className = 'text-warning fw-bold';
                esFuerte = true; // Ya es decente, permitimos el registro
            } else if (fuerza === 5) {
                barra.style.width = '100%';
                barra.className = 'progress-bar bg-success';
                texto.innerText = 'Fuerte (Excelente)';
                texto.className = 'text-success fw-bold';
                esFuerte = true; // Cumple con todos los estándares
            }

            // Validar coincidencia SOLO si el usuario ya escribió algo en confirmación
            let coinciden = false;
            if (confirmPass.length > 0) {
                if (pass === confirmPass) {
                    textoCoincidencia.classList.add('d-none');
                    coinciden = true;
                } else {
                    textoCoincidencia.classList.remove('d-none');
                    coinciden = false;
                }
            } else {
                textoCoincidencia.classList.add('d-none');
            }

            // Validar coincidencia de correo
            const correo = document.querySelector('input[name="correo"]').value;
            const confirmCorreo = document.querySelector('input[name="confirmar_correo"]').value;
            const correosCoinciden = (correo === confirmCorreo && correo.trim() !== '');

            // Validar que todos los campos requeridos tengan algún valor
            let todosLlenos = true;
            const camposRequeridos = document.querySelectorAll('#form-registro input[required], #form-registro select[required]');
            camposRequeridos.forEach(campo => {
                if (!campo.value.trim()) {
                    todosLlenos = false;
                }
            });

            // El botón solo se activa si la contraseña es fuerte, ambas contraseñas coinciden,
            // los correos coinciden, y TODOS los campos están llenos.
            if (esFuerte && coinciden && correosCoinciden && todosLlenos) {
                btnSubmit.disabled = false;
            } else {
                btnSubmit.disabled = true;
            }
        }

        // Escuchamos absolutamente todos los campos para validar en tiempo real
        const formInputs = document.querySelectorAll('#form-registro input[required], #form-registro select[required]');
        formInputs.forEach(input => {
            input.addEventListener('input', validarFormulario);
            input.addEventListener('change', validarFormulario);
        });

        // Lógica para mostrar/ocultar contraseña
        function toggleVisibilidad(inputId, lineId) {
            const input = document.getElementById(inputId);
            const linea = document.getElementById(lineId);
            if (input.type === 'password') {
                input.type = 'text';
                linea.style.display = 'block';
            } else {
                input.type = 'password';
                linea.style.display = 'none';
            }
        }

        document.getElementById('btn-ojo-pass').addEventListener('click', function () {
            toggleVisibilidad('pass-input', 'linea-pass');
        });
        document.getElementById('btn-ojo-confirm').addEventListener('click', function () {
            toggleVisibilidad('pass-confirm-input', 'linea-confirm');
        });
    </script>

    <script>
        // ... tu código de las huellas y contraseñas ...

        //  MÁSCARAS DIRECTAS EN LA VISTA
        function soloNumerosYFormato(e) {
            // Permitir teclas de borrado y flechas
            if (['Backspace', 'Tab', 'ArrowLeft', 'ArrowRight', 'Delete'].includes(e.key)) return;

            // Bloquear si intentan escribir una letra (si no es un número del 0 al 9)
            if (!/^[0-9]$/.test(e.key)) {
                e.preventDefault();
            }
        }

        // Aplicar a Cédula
        const inputCedula = document.querySelector('.mascara-cedula');
        if (inputCedula) {
            inputCedula.addEventListener('keydown', soloNumerosYFormato);
            inputCedula.addEventListener('input', function (e) {
                let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,7})(\d{0,1})/);
                e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '');
            });
        }

        // Aplicar a Teléfono
        const inputTelefono = document.querySelector('.mascara-telefono');
        if (inputTelefono) {
            inputTelefono.addEventListener('keydown', soloNumerosYFormato);
            inputTelefono.addEventListener('input', function (e) {
                let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '');
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php unset($_SESSION['datos_temporales']); ?>
</body>

</html>