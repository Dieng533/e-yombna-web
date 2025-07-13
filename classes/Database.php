<?php
// Démarrage de session sécurisé
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400,
        'cookie_secure' => true,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict'
    ]);
}

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Vérification de l'authentification et du rôle
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    header('Location: ' . SITE_URL . '/pages/login.php?redirect=dashboard');
    exit;
}

// Récupération des données du vendeur
$seller_id = $_SESSION['user_id'];
$seller_name = htmlspecialchars($_SESSION['user_name']);

// Fonction pour récupérer les statistiques
function getSellerStats($db, $seller_id) {
    try {
        $stmt = $db->prepare("
            SELECT 
                COUNT(p.id) as total_products,
                SUM(p.views) as total_views,
                (SELECT COUNT(id) FROM orders WHERE seller_id = ?) as total_orders,
                (SELECT SUM(total_amount) FROM orders WHERE seller_id = ?) as total_revenue
            FROM products p 
            WHERE p.user_id = ?
        ");
        $stmt->execute([$seller_id, $seller_id, $seller_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Erreur stats vendeur: " . $e->getMessage());
        return [
            'total_products' => 0,
            'total_views' => 0, 
            'total_orders' => 0,
            'total_revenue' => 0
        ];
    }
}

// Fonction pour récupérer les commandes récentes
function getRecentOrders($db, $seller_id, $limit = 5) {
    try {
        $stmt = $db->prepare("
            SELECT 
                o.id, 
                o.order_date, 
                o.status, 
                p.name as product_name, 
                o.total_amount,
                u.full_name as customer_name,
                o.payment_method
            FROM orders o
            JOIN products p ON o.product_id = p.id
            JOIN users u ON o.buyer_id = u.id
            WHERE p.user_id = ?
            ORDER BY o.order_date DESC
            LIMIT ?
        ");
        $stmt->execute([$seller_id, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Erreur commandes vendeur: " . $e->getMessage());
        return [];
    }
}

// Récupération des données
$stats = getSellerStats($db, $seller_id);
$orders = getRecentOrders($db, $seller_id);

// Préparation des données pour le graphique
$chart_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('M', strtotime("-$i months"));
    $chart_data['labels'][] = $month;
    $chart_data['data'][] = rand(10, 50); // À remplacer par des données réelles
}

// Définition du titre de la page
$page_title = "Tableau de bord - " . SITE_NAME;
require_once '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    
    <!-- CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Icons Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- CSS Personnalisé -->
    <link href="<?= SITE_URL ?>/assets/css/dashboard.css" rel="stylesheet">
    
    <style>
        .stat-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.5rem;
        }
        .sidebar-menu li a {
            transition: all 0.3s ease;
        }
        .sidebar-menu li.active a {
            font-weight: 600;
            background-color: rgba(255,255,255,0.1);
        }
    </style>
</head>
<body class="dashboard-body">
    <!-- Sidebar -->
    <div class="dashboard-sidebar bg-dark">
        <div class="sidebar-header text-center py-4">
            <img src="<?= SITE_URL ?>/assets/images/logo-white.png" alt="<?= SITE_NAME ?>" class="sidebar-logo">
            <h4 class="text-white mt-3">Espace Vendeur</h4>
        </div>
        <ul class="sidebar-menu">
            <li class="active">
                <a href="dashboard.php">
                    <i class="bi bi-speedometer2"></i>
                    <span>Tableau de bord</span>
                </a>
            </li>
            <li>
                <a href="products.php">
                    <i class="bi bi-box-seam"></i>
                    <span>Mes produits</span>
                    <span class="badge bg-primary float-end"><?= $stats['total_products'] ?></span>
                </a>
            </li>
            <li>
                <a href="orders.php">
                    <i class="bi bi-cart-check"></i>
                    <span>Commandes</span>
                    <span class="badge bg-primary float-end"><?= $stats['total_orders'] ?></span>
                </a>
            </li>
            <li>
                <a href="sales.php">
                    <i class="bi bi-graph-up"></i>
                    <span>Ventes</span>
                </a>
            </li>
            <li>
                <a href="messages.php">
                    <i class="bi bi-envelope"></i>
                    <span>Messages</span>
                    <span class="badge bg-primary float-end">3</span>
                </a>
            </li>
            <li>
                <a href="settings.php">
                    <i class="bi bi-gear"></i>
                    <span>Paramètres</span>
                </a>
            </li>
            <li class="logout-link mt-auto">
                <a href="<?= SITE_URL ?>/pages/logout.php" class="text-danger">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Déconnexion</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="dashboard-content">
        <!-- Top Navigation -->
        <nav class="dashboard-navbar navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container-fluid">
                <button class="sidebar-toggle btn btn-link d-lg-none">
                    <i class="bi bi-list"></i>
                </button>
                
                <div class="d-flex align-items-center ms-auto">
                    <div class="dropdown">
                        <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" id="userDropdown" data-bs-toggle="dropdown">
                            <div class="me-2 text-end d-none d-md-block">
                                <div class="fw-bold"><?= $seller_name ?></div>
                                <div class="small text-muted">Vendeur</div>
                            </div>
                            <i class="bi bi-person-circle fs-4"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Mon compte</h6></li>
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profil</a></li>
                            <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear me-2"></i>Paramètres</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= SITE_URL ?>/pages/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Dashboard Content -->
        <div class="container-fluid py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Tableau de bord</h2>
                <div class="text-muted"><?= date('d/m/Y') ?></div>
            </div>
            
            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card bg-primary text-white">
                        <div class="card-body text-center">
                            <div class="stat-icon bg-white text-primary">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <h3 class="mb-1"><?= $stats['total_products'] ?></h3>
                            <p class="stat-label mb-0">Produits en ligne</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card bg-success text-white">
                        <div class="card-body text-center">
                            <div class="stat-icon bg-white text-success">
                                <i class="bi bi-cart-check"></i>
                            </div>
                            <h3 class="mb-1"><?= $stats['total_orders'] ?></h3>
                            <p class="stat-label mb-0">Commandes</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card bg-info text-white">
                        <div class="card-body text-center">
                            <div class="stat-icon bg-white text-info">
                                <i class="bi bi-eye"></i>
                            </div>
                            <h3 class="mb-1"><?= number_format($stats['total_views']) ?></h3>
                            <p class="stat-label mb-0">Vues totales</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card bg-warning text-dark">
                        <div class="card-body text-center">
                            <div class="stat-icon bg-white text-warning">
                                <i class="bi bi-currency-exchange"></i>
                            </div>
                            <h3 class="mb-1"><?= number_format($stats['total_revenue'] ?? 0, 0, ',', ' ') ?> FCFA</h3>
                            <p class="stat-label mb-0">Chiffre d'affaires</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders and Stats -->
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-white border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Dernières commandes</h5>
                                <a href="orders.php" class="btn btn-sm btn-outline-primary">Voir tout</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($orders)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>N°</th>
                                                <th>Client</th>
                                                <th>Produit</th>
                                                <th>Montant</th>
                                                <th>Statut</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td>#<?= $order['id'] ?></td>
                                                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                                <td><?= htmlspecialchars($order['product_name']) ?></td>
                                                <td><?= number_format($order['total_amount'], 0, ',', ' ') ?> FCFA</td>
                                                <td>
                                                    <span class="badge rounded-pill bg-<?= 
                                                        $order['status'] === 'completed' ? 'success' : 
                                                        ($order['status'] === 'processing' ? 'warning' : 
                                                        ($order['status'] === 'cancelled' ? 'danger' : 'secondary')) 
                                                    ?>">
                                                        <?= ucfirst($order['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="order-details.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary" title="Détails">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info mb-0">Aucune commande récente</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0">Statistiques des ventes</h5>
                        </div>
                        <div class="card-body">
                            <div id="salesChart" style="height: 300px;">
                                <canvas></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0">Actions rapides</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="add-product.php" class="btn btn-success">
                                    <i class="bi bi-plus-lg me-2"></i>Ajouter un produit
                                </a>
                                <a href="products.php" class="btn btn-outline-primary">
                                    <i class="bi bi-box-seam me-2"></i>Gérer les produits
                                </a>
                                <a href="sales.php" class="btn btn-outline-info">
                                    <i class="bi bi-graph-up me-2"></i>Statistiques
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Toggle sidebar sur mobile
        document.querySelector('.sidebar-toggle').addEventListener('click', function() {
            document.querySelector('.dashboard-sidebar').classList.toggle('active');
        });

        // Graphique des ventes
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.querySelector('#salesChart canvas').getContext('2d');
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($chart_data['labels']) ?>,
                    datasets: [{
                        label: 'Ventes mensuelles',
                        data: <?= json_encode($chart_data['data']) ?>,
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: '#fff',
                            titleColor: '#212529',
                            bodyColor: '#212529',
                            borderColor: '#dee2e6',
                            borderWidth: 1,
                            padding: 12,
                            usePointStyle: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            },
                            ticks: {
                                stepSize: 10
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>