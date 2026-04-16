<?php

// Bloquear acceso directo
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("Location: " . URL_BASE . "/login");
    exit();
}
?>

<div id="panel-mis-notas" class="d-none">

    <!-- Cabecera: Título + botón config (⋮) + botón Nueva Nota -->
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <h2 class="fw-bold mb-0" style="color: var(--dh-navy);">
                <i class="fas fa-sticky-note me-2" style="color: var(--dh-navy);"></i>Mis Notas
            </h2>
            <!-- Botón de configuración (⋮) -->
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary rounded-circle" type="button" id="btnConfigNotas"
                    data-bs-toggle="dropdown" aria-expanded="false" title="Opciones"
                    style="width:34px; height:34px; padding:0; font-size:1.1rem;">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-start shadow-sm border-0"
                    style="border-radius:12px; min-width:220px;">
                    <li>
                        <button class="dropdown-item text-danger d-flex align-items-center gap-2 py-2"
                            onclick="abrirModalEliminarTodas()">
                            <i class="fas fa-trash-alt"></i> Eliminar todas las notas
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Botón Nueva Nota -->
        <button class="btn text-white d-flex align-items-center gap-2"
            style="background-color: var(--dh-navy); border-radius: 10px; padding: 8px 18px;"
            onclick="abrirModalNuevaNota()">
            <i class="fas fa-plus"></i> Nueva Nota
        </button>
    </div>

    <!-- Filtros de búsqueda y fecha -->
    <div class="card border-0 shadow-sm mb-4 p-3" style="border-radius: 14px; background: #f8f9ff; overflow: hidden;">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label text-muted small mb-1">Buscar nota</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" id="nota-busqueda" class="form-control border-start-0"
                        placeholder="Título o contenido..." oninput="cargarNotas()">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label text-muted small mb-1">Desde</label>
                <input type="date" id="nota-fecha-inicio" class="form-control" onchange="cargarNotas()">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label text-muted small mb-1">Hasta</label>
                <input type="date" id="nota-fecha-fin" class="form-control" onchange="cargarNotas()">
            </div>
            <div class="col-12 col-md-2">
                <button class="btn btn-outline-secondary w-100" onclick="limpiarFiltrosNotas()">
                    <i class="fas fa-times me-1"></i>Limpiar
                </button>
            </div>
        </div>
    </div>

    <!-- Grid de tarjetas de notas -->
    <div id="grid-notas" class="row g-3">
        <!-- Las tarjetas se renderizan por JS -->
    </div>

    <!-- Estado vacío (oculto por defecto) -->
    <div id="notas-empty-state" class="d-none text-center py-5 my-3">
        <div style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.25;">
            <i class="fas fa-sticky-note"></i>
        </div>
        <h5 class="fw-bold text-muted">Aún no tienes notas</h5>
        <p class="text-muted" style="max-width: 340px; margin: 0 auto;">
            ¡Empieza escribiendo tu primera idea! Haz clic en <strong>+ Nueva Nota</strong> para comenzar.
        </p>
    </div>

    <!-- Spinner de carga -->
    <div id="notas-spinner" class="text-center py-5 d-none">
        <div class="spinner-border text-secondary" role="status" style="width:2rem;height:2rem;"></div>
    </div>

</div><!-- /panel-mis-notas -->

<!-- ================================================================
     MODAL: NUEVA / EDITAR NOTA
