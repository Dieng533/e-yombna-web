<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/style.css">

<section class="hero-section">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-6 hero-text">
        <img src="assets/img/logo.jpg" alt="Logo e-Yombna" class="hero-logo">
        <h1>Bienvenue sur e-Yombna</h1>
        <p>La plateforme e-commerce des artisans, GIE et transformateurs du Sénégal.</p>
        <a href="login.php" class="btn btn-success mt-3">Devenir vendeur</a>
      </div>
      <div class="col-md-6 text-center">
        <img src="assets/images/hero-image.jpg" alt="Illustration vendeur" class="hero-img">
      </div>
    </div>
  </div>
</section>

<section class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card card-vendeur shadow-sm">
        <div class="card-body text-center">
          <h3 class="card-title text-dark">Vous êtes vendeur ?</h3>
          <p class="card-text text-muted">
            Créez votre boutique gratuitement et commencez à vendre vos produits en ligne.
          </p>
          <a href="login.php" class="btn btn-success me-2">Se connecter</a>
          <a href="register.php" class="btn btn-outline-success">Créer un compte</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>