<!-- Modal para Registrar Nueva Mascota -->
<div class="modal fade" id="modalRegistrarMascota" tabindex="-1" aria-labelledby="modalMascotaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header text-white" style="background-color: var(--dh-navy); border: none;">
                <h5 class="modal-title fw-bold" id="modalMascotaLabel"><i class="fas fa-paw me-2"></i>Preregistro de Mascota</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formRegistrarMascota" enctype="multipart/form-data">
                    <div class="row g-3">
                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombre de la Mascota</label>
                            <input type="text" id="nombre_mascota" name="nombre" class="form-control" placeholder="Ej: Toby" required style="border-radius: 10px;">
                            <small class="text-danger fw-bold d-block mt-1" style="font-size: 0.73rem;">
                                <i class="fas fa-exclamation-triangle me-1"></i>Este nombre NO podrá ser modificado después.
                            </small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Confirmar Nombre</label>
                            <input type="text" id="confirmar_nombre" name="confirmar_nombre" class="form-control" placeholder="Repite el nombre" required style="border-radius: 10px;">
                            <div id="feedback-nombre" class="mt-1 fw-bold" style="font-size: 0.82rem; min-height: 1.2rem;"></div>
                        </div>

                        <!-- Especie y Color -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Especie</label>
                            <select name="id_especie" id="reg_mascota_especie" class="form-select" required style="border-radius: 10px;">
                                <option value="">Selecciona...</option>
                                <?php if (isset($especies) && is_array($especies)): ?>
                                    <?php foreach ($especies as $esp): ?>
                                        <option value="<?php echo @$esp['ID_Especie']; ?>"><?php echo htmlspecialchars(@$esp['Nombre_Especie'] ?? ''); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Color</label>
                            <select name="id_color" id="reg_mascota_color" class="form-select" style="border-radius: 10px;">
                                <option value="">Selecciona...</option>
                                <?php if (isset($colores) && is_array($colores)): ?>
                                    <?php foreach ($colores as $col): ?>
                                        <option value="<?php echo @$col['ID_Color']; ?>"><?php echo htmlspecialchars(@$col['Nombre_Color'] ?? ''); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Sexo y Esterilización -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sexo</label>
                            <select name="sexo" class="form-select" required style="border-radius: 10px;">
                                <option value="">Selecciona...</option>
                                <option value="M">Macho</option>
                                <option value="F">Hembra</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">¿Está Esterilizado/a?</label>
                            <select name="esterilizacion" class="form-select" required style="border-radius: 10px;">
                                <option value="No">No</option>
                                <option value="Si">Sí</option>
                                <option value="No lo sé">No lo sé</option>
                            </select>
                        </div>

                        <!-- Rasgos -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Rasgos Distintivos / Notas</label>
                            <textarea name="rasgos" class="form-control" rows="2" placeholder="Ej: Mancha blanca en la pata derecha..." style="border-radius: 10px;"></textarea>
                        </div>
                        
                        <!-- Foto -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold d-block">Foto de la Mascota</label>
                            <div class="upload-area mt-2">
                                <label for="fotoMascota" class="btn btn-outline-primary w-100 d-flex flex-column align-items-center justify-content-center" style="border-style: dashed; padding: 20px; border-radius: 15px; border-width: 2px; min-height: 120px;">
                                    <i class="fas fa-camera fa-2x mb-2"></i>
                                    <span class="fw-bold">Subir Foto</span>
                                    <small class="text-muted mt-1" style="font-size: 0.65rem;">JPG, PNG (máx 2MB)</small>
                                </label>
                                <input type="file" id="fotoMascota" name="foto" class="d-none" accept="image/jpeg, image/png" onchange="previewImage(this)">
                            </div>
                        </div>

                        <!-- Preview -->
                        <div class="col-md-6 d-flex flex-column align-items-center justify-content-center">
                            <div id="previewContainer" class="d-none text-center">
                                <div class="position-relative d-inline-block">
                                    <img id="imgPreview" src="#" class="img-fluid rounded shadow" style="max-height: 110px; width: 110px; object-fit: cover; border: 3px solid #fff;">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle" style="transform: translate(40%, -40%); padding: 3px 7px;" onclick="quitarFoto()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <p class="text-muted small mt-2 mb-0" id="file-info"></p>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            <div class="alert alert-info border-0 mb-0 py-2 d-flex align-items-center" style="border-radius: 10px; background-color: rgba(26, 45, 64, 0.05); color: var(--dh-navy);">
                                <i class="fas fa-info-circle me-3"></i>
                                <span style="font-size: 0.8rem;">Recuerda que podrás completar los detalles médicos con tu veterinario.</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-light px-4 me-2 border" data-bs-dismiss="modal" style="border-radius: 10px;">Cerrar</button>
                        <button type="submit" class="btn btn-primary px-5" style="border-radius: 10px; background-color: var(--dh-navy); border: none;">
                            <i class="fas fa-save me-1"></i>Guardar Preregistro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    function initModalLogic() {
        const inputNombre = document.getElementById('nombre_mascota');
        const inputConfirmar = document.getElementById('confirmar_nombre');
        const feedback = document.getElementById('feedback-nombre');

        if (inputNombre && inputConfirmar && feedback) {
            function validarNombres() {
                const v1 = inputNombre.value.trim().toLowerCase();
                const v2 = inputConfirmar.value.trim().toLowerCase();
                
                if (v2 === "") {
                    feedback.innerHTML = "";
                    feedback.className = "mt-1 fw-bold";
                } else if (v1 === v2 && v1.length > 0) {
                    feedback.innerHTML = '<i class="fas fa-check-circle me-1"></i>Los nombres coinciden';
                    feedback.className = "mt-1 fw-bold text-success";
                } else {
                    feedback.innerHTML = '<i class="fas fa-times-circle me-1"></i>Los nombres no coinciden';
                    feedback.className = "mt-1 fw-bold text-danger";
                }
            }
            inputNombre.addEventListener('input', validarNombres);
            inputConfirmar.addEventListener('input', validarNombres);
        }

        window.previewImage = function(input) {
            const preview = document.getElementById('imgPreview');
            const container = document.getElementById('previewContainer');
            const info = document.getElementById('file-info');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                const allowed = ['image/jpeg', 'image/jpg', 'image/png'];

                // Validación de tipo
                if (!allowed.includes(file.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Formato no válido',
                        text: 'Solo se permiten imágenes JPG o PNG.',
                        confirmButtonColor: '#1A2D40'
                    });
                    input.value = "";
                    container.classList.add('d-none');
                    return;
                }

                // Validación de tamaño (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Imagen muy pesada',
                        text: `El archivo pesa ${sizeMB}MB. El límite es 2MB.`,
                        confirmButtonColor: '#1A2D40'
                    });
                    input.value = "";
                    container.classList.add('d-none');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    if(preview) preview.src = e.target.result;
                    if(container) container.classList.remove('d-none');
                    if(info) info.textContent = `${file.name} (${sizeMB}MB)`;
                }
                reader.readAsDataURL(file);
            }
        }

        window.quitarFoto = function() {
            const input = document.getElementById('fotoMascota');
            const container = document.getElementById('previewContainer');
            if(input) input.value = "";
            if(container) container.classList.add('d-none');
        }

        const formReg = document.getElementById('formRegistrarMascota');
        if(formReg) {
            formReg.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const n1 = inputNombre.value.trim().toLowerCase();
                const n2 = inputConfirmar.value.trim().toLowerCase();
                
                if (n1 !== n2) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Nombres no coinciden',
                        text: 'Asegúrate de que el nombre y su confirmación sean iguales.',
                        confirmButtonColor: '#1A2D40'
                    });
                    return;
                }

                Swal.fire({
                    title: '¿Confirmar Preregistro?',
                    text: "El nombre no podrá ser editado después.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#1A2D40',
                    confirmButtonText: 'Sí, registrar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData(this);
                        fetch('<?= URL_BASE ?>/cuidador/mascota/registrar', {
                            method: 'POST',
                            body: formData
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: 'Mascota preregistrada correctamente.',
                                    confirmButtonColor: '#1A2D40'
                                }).then(() => location.reload());
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message || 'Error desconocido.',
                                    confirmButtonColor: '#1A2D40'
                                });
                            }
                        })
                        .catch(err => {
                            Swal.fire({ icon: 'error', title: 'Error de Red', text: 'No se pudo comunicar con el servidor.', confirmButtonColor: '#1A2D40' });
                        });
                    }
                });
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initModalLogic);
    } else {
        initModalLogic();
    }
})();
</script>
