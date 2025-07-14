<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/header.php';

// Récupérer les produits avec les infos vendeurs
try {
    $stmt = $db->query("
        SELECT p.*, u.name as seller_name, u.phone as seller_phone 
        FROM products p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur: " . $e->getMessage());
}
?>

<link rel="stylesheet" href="assets/css/boutique.css">

<div class="container py-5 boutique-container">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show alert-animated mb-5">
            <?= $_SESSION['success_message'] ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <div class="section-title text-center mb-5">
        <h1>Notre Boutique</h1>
        <div class="title-divider"></div>
        <p class="lead">Découvrez des produits artisanaux de qualité</p>
    </div>
    
    <!-- Filtres -->
    <div class="row mb-4 filter-section">
        <div class="col-md-6">
            <input type="text" class="form-control" id="searchInput" placeholder="Rechercher un produit...">
        </div>
        <div class="col-md-6">
            <select class="form-control" id="categoryFilter">
                <option value="">Toutes catégories</option>
                <option value="Manuelles">Manuelles</option>
                <option value="Industrielle">Industrielle</option>
            </select>
        </div>
    </div>
    
    <div class="row" id="productsContainer">
        <?php foreach ($products as $product): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card product-card h-100">
                <span class="badge badge-primary category-badge">
                    <?= htmlspecialchars($product['category']) ?>
                </span>
                
                <div class="position-relative overflow-hidden">
                    <img src="<?= htmlspecialchars($product['image_path']) ?>" 
                         class="card-img-top product-image" 
                         alt="<?= htmlspecialchars($product['name']) ?>">
                </div>
                
                <div class="card-body">
                    <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                    <p class="product-description"><?= htmlspecialchars(substr($product['description'], 0, 120)) ?>...</p>
                    
                    <div class="product-meta">
                        <div class="rating mb-2">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                            <span class="ml-1">(12)</span>
                        </div>
                        
                        <p class="seller-info mb-1">
                            <i class="fas fa-store-alt"></i> <?= htmlspecialchars($product['seller_name']) ?>
                        </p>
                        
                        <p class="location mb-3">
                            <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($product['location']) ?>
                        </p>
                    </div>
                </div>
                
                <div class="card-footer bg-white border-0 pb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="price-tag"><?= number_format($product['price'], 2) ?> FCFA</span>
                        
                        <?php 
                        $whatsapp_link = "https://wa.me/221".preg_replace('/[^0-9]/', '', $product['seller_phone'])."?text=".
                            urlencode("Bonjour ".$product['seller_name']."! Je suis intéressé par votre produit '".
                            $product['name']."' (".number_format($product['price'], 2)." FCFA) sur e-Yombna. ");
                        ?>
                        <a href="<?= $whatsapp_link ?>" 
                           class="btn whatsapp-btn text-white"
                           target="_blank">
                            <i class="fab fa-whatsapp mr-2"></i> Commander
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="assets/js/boutique.js"></script>

<?php require_once 'includes/footer.php'; ?>