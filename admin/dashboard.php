<?php
session_start();
require_once __DIR__.'/../includes/config.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Récupération des données
try {
    // Données utilisateur
    $user_stmt = $db->prepare("SELECT name, email, entreprise, ville FROM users WHERE id = ?");
    $user_stmt->execute([$_SESSION['user_id']]);
    $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

    // Statistiques
    $stats_stmt = $db->prepare("
        SELECT 
            COUNT(id) as product_count,
            (SELECT COUNT(id) FROM orders WHERE seller_id = ?) as order_count,
            (SELECT SUM(total_amount) FROM orders WHERE seller_id = ?) as total_sales
        FROM products 
        WHERE user_id = ?
    ");
    $stats_stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
    $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

    // Dernières commandes
    $orders_stmt = $db->prepare("
        SELECT o.id, o.order_date, o.total_amount, o.status, u.name as customer_name
        FROM orders o
        JOIN users u ON o.buyer_id = u.id
        WHERE o.seller_id = ?
        ORDER BY o.order_date DESC
        LIMIT 5
    ");
    $orders_stmt->execute([$_SESSION['user_id']]);
    $orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

require_once __DIR__.'/../includes/header-dashboard.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
            <div class="sidebar-sticky pt-3">
                <div class="sidebar-header text-center mb-4">
                    <img src="../assets/img/logo-white.png" alt="e-Yombna" class="sidebar-logo">
                    <h5 class="text-white mt-2">Espace Vendeur</h5>
                </div>
                
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">
                            <i class="fas fa-boxes mr-2"></i>
                            Mes produits
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Commandes
                            <span class="badge badge-pill badge-primary float-right"><?= $stats['order_count'] ?? 0 ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="customers.php">
                            <i class="fas fa-users mr-2"></i>
                            Clients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">
                            <i class="fas fa-chart-line mr-2"></i>
                            Statistiques
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">
                            <i class="fas fa-cog mr-2"></i>
                            Paramètres
                        </a>
                    </li>
                </ul>
                
                <div class="sidebar-footer mt-auto">
                    <a href="../logout.php" class="btn btn-danger btn-block">
                        <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Tableau de bord</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group mr-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">Partager</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">Exporter</button>
                    </div>
                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#addProductModal">
                        <i class="fas fa-plus mr-1"></i> Nouveau produit
                    </button>
                </div>
            </div>

            <!-- Welcome Card -->
            <div class="card bg-light mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="text-primary">Bienvenue, <?= htmlspecialchars($user['name']) ?> !</h4>
                            <p class="mb-0">Vous gérez <strong><?= htmlspecialchars($user['entreprise']) ?></strong> depuis <strong><?= htmlspecialchars($user['ville']) ?></strong>.</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <img src="../assets/img/dashboard-welcome.png" alt="Welcome" class="img-fluid" style="max-height: 100px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Produits</h6>
                                    <h2 class="mb-0"><?= $stats['product_count'] ?? 0 ?></h2>
                                </div>
                                <i class="fas fa-boxes fa-3x"></i>
                            </div>
                        </div>
                        <div class="card-footer bg-primary-dark">
                            <a href="products.php" class="text-white">Voir tous <i class="fas fa-arrow-right ml-1"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Commandes</h6>
                                    <h2 class="mb-0"><?= $stats['order_count'] ?? 0 ?></h2>
                                </div>
                                <i class="fas fa-shopping-cart fa-3x"></i>
                            </div>
                        </div>
                        <div class="card-footer bg-success-dark">
                            <a href="orders.php" class="text-white">Voir toutes <i class="fas fa-arrow-right ml-1"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Chiffre d'affaires</h6>
                                    <h2 class="mb-0"><?= number_format($stats['total_sales'] ?? 0, 2) ?> FCFA</h2>
                                </div>
                                <i class="fas fa-chart-line fa-3x"></i>
                            </div>
                        </div>
                        <div class="card-footer bg-info-dark">
                            <a href="reports.php" class="text-white">Voir détails <i class="fas fa-arrow-right ml-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Dernières commandes</h5>
                        <a href="orders.php" class="btn btn-sm btn-outline-primary">Voir tout</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($orders)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>N° Commande</th>
                                        <th>Client</th>
                                        <th>Date</th>
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
                                        <td><?= date('d/m/Y', strtotime($order['order_date'])) ?></td>
                                        <td><?= number_format($order['total_amount'], 2) ?> FCFA</td>
                                        <td>
                                            <span class="badge badge-<?= 
                                                $order['status'] === 'completed' ? 'success' : 
                                                ($order['status'] === 'processing' ? 'warning' : 'secondary')
                                            ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="order-details.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
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

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Actions rapides</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <a href="add-product.php" class="btn btn-block btn-outline-primary py-3">
                                        <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
                                        Ajouter produit
                                    </a>
                                </div>
                                <div class="col-6 mb-3">
                                    <a href="products.php" class="btn btn-block btn-outline-success py-3">
                                        <i class="fas fa-boxes fa-2x mb-2"></i><br>
                                        Gérer produits
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="reports.php" class="btn btn-block btn-outline-info py-3">
                                        <i class="fas fa-chart-pie fa-2x mb-2"></i><br>
                                        Voir stats
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="settings.php" class="btn btn-block btn-outline-secondary py-3">
                                        <i class="fas fa-cog fa-2x mb-2"></i><br>
                                        Paramètres
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Statut du compte</h5>
                        </div>
                        <div class="card-body">
                            <div class="progress mb-3">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p class="mb-2"><strong>Profil complété à 75%</strong></p>
                            <p class="text-muted small mb-0">Complétez votre profil pour améliorer votre visibilité sur la plateforme.</p>
                            <a href="profile.php" class="btn btn-sm btn-primary mt-2">Compléter profil</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal Ajout Produit -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Ajouter un nouveau produit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="quickAddProductForm">
                    <div class="form-group">
                        <label for="productName">Nom du produit</label>
                        <input type="text" class="form-control" id="productName" required>
                    </div>
                    <div class="form-group">
                        <label for="productPrice">Prix (FCFA)</label>
                        <input type="number" class="form-control" id="productPrice" required>
                    </div>
                    <div class="form-group">
                        <label for="productCategory">Catégorie</label>
                        <select class="form-control" id="productCategory" required>
                            <option value="">Sélectionner...</option>
                            <option>Alimentation</option>
                            <option>Artisanat</option>
                            <option>Textile</option>
                            <option>Cosmétique</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__.'/../includes/footer-dashboard.php'; ?>

<script>
// Activer le tooltip
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

// Toggle sidebar sur mobile
$('#sidebarCollapse').on('click', function () {
    $('#sidebar').toggleClass('active');
});
</script>