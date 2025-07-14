<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-Yombna - Marketplace des artisans Sénégalais</title>
    <!-- <link rel="stylesheet" href="assets/css/home.css"> -->
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Hero Section Modernisée -->
<section class="hero-section" style="background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/images/hero-image.jpg'); background-size: cover; background-position: center; color: white; padding: 100px 0; width: 100%;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 text-center text-lg-start mb-5 mb-lg-0">
                <img src="assets/images/logo.png" alt="Logo e-Yombna" class="hero-logo mb-4 img-fluid" style="max-height: 80px;">
                <h1 class="display-4 fw-bold mb-3">Marketplace des artisans Sénégalais</h1>
                <p class="lead mb-4">Vendez et achetez des produits 100% locaux en toute confiance</p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    <a href="boutique.php" class="btn btn-primary btn-lg px-4 me-2">
                        <i class="fas fa-store me-2"></i>Explorer la boutique
                    </a>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="login.php" class="btn btn-outline-light btn-lg px-4">
                            <i class="fas fa-user-plus me-2"></i>Devenir vendeur
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Stats intégrées dans la version moderne -->
                <div class="mt-4 pt-3">
                    <div class="d-flex gap-4 justify-content-center justify-content-lg-start">
                        <div class="text-center">
                            <span class="d-block fw-bold fs-3">500+</span>
                            <span>Vendeurs</span>
                        </div>
                        <div class="text-center">
                            <span class="d-block fw-bold fs-3">10K+</span>
                            <span>Produits</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <!-- J'ai supprimé l'image en double puisque nous l'utilisons comme background -->
                <!-- Les stats ont été déplacées sous les boutons pour une meilleure intégration -->
            </div>
        </div>
    </div>
</section>

<!-- Section Catégories -->
<section class="category-section py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title display-5 fw-bold">Nos Catégories</h2>
            <p class="section-subtitle text-muted fs-5">Découvrez nos produits par spécialité</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <a href="boutique.php?category=Artisanat" class="category-card text-decoration-none">
                    <div class="category-icon bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3">
                        <i class="fas fa-hands"></i>
                    </div>
                    <h3 class="h4 fw-bold mb-2">Artisanat</h3>
                    <p class="text-muted mb-0">Produits faits main</p>
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="boutique.php?category=Alimentation" class="category-card text-decoration-none">
                    <div class="category-icon bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h3 class="h4 fw-bold mb-2">Alimentation</h3>
                    <p class="text-muted mb-0">Produits locaux</p>
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="boutique.php?category=Textile" class="category-card text-decoration-none">
                    <div class="category-icon bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <h3 class="h4 fw-bold mb-2">Textile</h3>
                    <p class="text-muted mb-0">Vêtements traditionnels</p>
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="boutique.php?category=Cosmétique" class="category-card text-decoration-none">
                    <div class="category-icon bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3">
                        <i class="fas fa-spa"></i>
                    </div>
                    <h3 class="h4 fw-bold mb-2">Cosmétique</h3>
                    <p class="text-muted mb-0">Produits naturels</p>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Section Produits Phares -->
<section class="featured-products py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title display-5 fw-bold">Produits Populaires</h2>
            <p class="section-subtitle text-muted fs-5">Découvrez nos meilleures ventes</p>
        </div>
        <div class="row g-4">
            <?php
            // Récupérer 4 produits aléatoires
            $stmt = $db->query("SELECT * FROM products ORDER BY RAND() LIMIT 4");
            while ($product = $stmt->fetch(PDO::FETCH_ASSOC)):
            ?>
            <div class="col-md-6 col-lg-3">
                <div class="product-card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="position-relative">
                        <span class="product-badge bg-success">Nouveau</span>
                        <img src="<?= htmlspecialchars($product['image_path']) ?>" class="product-image img-fluid w-100" alt="<?= htmlspecialchars($product['name']) ?>" style="height: 200px; object-fit: cover;">
                    </div>
                    <div class="product-details p-3">
                        <h3 class="product-title h5 fw-bold mb-2"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-price text-primary fw-bold fs-5 mb-3"><?= number_format($product['price'], 2) ?> FCFA</p>
                        <a href="boutique.php?product=<?= $product['id'] ?>" class="btn btn-outline-primary w-100">
                            <i class="fas fa-eye me-2"></i>Voir le produit
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="text-center mt-5">
            <a href="boutique.php" class="btn btn-primary btn-lg px-4 py-3">
                <i class="fas fa-list me-2"></i>Voir tous les produits
            </a>
        </div>
    </div>
</section>

