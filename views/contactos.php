<?php
$titulo = "Contactos | DocuHuella";
include_once 'layouts/header.php';
?>

<style>
    .contact-container {
        position: relative;
        min-height: calc(100vh - 150px);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background-color: var(--dh-beige); /* Fondo crema */
    }

    /* Animación de huellas similar al Hero */
    .huella-contact {
        position: absolute;
        bottom: -50px;
        color: var(--dh-navy);
        opacity: 0.1;
        z-index: 1;
        animation: flotarContact linear forwards;
    }

    @keyframes flotarContact {
        0% { transform: translateY(0) rotate(0deg); opacity: 0; }
        10% { opacity: 0.1; }
        90% { opacity: 0.1; }
        100% { transform: translateY(-110vh) rotate(45deg); opacity: 0; }
    }

    .contact-card {
        position: relative;
        z-index: 10;
        background-color: var(--dh-navy);
        color: white;
        padding: 3rem;
        border-radius: 40px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        max-width: 900px;
        width: 95%;
    }

    .contact-card h2 {
        font-weight: 800;
        margin-bottom: 1.5rem;
        color: var(--dh-beige);
    }

    .contact-info-item {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        font-size: 1.1rem;
    }

    .contact-info-item i {
        margin-right: 15px;
        font-size: 1.3rem;
        color: var(--dh-beige);
    }

    .logo-side img {
        max-width: 100%;
        border-radius: 20px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
</style>

<div class="contact-container" id="contact-animated">
    <div class="contact-card">
        <div class="row align-items-center">
            <!-- Columna de Información -->
            <div class="col-lg-7 text-center text-lg-start">
                <h2 class="text-uppercase">Contacto</h2>
                <p class="opacity-75 mb-5">Estamos aquí para ayudarte a cuidar de tus compañeros de vida. Contáctanos por cualquier duda o soporte.</p>

                <div class="contact-info-item justify-content-center justify-content-lg-start">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Barahona, República Dominicana</span>
                </div>

                <div class="contact-info-item justify-content-center justify-content-lg-start">
                    <i class="fas fa-phone-alt"></i>
                    <span>+1 (849) 751-1720</span>
                </div>

                <div class="contact-info-item justify-content-center justify-content-lg-start">
                    <i class="fas fa-envelope"></i>
                    <span>rickypega48@gmail.com</span>
                </div>
                
                <div class="mt-4">
                    <a href="<?= URL_BASE ?>/" class="btn btn-outline-light rounded-pill px-4">Volver al Inicio</a>
                </div>
            </div>

            <!-- Columna del Logo -->
            <div class="col-lg-5 d-none d-lg-block logo-side">
                <img src="<?= URL_BASE ?>/public/images/DH.jpg" alt="DocuHuella Logo" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<script>
    function crearHuellaContacto() {
        const container = document.getElementById('contact-animated');
        if(!container) return;

        const huella = document.createElement('i');
        huella.classList.add('fas', 'fa-paw', 'huella-contact');

        // Posición aleatoria
        huella.style.left = Math.random() * 100 + '%';
        
        // Tamaño aleatorio
        const tamaño = Math.random() * 2 + 1;
        huella.style.fontSize = tamaño + 'rem';
        
        // Duración aleatoria
        const duracion = Math.random() * 6 + 4;
        huella.style.animationDuration = duracion + 's';

        container.appendChild(huella);

        // Limpiar elemento tras la animación
        setTimeout(() => {
            huella.remove();
        }, duracion * 1000);
    }

    // Iniciar la lluvia de huellas
    setInterval(crearHuellaContacto, 800);
</script>

<?php include_once 'layouts/footer.php'; ?>