================================================================ -->
<div class="modal fade" id="modalNota" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header text-white"
                style="background-color: var(--dh-navy); border-radius: 20px 20px 0 0;">
                <h5 class="modal-title" id="modalNotaTitulo">
                    <i class="fas fa-sticky-note me-2"></i>Nueva Nota
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="nota_id_editar" value="">

                <!-- Título -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                    <input type="text" id="nota_titulo" class="form-control" placeholder="Título de la nota"
                        maxlength="200" oninput="validarFormNota()">
                    <small class="text-danger d-none" id="nota_titulo_error">El título es obligatorio.</small>
                </div>

                <!-- Contenido -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Contenido</label>
                    <textarea id="nota_contenido" class="form-control" rows="6" placeholder="Escribe aquí tu nota..."
                        style="resize: vertical;"></textarea>
                </div>

                <!-- Color de etiqueta -->
                <div class="mb-1">
                    <label class="form-label fw-semibold">Color de etiqueta</label>
                    <div class="d-flex gap-2 flex-wrap" id="nota-colores">
                        <?php
                        $colores_etiquetas_notas = [
                            '#1A2D40' => 'Navy',
                            '#2563EB' => 'Azul',
                            '#16A34A' => 'Verde',
                            '#DC2626' => 'Rojo',
                            '#D97706' => 'Ámbar',
                            '#7C3AED' => 'Violeta',
                            '#DB2777' => 'Rosa',
                            '#6B7280' => 'Gris',
                        ];
                        foreach ($colores_etiquetas_notas as $hex => $nombre): ?>
                            <button type="button" class="btn-color-nota" data-color="<?= $hex ?>" title="<?= $nombre ?>"
                                onclick="seleccionarColor('<?= $hex ?>')"
                                style="width:30px; height:30px; border-radius:50%; background:<?= $hex ?>; border:3px solid transparent; cursor:pointer; transition:all .2s;">
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" id="nota_color" value="#1A2D40">
                </div>
            </div>
            <div class="modal-footer bg-light" style="border-radius: 0 0 20px 20px;">
                <!-- Botón eliminar (solo en modo edición) -->
                <button type="button" id="btn-nota-eliminar" class="btn btn-outline-danger me-auto d-none"
                    onclick="eliminarNotaActual()">
                    <i class="fas fa-trash-alt me-1"></i>Eliminar
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn text-white" id="btn-nota-guardar"
                    style="background-color: var(--dh-navy); border: none; opacity: 0.5;" onclick="guardarNota()"
                    disabled>
                    <i class="fas fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ================================================================
     MODAL: ELIMINAR TODAS LAS NOTAS (requiere contraseña)
================================================================ -->
<div class="modal fade" id="modalEliminarTodas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header text-white" style="background-color: #8a0a2a; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Eliminar Todas las Notas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" style="border-radius:10px;">
                    <i class="fas fa-shield-alt fs-5"></i>
                    <span>Esta acción es <strong>irreversible</strong>. Se eliminarán <strong>todas</strong> tus notas
                        permanentemente.</span>
                </div>
                <label class="form-label fw-bold text-danger">Confirma con tu contraseña</label>
                <div class="input-group">
                    <input type="password" id="eliminar_todas_pass" class="form-control border-danger"
                        placeholder="Ingresa tu contraseña actual">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassEliminarTodas()">
                        <i class="fas fa-eye" id="ico-pass-eliminar"></i>
                    </button>
                </div>
                <small id="msg-eliminar-todas" class="text-danger fw-bold d-none mt-2 d-block"></small>
            </div>
            <div class="modal-footer bg-light" style="border-radius: 0 0 20px 20px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger px-4" id="btn-confirmar-eliminar-todas"
                    onclick="confirmarEliminarTodas()">
                    <i class="fas fa-trash-alt me-1"></i>Eliminar Todo
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ================================================================
     CSS PARA LAS TARJETAS DE NOTAS
