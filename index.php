<?php
include('php/conexion.php'); 
include('includes/header.php'); // El header ya no abre el div.container si es index.php

// Consultamos TODOS los menús
$sql = "SELECT id_menu, nombre_menu, descripcion_menu, foto_menu, precio_menu 
        FROM menus 
        ORDER BY nombre_menu ASC";
$result = $conexion->query($sql);
?>

<div class="hero-section text-center">
    <div class="container">
        <img src="<?php echo $base_url; ?>img/arbol.png" alt="Logo El Patio" style="width: 100px; margin-bottom: 20px;">
        <h1 class="display-3 fw-bold">Bienvenido a El Patio</h1>
        <p class="lead fs-4">Sabor y tradición en cada plato. Haz tu pedido online.</p>
        <a href="#menu-del-dia" class="btn btn-success btn-lg mt-3">Ver Menú</a>
    </div>
</div>

<div class="container py-5" id="menu-del-dia">
    
    <div class="text-center mb-5">
        <h2 class="display-4">Nuestro Menú</h2>
    </div>

    <div class="row g-4">
        
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($menu = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 product-card">
                        
                        <img src="<?php echo htmlspecialchars($menu['foto_menu']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($menu['nombre_menu']); ?>">
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($menu['nombre_menu']); ?></h5>
                            <p class="card-text text-muted small"><?php echo htmlspecialchars($menu['descripcion_menu']); ?></p>
                            
                            <h4 class="text-success mt-auto mb-3">$<?php echo number_format($menu['precio_menu'], 0, ',', '.'); ?></h4>
                            
                            <form class="form-add-to-cart">
                                <input type="hidden" name="id_menu" value="<?php echo $menu['id_menu']; ?>">
                                <div class="input-group">
                                    <input type="number" name="cantidad" class="form-control" value="1" min="1" max="10">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-cart-plus"></i> Añadir
                                    </button>
                                </div>
                            </form>
                            
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    No hay menús disponibles en este momento.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Instancia del Toast (¡Ahora sí encontrará el HTML!)
    var toastLiveExample = document.getElementById('liveToast');
    var toast = new bootstrap.Toast(toastLiveExample);
    var toastBody = document.getElementById('toast-message-body');

    var forms = document.querySelectorAll('.form-add-to-cart');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            
            e.preventDefault(); 
            var formData = new FormData(form);
            var button = form.querySelector('button[type="submit"]');
            var originalButtonText = button.innerHTML;

            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

            var fetchUrl = '<?php echo $base_url; ?>ajax_carrito.php';
            
            fetch(fetchUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text()) 
            .then(text => {
                
                try {
                    // Intentamos 'parsear' el JSON
                    const data = JSON.parse(text.trim()); 
                    
                    if (data.status === 'success') {
                        var cartBadge = document.getElementById('cart-count-badge');
                        if (cartBadge) {
                            cartBadge.textContent = data.nuevo_total_items;
                        }
                        
                        // Esta línea es la que fallaba (TypeError)
                        // Ahora 'toastBody' no será 'null'
                        toastBody.textContent = data.message; 
                        toast.show();

                    } else {
                        alert('Error (Respuesta JSON): ' + data.message);
                    }
                    
                } catch (e) {
                    // ¡¡MENSAJE DE ERROR CORREGIDO!!
                    // 'e' es el TypeError, 'text' es el JSON que recibimos
                    console.error("¡Error en el bloque TRY!", e, text);
                    alert('Hubo un error inesperado al procesar la respuesta.');
                }
                
                // Reactivar el botón
                button.disabled = false;
                button.innerHTML = originalButtonText;
            })
            .catch(error => {
                // Este es un error de red (ej: 404 o 500)
                console.error('Error de Fetch (red):', error);
                alert('Hubo un error de conexión con el servidor.');
                button.disabled = false;
                button.innerHTML = originalButtonText;
            });
        });
    });
});
</script>
<?php
// Nota: El footer.php debe ser modificado ligeramente.
// Debe cerrar el </main> pero NO el </div> (ya que no lo abrimos aquí)
include('includes/footer.php'); 
?>