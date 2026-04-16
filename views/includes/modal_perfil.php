<!-- Modal Global de Perfil -->
<div class="modal fade" id="modalPerfilGlobal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form action="<?= URL_BASE ?>/perfil/actualizar" method="POST" id="form-perfil-global">
                <div class="modal-header text-white" style="background-color: var(--dh-navy); border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i> Mi Perfil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <?php if(isset($_SESSION['id_rol']) && $_SESSION['id_rol'] != 4): ?>
                            <div class="col-12">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control mascara-telefono-perfil" maxlength="12" placeholder="809-000-0000" inputmode="numeric" value="<?= htmlspecialchars($perfil_info['Telefono'] ?? '') ?>">
                            </div>
                            <?php if($_SESSION['id_rol'] == 2 || $_SESSION['id_rol'] == 3): ?>
                                <div class="col-12">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" name="direccion" class="form-control" placeholder="Escriba su dirección" value="<?= htmlspecialchars($perfil_info['Direccion'] ?? '') ?>">
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <div class="col-12 <?php echo (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 4) ? '' : 'mt-3'; ?>">
                            <div class="p-3 bg-light rounded border border-warning">
                                <h6 class="text-uppercase fw-bold text-muted border-bottom pb-2 mb-3"><i class="fas fa-shield-alt text-warning"></i> Verificación y Seguridad</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label text-danger fw-bold">Contraseña Actual (Obligatoria)</label>
                                    <div class="input-group">
                                        <input type="password" name="password_actual" id="perfil_pass_actual" class="form-control border-danger" required placeholder="Confirme su identidad">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibilidadPerfil('perfil_pass_actual')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Nueva Contraseña (Opcional)</label>
                                    <div class="input-group">
                                        <input type="password" name="password_nueva" id="perfil_pass_nueva" class="form-control" placeholder="Solo si desea cambiarla">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibilidadPerfil('perfil_pass_nueva')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="progress mt-2 d-none" id="cont-barra-perfil" style="height: 5px; background-color: var(--dh-navy);">
                                        <div id="barra-fuerza-perfil" class="progress-bar bg-danger" role="progressbar" style="width: 0%;"></div>
                                    </div>
                                    <small id="texto-fuerza-perfil" class="text-muted d-none" style="font-size: 0.8rem;">Recomendado 8 caracteres mínimos</small>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label">Confirmar Nueva Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" id="perfil_pass_confirm" class="form-control" placeholder="Confirme la nueva contraseña">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibilidadPerfil('perfil_pass_confirm')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <small id="texto-coincidencia-perfil" class="text-danger fw-bold d-none" style="font-size: 0.8rem;">Las contraseñas no coinciden</small>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 20px 20px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" id="btn-guardar-perfil" style="background-color: var(--dh-navy); border: none;">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleVisibilidadPerfil(inputId) {
        const input = document.getElementById(inputId);
        if (input) {
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const passNueva = document.getElementById('perfil_pass_nueva');
        const passConfirm = document.getElementById('perfil_pass_confirm');
        const btnGuardarPerfil = document.getElementById('btn-guardar-perfil');
        const formPerfil = document.getElementById('form-perfil-global');
        
        const barraFuerza = document.getElementById('barra-fuerza-perfil');
        const contBarra = document.getElementById('cont-barra-perfil');
        const textoFuerza = document.getElementById('texto-fuerza-perfil');
        const textoCoincidencia = document.getElementById('texto-coincidencia-perfil');

        function validarPerfil() {
            if(!passNueva || !passConfirm) return;
            
            const pass = passNueva.value;
            const confirm = passConfirm.value;
            
            // Si no está intentando cambiar la contraseña
            if(pass.length === 0 && confirm.length === 0) {
                if(contBarra) contBarra.classList.add('d-none');
                if(textoFuerza) textoFuerza.classList.add('d-none');
                if(textoCoincidencia) textoCoincidencia.classList.add('d-none');
                if(btnGuardarPerfil) btnGuardarPerfil.disabled = false;
                return;
            }

            if(contBarra) contBarra.classList.remove('d-none');
            if(textoFuerza) textoFuerza.classList.remove('d-none');

            let fuerza = 0;
            let esFuerte = false;

            if (pass.length >= 8) fuerza += 1;
            if (/[a-z]/.test(pass)) fuerza += 1;
            if (/[A-Z]/.test(pass)) fuerza += 1;
            if (/[0-9]/.test(pass)) fuerza += 1;
            if (/[^A-Za-z0-9]/.test(pass)) fuerza += 1;

            if (pass.length === 0) {
                if(barraFuerza) barraFuerza.style.width = '0%';
                if(textoFuerza) textoFuerza.innerText = '';
            } else if (fuerza <= 2) {
                if(barraFuerza) { barraFuerza.style.width = '33%'; barraFuerza.className = 'progress-bar bg-danger'; }
                if(textoFuerza) { textoFuerza.innerText = 'Débil (Usa mayúsculas, números y símbolos)'; textoFuerza.className = 'text-danger fw-bold d-block mt-1'; }
            } else if (fuerza === 3 || fuerza === 4) {
                if(barraFuerza) { barraFuerza.style.width = '66%'; barraFuerza.className = 'progress-bar bg-warning text-dark'; }
                if(textoFuerza) { textoFuerza.innerText = 'Intermedia (Agrega un símbolo especial, mayúsculas o números)'; textoFuerza.className = 'text-warning fw-bold d-block mt-1'; }
                esFuerte = true;
            } else if (fuerza === 5) {
                if(barraFuerza) { barraFuerza.style.width = '100%'; barraFuerza.className = 'progress-bar bg-success'; }
                if(textoFuerza) { textoFuerza.innerText = 'Fuerte (Excelente)'; textoFuerza.className = 'text-success fw-bold d-block mt-1'; }
                esFuerte = true;
            }

            let coinciden = false;
            // Solo comparamos si ambos han sido tocados en algún nivel
            if (confirm.length > 0 || pass.length > 0) {
                if (pass === confirm && pass.length > 0) {
                    if(textoCoincidencia) textoCoincidencia.classList.add('d-none');
                    coinciden = true;
                } else if(confirm.length > 0) {
                    if(textoCoincidencia) textoCoincidencia.classList.remove('d-none');
                    coinciden = false;
                }
            }
            
            if (pass.length > 0) {
                if(btnGuardarPerfil) btnGuardarPerfil.disabled = !(esFuerte && coinciden);
            } else {
                if(btnGuardarPerfil) btnGuardarPerfil.disabled = false;
            }
        }

        if (passNueva) passNueva.addEventListener('input', validarPerfil);
        if (passConfirm) passConfirm.addEventListener('input', validarPerfil);

        if(formPerfil) {
            formPerfil.addEventListener('submit', function(e) {
                if (passNueva && passConfirm && passNueva.value.length > 0 && passNueva.value !== passConfirm.value) {
                    e.preventDefault();
                    if(textoCoincidencia) textoCoincidencia.classList.remove('d-none');
                }
            });
        }

        function soloNumerosFormat(e) {
            if (['Backspace', 'Tab', 'ArrowLeft', 'ArrowRight', 'Delete'].includes(e.key)) return;
            if (!/^[0-9]$/.test(e.key)) e.preventDefault();
        }
        
        document.querySelectorAll('.mascara-telefono-perfil').forEach(input => {
            input.addEventListener('keydown', soloNumerosFormat);
            input.addEventListener('input', function(e) {
                let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '');
            });
        });

        // Manejo de alertas según parámetros de URL (SweetAlert2)
        const urlParams = new URLSearchParams(window.location.search);
        const statusPerfil = urlParams.get('status_perfil');

        if (statusPerfil && typeof Swal !== 'undefined') {
            let config = {
                confirmButtonColor: '#1A2D40',
                timer: 4000,
                timerProgressBar: true
            };

            if (statusPerfil === 'success') {
                Swal.fire({
                    ...config,
                    icon: 'success',
                    title: '¡Actualización Exitosa!',
                    text: 'Los cambios en tu perfil han sido guardados correctamente.'
                });
            } else if (statusPerfil === 'error_pass') {
                Swal.fire({
                    ...config,
                    icon: 'error',
                    title: 'Verificación Fallida',
                    text: 'La contraseña actual no coincide. Por seguridad, no se aplicaron los cambios.',
                    timer: 6000
                });
            } else if (statusPerfil === 'error_db') {
                Swal.fire({
                    ...config,
                    icon: 'error',
                    title: 'Error de Servidor',
                    text: 'Ocurrió un problema técnico al guardar tus datos. Inténtalo de nuevo más tarde.',
                    timer: 6000
                });
            }

            // Limpiar la URL para evitar que la alerta reaparezca al recargar
            const newUrl = window.location.origin + window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        }

        // Trigger manual por si falla el data-bs-toggle de Bootstrap
        document.querySelectorAll('[data-bs-target="#modalPerfilGlobal"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const modalEl = document.getElementById('modalPerfilGlobal');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                }
            });
        });
    });
</script>
