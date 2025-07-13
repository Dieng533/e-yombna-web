<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Seulement pour utilisateurs normaux connectés
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mettre à jour le type d'utilisateur en 'seller'
    try {
        $stmt = $db->prepare("UPDATE users SET user_type = 'seller' WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        
        // Mettre à jour la session
        $_SESSION['user_type'] = 'seller';
        
        // Rediriger vers le dashboard
        header('Location: ../admin/dashboard.php');
        exit;
    } catch(PDOException $e) {
        $error = "Erreur lors de la mise à jour du compte: " . $e->getMessage();
    }
}

require_once '../includes/header.php';
?>

<!-- Formulaire simple -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4>Devenir vendeur</h4>
                </div>
                <div class="card-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <p>Vous êtes sur le point de devenir vendeur sur notre plateforme.</p>
                        <button type="submit" class="btn btn-success">Confirmer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>