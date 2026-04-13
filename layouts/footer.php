<footer class="footer mt-5">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-12 mb-3">

                    <a href="<?= URL_BASE ?>/privacidad" class="text-white text-decoration-none mx-2">Política de Privacidad</a>
                    <span class="opacity-50">|</span>
                    <a href="<?= URL_BASE ?>/terminos" class="text-white text-decoration-none mx-2">Términos de Uso</a>
                </div>
            </div>
            
            <hr class="bg-white opacity-25">
            
            <p class="mb-2">&copy; 2026 DocuHuella - Todos los derechos reservados.</p>
            <p class="small opacity-50">
                <i class="fas fa-map-marker-alt me-1"></i> Barahona, República Dominicana<br>
                Desarrollado para UCATEBA
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function crearHuellaHero() {
            const hero = document.getElementById('hero-animated');
            if(!hero) return;

            const huella = document.createElement('i');
            huella.classList.add('fas', 'fa-paw', 'huella-hero');

            huella.style.left = Math.random() * 100 + '%';
            const tamaño = Math.random() * 2 + 1;
            huella.style.fontSize = tamaño + 'rem';
            const duracion = Math.random() * 6 + 4;
            huella.style.animationDuration = duracion + 's';

            hero.appendChild(huella);
            setTimeout(() => { huella.remove(); }, duracion * 1000);
        }
        setInterval(crearHuellaHero, 900);
    </script>
</body>
</html>