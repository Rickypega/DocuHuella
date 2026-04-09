<h1>Listado de Sucursales</h1>

<a href="../../views/admin/crear_sucursal.php">
    Crear Nueva Sucursal
</a>

<br><br>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Dirección</th>
    </tr>

    <?php if (!empty($sucursales)): ?>
        <?php foreach($sucursales as $s): ?>
            <tr>
                <td><?= $s['ID_Clinica'] ?></td>
                <td><?= $s['Nombre_Sucursal'] ?></td>
                <td><?= $s['Direccion'] ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="3">No hay sucursales registradas</td>
        </tr>
    <?php endif; ?>

</table>
