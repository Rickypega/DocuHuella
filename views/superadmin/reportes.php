<?php 
session_start();
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 4) { header("Location: ../login.php"); exit(); }
?>
<div class="main-content">
    <h2 class="fw-bold" style="color: #1A2D40;">Próximamente: Reportes Globales</h2>
    <p>Aquí podrás exportar PDF y Excel de todas los reportes.</p>
</div>