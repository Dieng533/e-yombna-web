<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    $_SESSION['seller_error'] = "Vous devez être connecté pour devenir vendeur";
    header('Location: ../index.php');
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $business_type = sanitize($_POST['business_type']);
    $business_desc = sanitize($_POST['business_desc']);
    $tax_id = sanitize($_POST['tax_id']);
    
    try {
        // Vérifier si l'utilisateur est déjà vendeur
        $stmt = $db->prepare("SELECT user_type FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if ($user['user_type'] === 'seller') {
            $_SESSION['seller_error'] = "Vous êtes déjà inscrit comme vendeur";
            header('Location: ../index.php');
            exit;
        }
        
        // Mettre à jour le type d'utilisateur
        $stmt = $db->prepare("UPDATE users SET user_type = 'seller' WHERE id = ?");
        $stmt->execute([$user_id]);
        
        // Ajouter les infos complémentaires
        $stmt = $db->prepare("INSERT INTO seller_info (user_id, business_type, business_desc, tax_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $business_type, $business_desc, $tax_id]);
        
        // Mettre à jour la session
        $_SESSION['user_type'] = 'seller';
        $_SESSION['seller_success'] = "Félicitations! Vous êtes maintenant vendeur sur e-Yombna";
        
        // Redirection vers le dashboard
        header('Location: ../admin/dashboard.php');
        exit;
        
    } catch(PDOException $e) {
        $_SESSION['seller_error'] = "Erreur lors de l'enregistrement: " . $e->getMessage();
        header('Location: ../index.php');
        exit;
    }
}

header('Location: ../index.php');
exit;
?>