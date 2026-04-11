<?php
require_once 'config/db.php'; 
include_once 'layouts/header.php'; 
?>

    <header class="hero text-center" id="hero-animated">
        <div class="container hero-content">
            <h1 class="display-3 fw-bold" style="color: var(--dh-navy);">DocuHuella</h1>
            <p class="lead fs-4 mb-5 text-dark">La huella digital que cada mascota necesita para una vida saludable.</p>
            
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="views/registro.php" class="btn-dh btn-lg">Comenzar Ahora</a>
                <div class="d-flex align-items-center">
                    <i class="fas fa-paw fs-3" style="color: var(--dh-navy);"></i>
                </div>
                <a href="views/login.php" class="btn-dh btn-lg">Iniciar Sesión</a>
            </div>
        </div>
    </header>

    <section id="beneficios" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold" style="color: var(--dh-navy);">¿Por qué elegir DocuHuella?</h2>
                <div class="mx-auto" style="width: 60px; height: 4px; background-color: var(--dh-beige);"></div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 p-4 benefit-card text-center">
                        <div class="icon-box"><i class="fas fa-heart"></i></div>
                        <h4 class="fw-bold">Para Cuidadores</h4>
                        <p class="text-muted">Ten el historial clínico de tu mejor amigo siempre a mano. Mantén el control total de sus vacunas y tratamientos.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 p-4 benefit-card text-center">
                        <div class="icon-box"><i class="fas fa-user-md"></i></div>
                        <h4 class="fw-bold">Para Veterinarios</h4>
                        <p class="text-muted">Optimiza tus consultas con expedientes digitales rápidos. Olvídate del papel y accede a la información clínica desde cualquier dispositivo.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 p-4 benefit-card text-center">
                        <div class="icon-box"><i class="fas fa-hospital-user"></i></div>
                        <h4 class="fw-bold">Para Clínicas</h4>
                        <p class="text-muted">Gestión de inventarios y estadísticas. Mejora la eficiencia de tu negocio con datos precisos en tiempo real.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container text-center">
            <div class="row align-items-center">
                <div class="col-md-6 text-start">
                    <h3 class="fw-bold" style="color: var(--dh-navy);">Seguridad y Rapidez</h3>
                    <p>En DocuHuella protegemos cada dato. Con nuestro sistema de bloqueo de seguridad, la información está blindada.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success me-2"></i> Acceso 24/7 </li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> Reportes clínicos instantáneos</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> Interfaz intuitiva y moderna</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <img src="public/images/post.png" alt="Pet Care" class="img-fluid rounded-4 shadow">
                </div>
            </div>
        </div>
    </section>

<?php include_once 'layouts/footer.php'; ?>