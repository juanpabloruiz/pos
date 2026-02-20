<?php
require_once 'db.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        $nombre = trim($_POST['nombre'] ?? '');
        $detalle = trim($_POST['detalle'] ?? '');

        if ($nombre !== '' && $detalle !== '') {
            $stmt = $conexion->prepare('INSERT INTO productos (nombre, detalle) VALUES (?, ?)');
            $stmt->bind_param('ss', $nombre, $detalle);

            if ($stmt->execute()) {
                $mensaje = 'Producto agregado correctamente.';
            } else {
                $mensaje = 'No se pudo agregar el producto.';
            }

            $stmt->close();
        } else {
            $mensaje = 'Nombre y detalle son obligatorios.';
        }
    }

    if ($accion === 'actualizar') {
        $id = (int) ($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $detalle = trim($_POST['detalle'] ?? '');

        if ($id > 0 && $nombre !== '' && $detalle !== '') {
            $stmt = $conexion->prepare('UPDATE productos SET nombre = ?, detalle = ? WHERE id = ?');
            $stmt->bind_param('ssi', $nombre, $detalle, $id);

            if ($stmt->execute()) {
                $mensaje = 'Producto actualizado correctamente.';
            } else {
                $mensaje = 'No se pudo actualizar el producto.';
            }

            $stmt->close();
        } else {
            $mensaje = 'Datos inválidos para actualizar.';
        }
    }
}

if (isset($_GET['eliminar'])) {
    $idEliminar = (int) $_GET['eliminar'];

    if ($idEliminar > 0) {
        $stmt = $conexion->prepare('DELETE FROM productos WHERE id = ?');
        $stmt->bind_param('i', $idEliminar);

        if ($stmt->execute()) {
            $mensaje = 'Producto eliminado correctamente.';
        } else {
            $mensaje = 'No se pudo eliminar el producto.';
        }

        $stmt->close();
    }
}

$productoEditar = null;
if (isset($_GET['editar'])) {
    $idEditar = (int) $_GET['editar'];

    if ($idEditar > 0) {
        $stmt = $conexion->prepare('SELECT id, nombre, detalle FROM productos WHERE id = ?');
        $stmt->bind_param('i', $idEditar);
        $stmt->execute();
        $resultadoEditar = $stmt->get_result();
        $productoEditar = $resultadoEditar->fetch_assoc();
        $stmt->close();
    }
}

$resultado = $conexion->query('SELECT id, nombre, detalle, agregado FROM productos ORDER BY id DESC');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Productos - Tienda</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f7f7f7; }
        h1, h2 { color: #333; }
        .card { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        input, textarea { width: 100%; padding: 10px; margin-top: 6px; margin-bottom: 14px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 16px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-secondary { background: #16a34a; color: #fff; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f0f0f0; }
        .acciones a { margin-right: 8px; text-decoration: none; }
        .msg { padding: 10px; background: #e8f5e9; border: 1px solid #c8e6c9; color: #2e7d32; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>CRUD de Productos (LAMP + PHP + MySQLi OO)</h1>

    <?php if ($mensaje !== ''): ?>
        <div class="msg"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <div class="card">
        <h2><?= $productoEditar ? 'Editar producto' : 'Agregar producto' ?></h2>
        <form method="post" action="index.php">
            <input type="hidden" name="accion" value="<?= $productoEditar ? 'actualizar' : 'crear' ?>">
            <?php if ($productoEditar): ?>
                <input type="hidden" name="id" value="<?= (int) $productoEditar['id'] ?>">
            <?php endif; ?>

            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" required value="<?= htmlspecialchars($productoEditar['nombre'] ?? '') ?>">

            <label for="detalle">Detalle</label>
            <textarea id="detalle" name="detalle" rows="3" required><?= htmlspecialchars($productoEditar['detalle'] ?? '') ?></textarea>

            <button class="<?= $productoEditar ? 'btn-secondary' : 'btn-primary' ?>" type="submit">
                <?= $productoEditar ? 'Actualizar' : 'Guardar' ?>
            </button>
            <?php if ($productoEditar): ?>
                <a href="index.php" style="margin-left:10px;">Cancelar</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h2>Lista de productos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Detalle</th>
                    <th>Agregado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && $resultado->num_rows > 0): ?>
                    <?php while ($fila = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int) $fila['id'] ?></td>
                            <td><?= htmlspecialchars($fila['nombre']) ?></td>
                            <td><?= htmlspecialchars($fila['detalle']) ?></td>
                            <td><?= htmlspecialchars($fila['agregado']) ?></td>
                            <td class="acciones">
                                <a href="index.php?editar=<?= (int) $fila['id'] ?>">Editar</a>
                                <a href="index.php?eliminar=<?= (int) $fila['id'] ?>" onclick="return confirm('¿Seguro que quieres eliminar este producto?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No hay productos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
