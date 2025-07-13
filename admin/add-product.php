<?php
session_start();
require_once __DIR__.'/../includes/config.php';

// Vérification de l'authentification et du rôle admin/vendeur
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$errors = [];
$success = false;

// Catégories disponibles
$categories = ['Manuelles', 'Industrielle'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et validation des données
    $name = htmlspecialchars(trim($_POST['name']));
    $price = floatval($_POST['price']);
    $description = htmlspecialchars(trim($_POST['description']));
    $category = htmlspecialchars(trim($_POST['category']));
    $location = htmlspecialchars(trim($_POST['location']));
    
    // Validation
    if (empty($name)) $errors[] = "Le nom du produit est obligatoire";
    if ($price <= 0) $errors[] = "Le prix doit être supérieur à 0";
    if (empty($description)) $errors[] = "La description est obligatoire";
    if (!in_array($category, $categories)) $errors[] = "Catégorie invalide";
    
    // Gestion de l'image
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['image']['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Seuls les formats JPG, PNG et GIF sont autorisés";
        } else {
            $uploadDir = '../assets/img/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('product_', true) . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $imagePath = 'assets/img/products/' . $filename;
            } else {
                $errors[] = "Erreur lors de l'upload de l'image";
            }
        }
    } else {
        $errors[] = "L'image du produit est obligatoire";
    }
    
    // Si pas d'erreurs, insertion en base
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("
                INSERT INTO products 
                (user_id, name, price, description, category, location, image_path, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $name,
                $price,
                $description,
                $category,
                $location,
                $imagePath
            ]);
            
            // Redirection vers boutique.php avec message de succès
            $_SESSION['success_message'] = "Produit ajouté avec succès !";
            header("Location: ../boutique.php");
            exit;
            
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'ajout du produit: " . $e->getMessage();
        }
    }
}

require_once __DIR__.'/../includes/header-dashboard.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include __DIR__.'/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Ajouter un nouveau produit</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="products.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour aux produits
                    </a>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                Informations du produit
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Nom du produit *</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= $_POST['name'] ?? '' ?>" required>
                                    <div class="invalid-feedback">
                                        Veuillez saisir un nom valide.
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description *</label>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="5" required><?= $_POST['description'] ?? '' ?></textarea>
                                    <div class="invalid-feedback">
                                        Veuillez saisir une description.
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="price">Prix (FCFA) *</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="price" 
                                                   name="price" value="<?= $_POST['price'] ?? '' ?>" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">FCFA</span>
                                            </div>
                                            <div class="invalid-feedback">
                                                Veuillez saisir un prix valide.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="category">Catégorie *</label>
                                        <select class="form-control" id="category" name="category" required>
                                            <option value="">Sélectionner...</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat ?>" 
                                                    <?= ($_POST['category'] ?? '') === $cat ? 'selected' : '' ?>>
                                                    <?= $cat ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Veuillez sélectionner une catégorie.
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="location">Localisation *</label>
                                    <input type="text" class="form-control" id="location" name="location" 
                                           value="<?= $_POST['location'] ?? '' ?>" required>
                                    <div class="invalid-feedback">
                                        Veuillez saisir une localisation.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                Image du produit
                            </div>
                            <div class="card-body text-center">
                                <div class="form-group">
                                    <div class="image-preview mb-3" id="imagePreview">
                                        <img src="../assets/img/placeholder-product.png" 
                                             alt="Aperçu de l'image" class="img-thumbnail" id="previewImage">
                                    </div>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="image" 
                                               name="image" accept="image/*" required>
                                        <label class="custom-file-label" for="image" id="imageLabel">
                                            Choisir une image...
                                        </label>
                                        <div class="invalid-feedback">
                                            Veuillez sélectionner une image.
                                        </div>
                                        <small class="form-text text-muted">
                                            Formats acceptés: JPG, PNG, GIF (max 2MB)
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                Publication
                            </div>
                            <div class="card-body">
                                <button type="submit" class="btn btn-success btn-block btn-lg">
                                    <i class="fas fa-check-circle"></i> Publier le produit
                                </button>
                                <small class="text-muted">
                                    Le produit sera visible immédiatement dans la boutique.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </main>
    </div>
</div>

<script>
// Aperçu de l'image avant upload
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('previewImage').src = event.target.result;
            document.getElementById('imageLabel').textContent = file.name;
        }
        reader.readAsDataURL(file);
    }
});

// Validation du formulaire
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>

<?php require_once __DIR__.'/../includes/footer-dashboard.php'; ?>