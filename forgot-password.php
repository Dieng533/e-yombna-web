<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/phpmailer/src/Exception.php';
require_once 'includes/phpmailer/src/PHPMailer.php';
require_once 'includes/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
$error = '';
$show_debug_link = false; // Passer à true pour debug si l'email échoue

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    
    if (empty($email)) {
        $error = "Veuillez entrer votre adresse email";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format d'email invalide";
    } else {
        try {
            // Vérifier si l'email existe
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Générer un token sécurisé
                $token = bin2hex(random_bytes(32));
                $expires = date("Y-m-d H:i:s", time() + 3600); // 1 heure
                
                // Supprimer les anciennes demandes
                $stmt = $db->prepare("DELETE FROM password_resets WHERE email = ?");
                $stmt->execute([$email]);
                
                // Stocker la nouvelle demande
                $stmt = $db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
                $stmt->execute([$email, $token, $expires]);
                
                // Générer le lien
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                $reset_link = "$protocol://$_SERVER[HTTP_HOST]/reset-password.php?token=$token";
                
                // Envoyer par PHPMailer
                $mail = new PHPMailer(true);
                
                try {
                    // Configuration SMTP (à adapter)
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // Serveur SMTP
                    $mail->SMTPAuth = true;
                    $mail->Username = 'votre@gmail.com'; // Votre email
                    $mail->Password = 'votre-motdepasse'; // Mot de passe app ou 2FA
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    
                    // Destinataire
                    $mail->setFrom('no-reply@votresite.com', 'e-Yombna');
                    $mail->addAddress($email);
                    
                    // Contenu
                    $mail->isHTML(true);
                    $mail->Subject = 'Réinitialisation de votre mot de passe';
                    $mail->Body = "
                        <h2>Réinitialisation de mot de passe</h2>
                        <p>Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p>
                        <p><a href='$reset_link' style='background:#000; color:#fff; padding:10px 15px; text-decoration:none; border-radius:5px;'>Réinitialiser</a></p>
                        <p>Ou copiez ce lien dans votre navigateur :<br>$reset_link</p>
                        <p><em>Ce lien expirera dans 1 heure.</em></p>
                    ";
                    
                    $mail->send();
                    $message = "Un email de réinitialisation a été envoyé à $email";
                    
                } catch (Exception $e) {
                    $error = "L'email n'a pas pu être envoyé. Erreur : " . $mail->ErrorInfo;
                    $show_debug_link = true; // Affiche le lien en cas d'échec
                }
                
            } else {
                $message = "Si cet email existe, un lien de réinitialisation a été envoyé.";
            }
        } catch (PDOException $e) {
            $error = "Une erreur est survenue. Veuillez réessayer.";
            error_log("Reset password error: " . $e->getMessage());
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
    <title>Mot de passe oublié - e-Yombna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #000000;
            --accent-color: #333333;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .reset-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 2rem auto;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 1.5rem;
            text-align: center;
        }
        
        .card-body {
            padding: 2rem;
            background-color: white;
            border-radius: 0 0 10px 10px;
        }
        
        .btn-black {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: 600;
        }
        
        .btn-black:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }
        
        .reset-link {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            word-break: break-all;
            margin-top: 15px;
        }
        
        .input-group-text {
            background-color: #000;
            color: white;
            border: none;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="reset-card">
                <div class="card-header">
                    <img src="assets/images/logo-white.png" alt="Logo" style="height: 50px;">
                    <h3 class="mt-3">Réinitialisation du mot de passe</h3>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-info">
                            <?= $message ?>
                            <?php if ($reset_link): ?>
                                <div class="mt-3">
                                    <p class="mb-2"><strong>Lien de réinitialisation :</strong></p>
                                    <a href="<?= $reset_link ?>" class="reset-link d-block"><?= $reset_link ?></a>
                                    <p class="small text-muted mt-2">Copiez ce lien dans votre navigateur</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Adresse Email</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-black btn-lg">
                                <i class="fas fa-key me-2"></i> Générer le lien
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="login.php" class="text-dark"><i class="fas fa-arrow-left me-2"></i> Retour à la connexion</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php require_once 'includes/footer.php'; ?>