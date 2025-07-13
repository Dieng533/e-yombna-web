<?php
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
?>

<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="sidebar-sticky pt-3">
        <div class="sidebar-header text-center mb-4">
            <img src="../assets/img/logo-white.png" alt="e-Yombna" class="sidebar-logo">
            <h5 class="text-white mt-2">Espace Vendeur</h5>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    Tableau de bord
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="add-product.php">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Ajouter produit
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