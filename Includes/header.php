<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>e-Yombna</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="icon" href="assets/img/logo.jpg" type="image/jpeg">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <img src="assets/img/logo.jpg" alt="Logo" width="40" height="40" class="me-2 rounded-circle">
        <strong>e-Yombna</strong>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="index.php">Accueil</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="boutique.php">Boutique</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="contact.php">Contact</a>
          </li>
          <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
              <a class="nav-link" href="admin/dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-danger" href="logout.php">Déconnexion</a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link" href="login.php">Connexion</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="register.php">Inscription</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container mt-4">