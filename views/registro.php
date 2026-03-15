<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cuidador - DocuHuella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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

        /* Panel Izquierdo: Formulario (Azul) */
        .left-panel {
            background-color: var(--dh-navy);
            color: white;
            height: 100vh;
            overflow-y: auto; /* Permite hacer scroll si la pantalla es pequeña */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
        }

        /* Ocultar barra de scroll para que se vea más limpio */
        .left-panel::-webkit-scrollbar { width: 8px; }
        .left-panel::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }

        .form-content {
            width: 100%;
            max-width: 600px; /* Más ancho para las dos columnas */
        }

        /* Panel Derecho: Logo (Beige) */
        .right-panel {
            background-color: var(--dh-beige);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden; /* Para las huellas */
        }

        .main-logo {
            max-width: 70%;
            position: relative;
            z-index: 10;
            mix-blend-mode: darken; 
        }

        /* Estilos de inputs y botones */
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
            background-color: var(--dh-beige); /* Botón del color del panel derecho */
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

        /* Animación de huellas */
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

                    <form action="../controllers/RegistroController.php?action=registrar_cuidador" method="POST">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nombre</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Apellido</label>
                                <input type="text" class="form-control" name="apellido" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Cédula</label>
                                <input type="text" class="form-control" name="cedula" placeholder="000-0000000-0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Teléfono</label>
                                <input type="tel" class="form-control" name="telefono" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" name="fecha_nacimiento" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Sexo</label>
                                <select class="form-select" name="sexo" required>
                                    <option value="" selected disabled>Seleccione...</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Dirección</label>
                                <input type="text" class="form-control" name="direccion" required>
                            </div>
                        </div>

                        <hr class="border-secondary my-4">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Correo Electrónico</label>
                                <input type="email" class="form-control" name="correo" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirmar Correo</label>
                                <input type="email" class="form-control" name="confirmar_correo" required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contraseña</label>
                                <input type="password" class="form-control" name="contrasena" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirmar Contraseña</label>
                                <input type="password" class="form-control" name="confirmar_contrasena" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="login.php" class="text-white text-decoration-none opacity-75 small"><i class="fas fa-arrow-left me-1"></i> Volver al Login</a>
                            <button type="submit" class="btn btn-registrar">Registrar</button>
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
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>