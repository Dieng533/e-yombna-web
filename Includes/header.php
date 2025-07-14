<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>e-Yombna</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="icon" href="assets/images/logo.png" type="image/jpeg">
  <link rel="shortcut icon" href="assets/images/logo.png" type="image/x-icon">
  <style>
    .navbar {
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .btn-auth {
      border-radius: 20px;
      padding: 5px 15px;
      margin-left: 10px;
    }
    .btn-login {
      border: 1px solid #fff;
    }
    .btn-register {
      background-color: #0d6efd;
    }
    .cart-icon {
      position: relative;
      margin-right: 15px;
    }
    .cart-count {
      position: absolute;
      top: -8px;
      right: -8px;
      background-color: #ff4757;
      color: white;
      border-radius: 50%;
      width: 18px;
      height: 18px;
      font-size: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <img src="assets/images/logo.png" alt="Logo" width="60" height="40" class="me-2 rounded-circle">
        <strong>e-Yombna</strong>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="index.php">Accueil</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="boutique.php">Boutique</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="contact.php">Contact</a>
          </li>
        </ul>
        
        <div class="d-flex align-items-center">
          <!-- Icône panier -->
          <a href="panier.php" class="cart-icon text-white position-relative">
            <i class="fas fa-shopping-cart fa-lg"></i>
            <span class="cart-count">0</span>
          </a>
          
          <?php if (isset($_SESSION['user_id'])): ?>
            <div class="dropdown">
              <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                <i class="fas fa-user-circle me-1"></i> Mon compte
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="admin/dashboard.php">Dashboard</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php">Déconnexion</a></li>
              </ul>
            </div>
          <?php else: ?>
            <a href="login.php" class="btn btn-outline-light btn-auth btn-login">
              <i class="fas fa-sign-in-alt me-1"></i> Connexion
            </a>
            <a href="register.php" class="btn btn-primary btn-auth btn-register">
              <i class="fas fa-user-plus me-1"></i> Inscription
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>
  <div class="container">