<!-- Section Publicité - Version améliorée -->
<section class="ad-section py-5 position-relative bg-primary">
    <div class="container position-relative z-2">
        <div class="row align-items-center g-4">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="display-5 fw-bold text-white mb-3">Boostez vos ventes avec nos publicités</h2>
                <p class="lead text-white-80 mb-4">Augmentez votre visibilité sur notre plateforme et atteignez des milliers de clients potentiels</p>
                
                <div class="d-flex align-items-start mb-4">
                    <div class="me-3">
                        <div class="icon-circle bg-white bg-opacity-10 text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-chart-line fs-5"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-white mb-1">Mise en avant premium</h4>
                        <p class="text-white-80 mb-0">Votre produit en tête de liste pendant 7 jours</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start mb-4">
                    <div class="me-3">
                        <div class="icon-circle bg-white bg-opacity-10 text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-bullhorn fs-5"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-white mb-1">Notification aux membres</h4>
                        <p class="text-white-80 mb-0">Alertes envoyées à nos 10,000+ utilisateurs</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start">
                    <div class="me-3">
                        <div class="icon-circle bg-white bg-opacity-10 text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-mobile-alt fs-5"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-white mb-1">Apparence mobile optimisée</h4>
                        <p class="text-white-80 mb-0">Design responsive pour tous les appareils</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg h-100">
                    <div class="card-header bg-dark text-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="h4 mb-0">Pack Publicitaire Standard</h3>
                            <span class="badge bg-success fs-6 py-2 px-3">5,000 FCFA</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form id="wavePaymentForm">
                            <div class="mb-3">
                                <label for="adTitle" class="form-label fw-bold">Titre de votre publicité</label>
                                <input type="text" class="form-control form-control-lg" id="adTitle" placeholder="Ex: Promotion Spéciale -20%" required>
                            </div>
                            <div class="mb-3">
                                <label for="adImage" class="form-label fw-bold">Image publicitaire</label>
                                <input type="file" class="form-control" id="adImage" accept="image/*" required>
                                <small class="text-muted">Format recommandé: 1200x400px (JPG/PNG)</small>
                            </div>
                            <div class="mb-4">
                                <label for="adLink" class="form-label fw-bold">Lien de destination</label>
                                <input type="url" class="form-control form-control-lg" id="adLink" placeholder="https://votre-lien.com" required>
                            </div>
                            <button type="submit" class="btn btn-light btn-lg w-100 d-flex align-items-center justify-content-center py-3">
                                <img src="assets/img/wave-logo.png" alt="Wave" height="24" class="me-2">
                                <span>Payer avec Wave</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Témoignages -->
<section class="testimonials py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title display-5 fw-bold">Ils nous font confiance</h2>
            <p class="section-subtitle text-muted fs-5">Découvrez ce que disent nos vendeurs</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="testimonial-card h-100 bg-white p-4 rounded-3 shadow-sm">
                    <div class="testimonial-content mb-4 position-relative">
                        <p class="fst-italic text-dark">"Grâce à e-Yombna, j'ai pu développer mon activité artisanale et toucher des clients dans tout le pays."</p>
                    </div>
                    <div class="testimonial-author d-flex align-items-center">
                        <img src="assets/images/img2.jpg" alt="Aminata Diop" class="rounded-circle me-3" width="60" height="60">
                        <div>
                            <h5 class="mb-1 fw-bold">Aminata Diop</h5>
                            <p class="text-muted mb-0">Artisane, Dakar</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card h-100 bg-white p-4 rounded-3 shadow-sm">
                    <div class="testimonial-content mb-4 position-relative">
                        <p class="fst-italic text-dark">"La simplicité de vente et le système de paiement sécurisé ont révolutionné mon commerce."</p>
                    </div>
                    <div class="testimonial-author d-flex align-items-center">
                        <img src="assets/images/img1.jpg" alt="Mamadou Fall" class="rounded-circle me-3" width="60" height="60">
                        <div>
                            <h5 class="mb-1 fw-bold">Mamadou Fall</h5>
                            <p class="text-muted mb-0">Producteur, Thiès</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card h-100 bg-white p-4 rounded-3 shadow-sm">
                    <div class="testimonial-content mb-4 position-relative">
                        <p class="fst-italic text-dark">"En 3 mois, mes ventes ont augmenté de 200%. Je recommande vivement cette plateforme."</p>
                    </div>
                    <div class="testimonial-author d-flex align-items-center">
                        <img src="assets/images/3.jpg" alt="Aïssatou Ndiaye" class="rounded-circle me-3" width="60" height="60">
                        <div>
                            <h5 class="mb-1 fw-bold">Aïssatou Ndiaye</h5>
                            <p class="text-muted mb-0">Créatrice, Saint-Louis</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://gateway.wave.com/wave.js"></script>
<script>
// Initialisation Wave Payment
document.getElementById('wavePaymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Vérification de l'authentification
    <?php if (!isset($_SESSION['user_id'])): ?>
        window.location.href = 'login.php?redirect=index.php';
        return;
    <?php endif; ?>
    
    // Configuration du paiement
    Wave.init({
        business: 'e-yombna',
        amount: '5000',
        currency: 'XOF',
        container: 'wavePaymentForm',
        callback: function(response) {
            if (response.status === 'success') {
                // Envoyer les données du formulaire + référence Wave
                alert('Paiement réussi! Votre pub sera activée après validation.');
                // Ici, vous ajouteriez une requête AJAX pour enregistrer la pub
            } else {
                alert('Paiement échoué: ' + response.message);
            }
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>