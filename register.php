<?php
session_start();
require_once 'includes/config.php';

$errors = [];
$typesEntreprise = ['GIE', 'PME', 'PMI', 'Jeune entrepreneur', 'Autre'];
$categoriesVente = ['Alimentation', 'Artisanat', 'Textile', 'Cosmétique', 'Services', 'Autre'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $entreprise = htmlspecialchars(trim($_POST['entreprise']));
    $type_entreprise = htmlspecialchars(trim($_POST['type_entreprise']));
    $ville = htmlspecialchars(trim($_POST['ville']));
    $categorie = htmlspecialchars(trim($_POST['categorie']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if (empty($name) || empty($entreprise) || empty($email) || empty($password) || empty($confirm)) {
        $errors[] = "Les champs marqués d'un * sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invalide.";
    } elseif ($password !== $confirm) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    } else {
        try {
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $errors[] = "Email déjà utilisé.";
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $db->prepare("INSERT INTO users (name, entreprise, type_entreprise, ville, categorie, email, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
                
                if ($stmt->execute([$name, $entreprise, $type_entreprise, $ville, $categorie, $email, $hash])) {
                    $_SESSION['user_id'] = $db->lastInsertId();
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_type'] = 'seller';
                    $_SESSION['success_message'] = "Inscription réussie !";
                    header("Location: admin/dashboard.php");
                    exit;
                }
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'inscription. Veuillez réessayer.";
        }
    }
}

require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - e-Yombna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #000000;  /* Noir */
            --secondary-color: #f8f9fc;
            --accent-color: #333333;   /* Gris foncé */
            --success-color: #28a745;  /* Vert */
        }
        
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background-color: #f5f5f5;
        }
        
        .register-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .register-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .register-body {
            padding: 2rem;
            background-color: white;
        }
        
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(0, 0, 0, 0.1);
        }
        
        .btn-register {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .password-input-group {
            position: relative;
        }
        
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
    </style>
</head>
<body>
<div class="register-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="register-card">
                    <div class="register-header">
                        <img src="assets/images/logo-white.png" alt="e-Yombna" class="img-fluid mb-3" style="max-height: 60px;">
                        <h3>Créer un compte vendeur</h3>
                        <p class="mb-0">Rejoignez notre marketplace d'artisans sénégalais</p>
                    </div>
                    <div class="register-body">
                        <?php if ($errors): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="mt-4">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold required-field">Nom complet</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-dark text-white"><i class="fas fa-user"></i></span>
                                        <input type="text" name="name" class="form-control" required value="<?= isset($_POST['name']) ? $_POST['name'] : '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold required-field">Nom de l'entreprise</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-dark text-white"><i class="fas fa-building"></i></span>
                                        <input type="text" name="entreprise" class="form-control" required value="<?= isset($_POST['entreprise']) ? $_POST['entreprise'] : '' ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold">Type d'entreprise</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-dark text-white"><i class="fas fa-tag"></i></span>
                                        <select name="type_entreprise" class="form-select">
                                            <?php foreach ($typesEntreprise as $type): ?>
                                                <option value="<?= $type ?>" <?= (isset($_POST['type_entreprise']) && $_POST['type_entreprise'] === $type) ? 'selected' : '' ?>>
                                                    <?= $type ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold">Ville</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-dark text-white"><i class="fas fa-map-marker-alt"></i></span>
                                        <input type="text" name="ville" class="form-control" value="<?= isset($_POST['ville']) ? $_POST['ville'] : '' ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Catégorie de vente</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-white"><i class="fas fa-shopping-basket"></i></span>
                                    <select name="categorie" class="form-select">
                                        <?php foreach ($categoriesVente as $categorie): ?>
                                            <option value="<?= $categorie ?>" <?= (isset($_POST['categorie']) && $_POST['categorie'] === $categorie) ? 'selected' : '' ?>>
                                                <?= $categorie ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold required-field">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-white"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" required value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold required-field">Mot de passe (8 caractères min)</label>
                                    <div class="password-input-group">
                                        <div class="input-group">
                                            <span class="input-group-text bg-dark text-white"><i class="fas fa-lock"></i></span>
                                            <input type="password" name="password" id="passwordField" class="form-control" required minlength="8">
                                            <span class="password-toggle" id="togglePassword">
                                                <i class="far fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold required-field">Confirmer mot de passe</label>
                                    <div class="password-input-group">
                                        <div class="input-group">
                                            <span class="input-group-text bg-dark text-white"><i class="fas fa-lock"></i></span>
                                            <input type="password" name="confirm" id="confirmPasswordField" class="form-control" required minlength="8">
                                            <span class="password-toggle" id="toggleConfirmPassword">
                                                <i class="far fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-register btn-lg">
                                    <i class="fas fa-user-plus me-2"></i> Créer mon compte
                                </button>
                            </div>
                            
                            <div class="text-center mt-3">
                                <p class="mb-0">Déjà inscrit ? <a href="login.php" class="text-dark fw-bold">Se connecter</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Fonctionnalité pour afficher/masquer les mots de passe
function setupPasswordToggle(fieldId, toggleId) {
    const toggle = document.getElementById(toggleId);
    const field = document.getElementById(fieldId);
    
    toggle.addEventListener('click', function() {
        const icon = this.querySelector('i');
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
}

setupPasswordToggle('passwordField', 'togglePassword');
setupPasswordToggle('confirmPasswordField', 'toggleConfirmPassword');

// Animation pour les champs de formulaire
document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.querySelector('.input-group-text').style.backgroundColor = 'var(--primary-color)';
    });
    
    input.addEventListener('blur', function() {
        this.parentElement.querySelector('.input-group-text').style.backgroundColor = '';
    });
});
</script>
</body>
</html>

<?php require_once 'includes/footer.php'; ?>