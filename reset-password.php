<?php
session_start();
require_once 'includes/config.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    header("Location: forgot-password.php");
    exit;
}

// Vérifier le token
try {
    $stmt = $db->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $resetRequest = $stmt->fetch();
    
    if (!$resetRequest) {
        $error = "Lien invalide ou expiré. Veuillez faire une nouvelle demande.";
    }
} catch (PDOException $e) {
    $error = "Erreur de vérification. Veuillez réessayer.";
}

// Traitement du formulaire de réinitialisation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    
    if (empty($password) || empty($confirm)) {
        $error = "Veuillez remplir tous les champs";
    } elseif ($password !== $confirm) {
        $error = "Les mots de passe ne correspondent pas";
    } elseif (strlen($password) < 8) {
        $error = "Le mot de passe doit contenir au moins 8 caractères";
    } else {
        try {
            // Mettre à jour le mot de passe
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$hash, $resetRequest['email']]);
            
            // Supprimer la demande de réinitialisation
            $stmt = $db->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->execute([$token]);
            
            $success = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
            $_SESSION['reset_success'] = $success;
            header("Location: login.php");
            exit;
        } catch (PDOException $e) {
            $error = "Erreur lors de la réinitialisation. Veuillez réessayer.";
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
    <title>Nouveau mot de passe - e-Yombna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #000000;
            --accent-color: #333333;
        }
        
        .password-reset-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background-color: #f5f5f5;
        }
        
        .password-reset-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .password-reset-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .password-reset-body {
            padding: 2rem;
            background-color: white;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border: none;
            padding: 12px;
            font-weight: 600;
            color: white;
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
    </style>
</head>
<body>
<div class="password-reset-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="password-reset-card">
                    <div class="password-reset-header">
                        <img src="assets/images/logo-white.png" alt="e-Yombna" class="img-fluid mb-3" style="max-height: 60px;">
                        <h3>Créer un nouveau mot de passe</h3>
                    </div>
                    <div class="password-reset-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <?= $success ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Nouveau mot de passe</label>
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
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Confirmer le mot de passe</label>
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
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-submit btn-lg">
                                    <i class="fas fa-save me-2"></i> Enregistrer
                                </button>
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
</script>
</body>
</html>

<?php require_once 'includes/footer.php'; ?>