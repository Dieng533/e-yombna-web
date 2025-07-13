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

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">Créer un compte vendeur</h3>
                </div>
                <div class="card-body">
                    <?php if ($errors): ?>
                        <div class="alert alert-danger">
                            <ul><?php foreach ($errors as $error) echo "<li>$error</li>"; ?></ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom complet *</label>
                                <input type="text" name="name" class="form-control" required value="<?= isset($_POST['name']) ? $_POST['name'] : '' ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom de l'entreprise *</label>
                                <input type="text" name="entreprise" class="form-control" required value="<?= isset($_POST['entreprise']) ? $_POST['entreprise'] : '' ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Type d'entreprise</label>
                                <select name="type_entreprise" class="form-select">
                                    <?php foreach ($typesEntreprise as $type): ?>
                                        <option value="<?= $type ?>" <?= (isset($_POST['type_entreprise']) && $_POST['type_entreprise'] === $type) ? 'selected' : '' ?>>
                                            <?= $type ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ville</label>
                                <input type="text" name="ville" class="form-control" value="<?= isset($_POST['ville']) ? $_POST['ville'] : '' ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catégorie de vente</label>
                            <select name="categorie" class="form-select">
                                <?php foreach ($categoriesVente as $categorie): ?>
                                    <option value="<?= $categorie ?>" <?= (isset($_POST['categorie']) && $_POST['categorie'] === $categorie) ? 'selected' : '' ?>>
                                        <?= $categorie ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mot de passe * (8 caractères min)</label>
                                <input type="password" name="password" class="form-control" required minlength="8">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirmer mot de passe *</label>
                                <input type="password" name="confirm" class="form-control" required minlength="8">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">Créer mon compte</button>
                            <a href="login.php" class="btn btn-outline-secondary">Déjà inscrit ? Se connecter</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>