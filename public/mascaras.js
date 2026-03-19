// Este script formatea automáticamente las cédulas, teléfonos y RNC en todo el sistema.

document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Máscara Global para Teléfonos (Formato: 809-000-0000)
    // Se activa en cualquier input que tenga la clase "mascara-telefono"
    document.querySelectorAll('.mascara-telefono').forEach(function(input) {
        input.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    });

    // 2. Máscara Global para Cédulas (Formato: 000-0000000-0)
    // Se activa en cualquier input que tenga la clase "mascara-cedula"
    document.querySelectorAll('.mascara-cedula').forEach(function(input) {
        input.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,7})(\d{0,1})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    });

    // 3. Máscara Global para RNC (Solo números)
    // Se activa en cualquier input que tenga la clase "mascara-rnc"
    document.querySelectorAll('.mascara-rnc').forEach(function(input) {
        input.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, ''); // Borra todo lo que no sea número
        });
    });

});