<?php
// 1. Incluimos nuestra nueva configuración.
include(__DIR__ . '/../php/config.php');

// 2. Calculamos el total de items en el carrito
$total_items_carrito = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $total_items_carrito += $item['cantidad'];
    }
}

// 3. Lógica de sesión de usuario (COMPLETA)
$nombre_usuario = null;
$panel_link = null;
$is_logged_in = false;

if (isset($_SESSION['admin'])) {
    $nombre_usuario = $_SESSION['admin'];
    $panel_link = $base_url . 'administrador/opciones_admin.php'; 
    $is_logged_in = true;
} elseif (isset($_SESSION['cajero'])) {
    $nombre_usuario = $_SESSION['cajero'];
    $panel_link = $base_url . 'caja/panel_caja.php';
    $is_logged_in = true;
} elseif (isset($_SESSION['id_trabajador'])) {
    $nombre_usuario = $_SESSION['nombre_usuario'];
    $panel_link = $base_url . 'mesero/panel.php';
    $is_logged_in = true;
} elseif (isset($_SESSION['id_cliente'])) {
    $nombre_usuario = $_SESSION['nombre_cliente'];
    $panel_link = $base_url . 'cliente/panel.php';
    $is_logged_in = true;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurante El Patio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 2rem 0;
            margin-top: auto; 
        }
        .product-card img {
            height: 220px;
            object-fit: cover;
            width: 100%;
        }
        .navbar-brand img {
            height: 40px;
        }
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4');
            background-size: cover;
            background-position: center;
            padding: 8rem 0;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.6);
        }
        .product-card {
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border: none;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .product-card .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .product-card .card-body .btn-success {
            margin-top: auto;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .toast-container {
            z-index: 1100;
        }
    </style>
</head>
<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
            <div class="container">
                <a class="navbar-brand" href="<?php echo $base_url; ?>index.php">
                    <img src="<?php echo $base_url; ?>img/arbol.png" alt="Logo El Patio" class="me-2">
                    Restaurante <strong>El Patio</strong>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo $base_url; ?>index.php"><i class="bi bi-shop"></i> Menú</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>cliente/carrito.php">
                                <i class="bi bi-cart"></i> Mi Carrito
                                <span id="cart-count-badge" class="badge bg-success ms-1 rounded-pill">
                                    <?php echo $total_items_carrito; ?>
                                </span>
                            </a>
                        </li>
                        
                        <?php if ($is_logged_in): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle"></i> Hola, <?php echo htmlspecialchars($nombre_usuario); ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="<?php echo $panel_link; ?>">Mi Panel</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $base_url; ?>logout_universal.php">Cerrar Sesión</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $base_url; ?>login_universal.php"><i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $base_url; ?>registro.php"><i class="bi bi-person-plus"></i> Registrarse</a>
                            </li>
                        <?php endif; ?>
                        
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong class="me-auto">¡Éxito!</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toast-message-body">
                </div>
        </div>
    </div>
    <main class="main-content <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'p-0' : 'py-5'; ?>">
        <?php if (basename($_SERVER['PHP_SELF']) != 'index.php'): ?>
            <div class="container">
        <?php endif; ?>