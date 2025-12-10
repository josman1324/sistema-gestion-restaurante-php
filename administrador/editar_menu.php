<?php
include('../php/config.php');
include('../php/conexion.php');
if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) {
    header("Location: " . $base_url . "login_universal.php");
    exit;
}
$id_menu = $_GET['id'] ?? 0;
if ($id_menu <= 0) { die("ID de menú no válido."); }
$sql = "SELECT * FROM menus WHERE id_menu = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_menu);
$stmt->execute();
$menu = $stmt->get_result()->fetch_assoc();
if (!$menu) { die("Menú no encontrado."); }
$sql_dias = "SELECT id_menu_dia FROM menu_dias WHERE id_menu = ?";
$stmt_dias = $conexion->prepare($sql_dias);
$stmt_dias->bind_param("i", $id_menu);
$stmt_dias->execute();
$result_dias = $stmt_dias->get_result();
$dias_asignados = [];
while ($fila = $result_dias->fetch_assoc()) {
    $dias_asignados[] = $fila['id_menu_dia'];
}
$stmt_dias->close();
include('../includes/header.php');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="display-5"><i class="bi bi-pencil-square"></i> Editar Menú</h1>
    <a href="gestion_menus.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
</div>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="guardar_menu.php" method="POST">
                    <input type="hidden" name="id_menu" value="<?php echo $menu['id_menu']; ?>">
                    <div class="mb-3">
                        <label for="nombre_menu" class="form-label fw-bold">Nombre del Menú</label>
                        <input type="text" class="form-control" id="nombre_menu" name="nombre_menu" value="<?php echo htmlspecialchars($menu['nombre_menu']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion_menu" class="form-label fw-bold">Descripción</label>
                        <textarea class="form-control" id="descripcion_menu" name="descripcion_menu" rows="3"><?php echo htmlspecialchars($menu['descripcion_menu']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="precio_menu" class="form-label fw-bold">Precio</label>
                        <input type="number" class="form-control" id="precio_menu" name="precio_menu" min="0" value="<?php echo $menu['precio_menu']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="foto_menu" class="form-label fw-bold">URL de la Foto</label>
                        <input type="text" class="form-control" id="foto_menu" name="foto_menu" value="<?php echo htmlspecialchars($menu['foto_menu']); ?>" placeholder="https://ejemplo.com/imagen.jpg">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Días Disponibles (para el Mesero)</label>
                        <div class="d-flex justify-content-between flex-wrap">
                            <?php 
                            // ¡¡ESTE ES EL ARREGLO!!
                            // Usamos el estándar de tu BD: 1=Domingo, 2=Lunes, 3=Martes...
                            $dias = [
                                2 => 'Lunes', 
                                3 => 'Martes', 
                                4 => 'Miércoles', 
                                5 => 'Jueves', 
                                6 => 'Viernes', 
                                7 => 'Sábado', 
                                1 => 'Domingo'
                            ];
                            foreach ($dias as $id_dia_key => $nombre_dia):
                                $checked = in_array($id_dia_key, $dias_asignados) ? 'checked' : '';
                            ?>
                                <div class="form-check form-check-inline m-1">
                                    <input class="form-check-input" type="checkbox" name="dias[]" value="<?php echo $id_dia_key; ?>" id="dia-<?php echo $id_dia_key; ?>" <?php echo $checked; ?>>
                                    <label class="form-check-label" for="dia-<?php echo $id_dia_key; ?>"><?php echo $nombre_dia; ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-success btn-lg">Actualizar Menú</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php 
$stmt->close();
$conexion->close();
include('../includes/footer.php'); 
?>