================================================================ -->
<style>
    /* Grid cards de notas */
    .nota-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.07);
        cursor: pointer;
        transition: transform .18s ease, box-shadow .18s ease;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.06);
        display: flex;
        flex-direction: column;
        min-height: 140px;
    }

    .nota-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.13);
    }

    .nota-card .nota-color-bar {
        height: 5px;
        width: 100%;
        flex-shrink: 0;
    }

    .nota-card .nota-body {
        padding: 14px 14px 10px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .nota-card .nota-contenido-preview {
        font-size: 0.82rem;
        color: #6b7280;
        flex: 1;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }

    .nota-card .nota-titulo-label {
        font-weight: 700;
        font-size: 0.9rem;
        color: #1a2d40;
        margin-top: 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 6px 14px 10px;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        background: #fafafe;
    }

    .nota-card .nota-fecha {
        font-size: 0.72rem;
        color: #9ca3af;
        padding: 0 14px 8px;
        background: #fafafe;
    }

    /* Color selector */
    .btn-color-nota {
        outline: none;
    }

    .btn-color-nota.activo {
        border-color: #fff !important;
        box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.35);
        transform: scale(1.15);
    }

    /* Panel transición */
    #panel-mis-notas {
        animation: fadeInPanel .25s ease;
    }

    @keyframes fadeInPanel {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>


<!-- ================================================================
     JAVASCRIPT — LÓGICA COMPLETA DE NOTAS
================================================================ -->
<!-- Cargar SweetAlert2 solo si todavía no está cargado en la página -->
<script>
    if (typeof Swal === 'undefined') {
        var _swalScript = document.createElement('script');
        _swalScript.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js';
        document.head.appendChild(_swalScript);
    }
</script>
<script>
    // URL base de la API de notas
    var NOTAS_API_URL = '<?= URL_BASE ?>/notas/api';

    // Referencia al modal de Bootstrap
    var modalNotaBS = modalNotaBS || null;
    var modalEliminarTodasBS = modalEliminarTodasBS || null;

    document.addEventListener('DOMContentLoaded', function () {
        const modalNotaEl = document.getElementById('modalNota');
        const modalEliminarTodasEl = document.getElementById('modalEliminarTodas');
        
        // Mover modales al final del body para evitar conflictos de z-index (Pantalla Oscura)
        if (modalNotaEl) document.body.appendChild(modalNotaEl);
        if (modalEliminarTodasEl) document.body.appendChild(modalEliminarTodasEl);

        if (modalNotaEl && typeof bootstrap !== 'undefined') {
            modalNotaBS = new bootstrap.Modal(modalNotaEl);
        }
        if (modalEliminarTodasEl && typeof bootstrap !== 'undefined') {
            modalEliminarTodasBS = new bootstrap.Modal(modalEliminarTodasEl);
        }

        // Deep linking: Verificar si el panel de notas debe abrirse al entrar
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('view') === 'notas') {
            mostrarPanelNotas(false); 
        }
    });

    window.onpopstate = function(event) {
        if (event.state && event.state.panel === 'notas') {
            mostrarPanelNotas(false);
        } else {
            ocultarPanelNotas(false);
        }
    };

    // ============================================================
    // MOSTRAR / OCULTAR el panel de Mis Notas
    // ============================================================
    function mostrarPanelNotas(shouldPushState = true) {
        const panelNotas = document.getElementById('panel-mis-notas');
        if (!panelNotas) return;

        // Ocultar todos los hermanos EXCEPT el panel de notas
        const siblings = panelNotas.parentNode.children;
        for (let s of siblings) {
            if (s !== panelNotas) s.classList.add('d-none');
        }
        panelNotas.classList.remove('d-none');

        // Marcar el enlace activo en el sidebar
        document.querySelectorAll('.sidebar nav a').forEach(a => a.classList.remove('active'));
        const enlaceNotas = document.getElementById('enlace-mis-notas');
        if (enlaceNotas) enlaceNotas.classList.add('active');

        if (shouldPushState) {
            const url = new URL(window.location);
            url.searchParams.set('view', 'notas');
            window.history.pushState({panel: 'notas'}, '', url);
        }

        cargarNotas();
    }

    function ocultarPanelNotas(shouldPushState = true) {
        const panelNotas = document.getElementById('panel-mis-notas');
        if (!panelNotas) return;

        const siblings = panelNotas.parentNode.children;
        for (let s of siblings) {
            if (s !== panelNotas) s.classList.remove('d-none');
        }
        panelNotas.classList.add('d-none');

        if (shouldPushState) {
            const url = new URL(window.location);
            url.searchParams.delete('view');
            window.history.pushState({}, '', url);
        }
    }

    // ============================================================
    // CARGAR NOTAS VIA AJAX
    // ============================================================
    function cargarNotas() {
        const busqueda = document.getElementById('nota-busqueda')?.value || '';
        const fechaInicio = document.getElementById('nota-fecha-inicio')?.value || '';
        const fechaFin = document.getElementById('nota-fecha-fin')?.value || '';

        mostrarSpinnerNotas(true);

        const formData = new FormData();
        formData.append('action', 'obtener');
        formData.append('busqueda', busqueda);
        formData.append('fecha_inicio', fechaInicio);
        formData.append('fecha_fin', fechaFin);

        fetch(NOTAS_API_URL, { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                mostrarSpinnerNotas(false);
                if (data.exito) {
                    renderizarTarjetas(data.notas);
                }
            })
            .catch(() => {
                mostrarSpinnerNotas(false);
            });
    }

    // ============================================================
    // RENDERIZAR TARJETAS
    // ============================================================
    function renderizarTarjetas(notas) {
        const grid = document.getElementById('grid-notas');
        const emptyState = document.getElementById('notas-empty-state');

        grid.innerHTML = '';

        if (!notas || notas.length === 0) {
            emptyState.classList.remove('d-none');
            return;
        }

        emptyState.classList.add('d-none');

        notas.forEach(nota => {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-4 col-xl-3';

            const card = document.createElement('div');
            card.className = 'nota-card';
            card.onclick = () => abrirModalEditar(nota);
            card.setAttribute('data-id', nota.ID_Nota);

            // Barra de color superior
            const colorBar = document.createElement('div');
            colorBar.className = 'nota-color-bar';
            colorBar.style.background = nota.Color_Etiqueta || '#1A2D40';

            // Cuerpo: preview del contenido
            const body = document.createElement('div');
            body.className = 'nota-body';

            const preview = document.createElement('div');
            preview.className = 'nota-contenido-preview';
            // Usamos textContent para evitar XSS
            preview.textContent = nota.Contenido
                ? decodeHTMLEntities(nota.Contenido)
                : '(sin contenido)';

            body.appendChild(preview);

            // Título debajo
            const tituloLabel = document.createElement('div');
            tituloLabel.className = 'nota-titulo-label';
            tituloLabel.textContent = decodeHTMLEntities(nota.Titulo);

            // Fecha
            const fechaDiv = document.createElement('div');
            fechaDiv.className = 'nota-fecha';
            fechaDiv.textContent = formatearFecha(nota.Fecha_Creacion);

            card.appendChild(colorBar);
            card.appendChild(body);
            card.appendChild(tituloLabel);
            card.appendChild(fechaDiv);

            col.appendChild(card);
            grid.appendChild(col);
        });
    }

    // Decodifica entidades HTML (para mostrar caracteres especiales guardados con htmlspecialchars)
    function decodeHTMLEntities(text) {
        const txt = document.createElement('textarea');
        txt.innerHTML = text;
        return txt.value;
    }

    function formatearFecha(fechaStr) {
        if (!fechaStr) return '';
        const d = new Date(fechaStr.replace(' ', 'T'));
        return d.toLocaleDateString('es-DO', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    // ============================================================
    // SPINNER
    // ============================================================
    function mostrarSpinnerNotas(show) {
        const spinner = document.getElementById('notas-spinner');
        const grid = document.getElementById('grid-notas');
        if (show) {
            spinner.classList.remove('d-none');
            grid.classList.add('d-none');
        } else {
            spinner.classList.add('d-none');
            grid.classList.remove('d-none');
        }
    }

    // ============================================================
    // FILTROS — Limpiar
    // ============================================================
    function limpiarFiltrosNotas() {
        document.getElementById('nota-busqueda').value = '';
        document.getElementById('nota-fecha-inicio').value = '';
        document.getElementById('nota-fecha-fin').value = '';
        cargarNotas();
    }

    // ============================================================
    // MODAL NUEVA NOTA
    // ============================================================
    function abrirModalNuevaNota() {
        const modalEl = document.getElementById('modalNota');
        if (!modalNotaBS && modalEl && typeof bootstrap !== 'undefined') {
            modalNotaBS = new bootstrap.Modal(modalEl);
        }

        if (document.getElementById('modalNotaTitulo')) {
            document.getElementById('modalNotaTitulo').innerHTML = '<i class="fas fa-plus me-2"></i>Nueva Nota';
        }
        
        if (document.getElementById('nota_id_editar')) document.getElementById('nota_id_editar').value = '';
        if (document.getElementById('nota_titulo')) document.getElementById('nota_titulo').value = '';
        if (document.getElementById('nota_contenido')) document.getElementById('nota_contenido').value = '';
        if (document.getElementById('nota_color')) document.getElementById('nota_color').value = '#1A2D40';
        
        const btnEliminar = document.getElementById('btn-nota-eliminar');
        if (btnEliminar) btnEliminar.classList.add('d-none');
        
        seleccionarColor('#1A2D40');
        validarFormNota();
        
        if (modalNotaBS) modalNotaBS.show();
    }

    // ============================================================
    // MODAL EDITAR NOTA
    // ============================================================
    function abrirModalEditar(nota) {
        const modalEl = document.getElementById('modalNota');
        if (!modalNotaBS && modalEl && typeof bootstrap !== 'undefined') {
            modalNotaBS = new bootstrap.Modal(modalEl);
        }

        if (document.getElementById('modalNotaTitulo')) {
            document.getElementById('modalNotaTitulo').innerHTML = '<i class="fas fa-edit me-2"></i>Editar Nota';
        }
        
        if (document.getElementById('nota_id_editar')) document.getElementById('nota_id_editar').value = nota.ID_Nota;
        if (document.getElementById('nota_titulo')) document.getElementById('nota_titulo').value = decodeHTMLEntities(nota.Titulo);
        if (document.getElementById('nota_contenido')) document.getElementById('nota_contenido').value = decodeHTMLEntities(nota.Contenido || '');
        if (document.getElementById('nota_color')) document.getElementById('nota_color').value = nota.Color_Etiqueta || '#1A2D40';
        
        const btnEliminar = document.getElementById('btn-nota-eliminar');
        if (btnEliminar) btnEliminar.classList.remove('d-none');
        
        seleccionarColor(nota.Color_Etiqueta || '#1A2D40');
        validarFormNota();
        
        if (modalNotaBS) modalNotaBS.show();
    }

    // ============================================================
    // SELECCIONAR COLOR
    // ============================================================
    function seleccionarColor(hex) {
        document.getElementById('nota_color').value = hex;
        document.querySelectorAll('.btn-color-nota').forEach(btn => {
            btn.classList.remove('activo');
            if (btn.dataset.color === hex) btn.classList.add('activo');
        });
    }

    // ============================================================
    // VALIDACIÓN DEL FORM DE NOTA
    // ============================================================
    function validarFormNota() {
        const titulo = document.getElementById('nota_titulo').value.trim();
        const btnGuardar = document.getElementById('btn-nota-guardar');
        const errTitulo = document.getElementById('nota_titulo_error');

        if (titulo === '') {
            errTitulo.classList.remove('d-none');
            btnGuardar.disabled = true;
            btnGuardar.style.opacity = '0.5';
        } else {
            errTitulo.classList.add('d-none');
            btnGuardar.disabled = false;
            btnGuardar.style.opacity = '1';
        }
    }

    // ============================================================
    // GUARDAR NOTA (crear o actualizar)
    // ============================================================
    function guardarNota() {
        const id_nota = document.getElementById('nota_id_editar').value;
        const titulo = document.getElementById('nota_titulo').value.trim();
        const contenido = document.getElementById('nota_contenido').value.trim();
        const color = document.getElementById('nota_color').value;

        if (!titulo) { validarFormNota(); return; }

        const action = id_nota ? 'actualizar' : 'crear';
        const formData = new FormData();
        formData.append('action', action);
        formData.append('titulo', titulo);
        formData.append('contenido', contenido);
        formData.append('color', color);
        if (id_nota) formData.append('id_nota', id_nota);

        const btnGuardar = document.getElementById('btn-nota-guardar');
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Guardando...';

        fetch(NOTAS_API_URL, { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = '<i class="fas fa-save me-1"></i>Guardar';
                if (data.exito) {
                    modalNotaBS.hide();
                    cargarNotas();
                } else {
                    alert(data.mensaje || 'Error al guardar la nota.');
                }
            })
            .catch(() => {
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = '<i class="fas fa-save me-1"></i>Guardar';
                alert('Error de conexión. Inténtalo de nuevo.');
            });
    }

    // ============================================================
    // ELIMINAR NOTA INDIVIDUAL (desde el modal de edición)
    // ============================================================
    function eliminarNotaActual() {
        const id_nota = document.getElementById('nota_id_editar').value;
        if (!id_nota) return;

        // Usar SweetAlert2 si está disponible, si no, usar confirm nativo como fallback
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '¿Eliminar esta nota?',
                html: 'Esta acción <strong>no se puede deshacer</strong>.<br>La nota será borrada permanentemente.',
                icon: 'warning',
                iconColor: '#dc3545',
                showCancelButton: true,
                confirmButtonColor: '#8a0a2a',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash-alt me-1"></i> Sí, eliminar',
                cancelButtonText: '<i class="fas fa-times me-1"></i> Cancelar',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    popup: 'shadow-lg',
                    confirmButton: 'fw-bold',
                    cancelButton: 'fw-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    _ejecutarEliminarNota(id_nota);
                }
            });
        } else {
            if (!confirm('¿Estás seguro de que deseas eliminar esta nota? Esta acción no se puede deshacer.')) return;
            _ejecutarEliminarNota(id_nota);
        }
    }

    function _ejecutarEliminarNota(id_nota) {
        const formData = new FormData();
        formData.append('action', 'eliminar');
        formData.append('id_nota', id_nota);

        fetch(NOTAS_API_URL, { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.exito) {
                    modalNotaBS.hide();
                    cargarNotas();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Nota eliminada!',
                            text: 'La nota fue borrada correctamente.',
                            timer: 1800,
                            showConfirmButton: false,
                            iconColor: '#16A34A'
                        });
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.mensaje || 'Error al eliminar la nota.', confirmButtonColor: '#1A2D40' });
                    } else {
                        alert(data.mensaje || 'Error al eliminar la nota.');
                    }
                }
            })
            .catch(() => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Sin conexión', text: 'Error de conexión. Inténtalo de nuevo.', confirmButtonColor: '#1A2D40' });
                } else {
                    alert('Error de conexión. Inténtalo de nuevo.');
                }
            });
    }

    // ============================================================
    // MODAL ELIMINAR TODAS
    // ============================================================
    function abrirModalEliminarTodas() {
        const modalEl = document.getElementById('modalEliminarTodas');
        if (!modalEliminarTodasBS && modalEl && typeof bootstrap !== 'undefined') {
            modalEliminarTodasBS = new bootstrap.Modal(modalEl);
        }

        if (document.getElementById('eliminar_todas_pass')) document.getElementById('eliminar_todas_pass').value = '';
        if (document.getElementById('msg-eliminar-todas')) {
            document.getElementById('msg-eliminar-todas').classList.add('d-none');
            document.getElementById('msg-eliminar-todas').textContent = '';
        }
        
        const icoPass = document.getElementById('ico-pass-eliminar');
        const passInput = document.getElementById('eliminar_todas_pass');
        if (icoPass && passInput) {
            icoPass.className = 'fas fa-eye';
            passInput.type = 'password';
        }
        
        if (modalEliminarTodasBS) modalEliminarTodasBS.show();
    }

    function togglePassEliminarTodas() {
        const inp = document.getElementById('eliminar_todas_pass');
        const ico = document.getElementById('ico-pass-eliminar');
        if (inp.type === 'password') {
            inp.type = 'text';
            ico.className = 'fas fa-eye-slash';
        } else {
            inp.type = 'password';
            ico.className = 'fas fa-eye';
        }
    }

    function confirmarEliminarTodas() {
        const password = document.getElementById('eliminar_todas_pass').value;
        const msgEl = document.getElementById('msg-eliminar-todas');
        const btnConf = document.getElementById('btn-confirmar-eliminar-todas');

        if (!password) {
            msgEl.textContent = 'Debes ingresar tu contraseña para confirmar.';
            msgEl.classList.remove('d-none');
            return;
        }

        btnConf.disabled = true;
        btnConf.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Eliminando...';

        const formData = new FormData();
        formData.append('action', 'eliminarTodas');
        formData.append('password', password);

        fetch(NOTAS_API_URL, { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                btnConf.disabled = false;
                btnConf.innerHTML = '<i class="fas fa-trash-alt me-1"></i>Eliminar Todo';

                if (data.exito) {
                    modalEliminarTodasBS.hide();
                    cargarNotas();
                } else {
                    msgEl.textContent = data.mensaje || 'Error al eliminar.';
                    msgEl.classList.remove('d-none');
                    // Limpiar el campo de contraseña al fallar
                    document.getElementById('eliminar_todas_pass').value = '';
                }
            })
            .catch(() => {
                btnConf.disabled = false;
                btnConf.innerHTML = '<i class="fas fa-trash-alt me-1"></i>Eliminar Todo';
                msgEl.textContent = 'Error de conexión. Inténtalo de nuevo.';
                msgEl.classList.remove('d-none');
            });
    }
</script>