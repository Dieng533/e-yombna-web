<?php
/**
 * Vérifie si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Vérifie le rôle de l'utilisateur
 */
function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

/**
 * Protection des pages admin
 */
function requireAuth($role = null) {
    if (!isLoggedIn()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        redirect('login.php');
    }
    
    if ($role && !hasRole($role)) {
        redirect('index.php');
    }
}

/**
 * Récupère l'utilisateur courant
 */
function currentUser() {
    if (!isLoggedIn()) return null;
    
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Hashage de mot de passe
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Vérification de mot de passe
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Récupère les produits
 */
function getProducts($limit = null) {
    global $pdo;
    $sql = "SELECT * FROM products ORDER BY created_at DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    return $pdo->query($sql)->fetchAll();
}

/**
 * Nettoie les données
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}