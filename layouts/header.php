<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocuHuella</title>
    <link rel="icon" href="<?= URL_BASE ?>/public/images/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --dh-beige: #EADAC1;
            --dh-navy: #1A2D40;
            --dh-white: #FFFFFF;
        }

        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: hidden; }

        .navbar { background-color: var(--dh-navy); z-index: 1000; }
        .navbar-brand { font-weight: bold; color: var(--dh-beige) !important; }

        /* Estilo del Hero y Animación de Huellas */
        .hero {
            background-color: var(--dh-beige);
            padding: 100px 0;
            clip-path: ellipse(150% 100% at 50% 0%);
            position: relative;
            overflow: hidden;
        }

        .hero-content { position: relative; z-index: 10; }

        .huella-hero {
            position: absolute;
            bottom: -50px;
            color: var(--dh-navy);
            opacity: 0.1;
            z-index: 1;
            animation: flotarHero linear forwards;
        }

        @keyframes flotarHero {
            0% { transform: translateY(0) rotate(0deg); opacity: 0; }
            10% { opacity: 0.1; }
            90% { opacity: 0.1; }
            100% { transform: translateY(-100vh) rotate(45deg); opacity: 0; }
        }

        .btn-dh {
            background-color: var(--dh-navy);
            color: white;
            border-radius: 30px;
            padding: 12px 30px;
            font-weight: bold;
            transition: 0.3s;
            text-decoration: none;
        }
        .btn-dh:hover { background-color: #2c4a63; color: white; transform: scale(1.05); }

        .benefit-card { border: none; border-radius: 20px; transition: 0.3s; background: #f8f9fa; }
        .benefit-card:hover { transform: translateY(-10px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .icon-box { font-size: 2.5rem; color: var(--dh-navy); margin-bottom: 15px; }
        .footer { background-color: var(--dh-navy); color: white; padding: 40px 0; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?= URL_BASE ?>/"><i class="fas fa-paw me-2"></i>DocuHuella</a>
        </div>
    </nav>