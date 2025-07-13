<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/header.php';

// Récupérer tous les produits
try {
    $stmt = $db->query("
        SELECT p.*, u.name as seller_name, u.phone as seller_phone 
        FROM products p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
?>

<link rel="stylesheet" href="assets/css/boutique.css">

<div class="container mt-5">
    <h1 class="text-center mb-5">Boutique e-Yombna</h1>
    
    <div class="row">
        <?php foreach ($products as $product): ?>
        <div class="col-md-4 mb-4">
            <div class="card product-card h-100">
                <img src="<?= htmlspecialchars($product['image_path']) ?>" 
                     class="card-img-top product-image" 
                     alt="<?= htmlspecialchars($product['name']) ?>">
                
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                    <p class="text-muted"><?= htmlspecialchars($product['category']) ?></p>
                    <p class="card-text"><?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...</p>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="h5 text-success"><?= number_format($product['price'], 2) ?> FCFA</span>
                        <span class="badge badge-info"><?= htmlspecialchars($product['location']) ?></span>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <button class="btn btn-primary btn-block mb-2" 
                            data-toggle="modal" 
                            data-target="#orderModal"
                            data-product-id="<?= $product['id'] ?>"
                            data-product-name="<?= htmlspecialchars($product['name']) ?>"
                            data-seller-phone="<?= htmlspecialchars($product['seller_phone']) ?>">
                        <i class="fas fa-shopping-cart"></i> Commander
                    </button>
                    
                    <a href="https://wa.me/221<?= htmlspecialchars($product['seller_phone']) ?>?text=Je%20suis%20intéressé%20par%20<?= urlencode($product['name']) ?>%20(%20<?= number_format($product['price'], 2) ?>%20FCFA)%20sur%20e-Yombna" 
                       class="btn btn-success btn-block" target="_blank">
                        <i class="fab fa-whatsapp"></i> Discuter
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal de commande -->
<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel">Commander <span id="productName"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="orderForm" method="POST" action="process-order.php">
                <div class="modal-body">
                    <input type="hidden" name="product_id" id="modalProductId">
                    
                    <div class="form-group">
                        <label for="quantity">Quantité</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="customer_name">Votre nom complet</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="customer_phone">Votre numéro WhatsApp</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">+221</span>
                            </div>
                            <input type="tel" class="form-control" id="customer_phone" name="customer_phone" 
                                   pattern="[0-9]{9}" placeholder="771234567" required>
                        </div>
                        <small class="form-text text-muted">Format: 771234567 (sans espaces)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="customer_address">Adresse de livraison</label>
                        <textarea class="form-control" id="customer_address" name="customer_address" rows="2" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Envoyer la commande</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Gestion du modal
$('#orderModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var productId = button.data('product-id');
    var productName = button.data('product-name');
    var sellerPhone = button.data('seller-phone');
    
    var modal = $(this);
    modal.find('#productName').text(productName);
    modal.find('#modalProductId').val(productId);
    
    // Mise à jour du lien WhatsApp dans le formulaire si nécessaire
});
</script>

<?php require_once 'includes/footer.php'; ?>