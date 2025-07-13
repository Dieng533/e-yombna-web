<?php
// Liste des requêtes SQL pour créer les tables

$tables = [
    // Table des utilisateurs (clients/vendeurs)
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        phone VARCHAR(20) NOT NULL,
        password VARCHAR(255) NOT NULL,
        avatar VARCHAR(255) DEFAULT 'default.jpg',
        user_type ENUM('client', 'vendor', 'admin') DEFAULT 'client',
        shop_name VARCHAR(100),
        shop_description TEXT,
        shop_logo VARCHAR(255),
        local_vendor TINYINT(1) DEFAULT 0,
        verified TINYINT(1) DEFAULT 0,
        verification_token VARCHAR(100),
        reset_token VARCHAR(100),
        reset_expires DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_user_type (user_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    // Table des adresses
    "CREATE TABLE IF NOT EXISTS addresses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        address_type ENUM('home', 'work', 'other') NOT NULL,
        street_address TEXT NOT NULL,
        city VARCHAR(50) NOT NULL,
        region VARCHAR(50) NOT NULL,
        postal_code VARCHAR(20),
        country VARCHAR(50) DEFAULT 'Sénégal',
        is_default TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    // Table des catégories
    "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        slug VARCHAR(50) NOT NULL UNIQUE,
        parent_id INT,
        icon VARCHAR(50) DEFAULT 'fas fa-tag',
        image VARCHAR(255),
        featured TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
        INDEX idx_parent (parent_id),
        INDEX idx_slug (slug)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    // Table des produits
    "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        vendor_id INT NOT NULL,
        category_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL,
        description TEXT NOT NULL,
        price DECIMAL(12,2) NOT NULL,
        discount DECIMAL(5,2) DEFAULT 0,
        quantity INT NOT NULL DEFAULT 0,
        sku VARCHAR(50) UNIQUE,
        image VARCHAR(255) NOT NULL,
        images TEXT,
        featured TINYINT(1) DEFAULT 0,
        ar_model VARCHAR(255),
        condition ENUM('new', 'used', 'refurbished') DEFAULT 'new',
        rating DECIMAL(3,2) DEFAULT 0,
        rating_count INT DEFAULT 0,
        views INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (vendor_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES categories(id),
        INDEX idx_vendor (vendor_id),
        INDEX idx_category (category_id),
        INDEX idx_slug (slug),
        FULLTEXT idx_search (name, description)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    // Table des attributs produits
    "CREATE TABLE IF NOT EXISTS product_attributes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        attribute_name VARCHAR(50) NOT NULL,
        attribute_value TEXT NOT NULL,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        INDEX idx_product (product_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    // Table des variations produits
    "CREATE TABLE IF NOT EXISTS product_variations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        variation_type VARCHAR(50) NOT NULL,
        variation_value VARCHAR(50) NOT NULL,
        price_modifier DECIMAL(12,2) DEFAULT 0,
        quantity INT NOT NULL DEFAULT 0,
        sku VARCHAR(50),
        image VARCHAR(255),
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        INDEX idx_product (product_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    // Table des avis produits
    "CREATE TABLE IF NOT EXISTS product_reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        user_id INT NOT NULL,
        rating TINYINT(1) NOT NULL,
        title VARCHAR(100),
        comment TEXT,
        images TEXT,
        verified_purchase TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_product (product_id),
        INDEX idx_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    // Table des paniers
    "CREATE TABLE IF NOT EXISTS carts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        variation_id INT,
        quantity INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        FOREIGN KEY (variation_id) REFERENCES product_variations(id) ON DELETE SET NULL,
        UNIQUE KEY uniq_cart_item (user_id, product_id, variation_id),
        INDEX idx_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    // Table des commandes
    "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_number VARCHAR(20) NOT NULL UNIQUE,
        user_id INT NOT NULL,
        address_id INT NOT NULL,
        subtotal DECIMAL(12,2) NOT NULL,
        shipping DECIMAL(10,2) NOT NULL,
        tax DECIMAL(10,2) DEFAULT 0,
        discount DECIMAL(10,2) DEFAULT 0,
        total DECIMAL(12,2) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
        status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
        tracking_number VARCHAR(100),
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (address_id) REFERENCES addresses(id),
        INDEX idx_user (user_id),
        INDEX idx_order_number (order_number)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    // Table des articles commandés
    "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        variation_id INT,
        product_name VARCHAR(100) NOT NULL,
        product_image VARCHAR(255) NOT NULL,
        price DECIMAL(12,2) NOT NULL,
        quantity INT NOT NULL,
        discount DECIMAL(5,2) DEFAULT 0,
        total DECIMAL(12,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id),
        FOREIGN KEY (variation_id) REFERENCES product_variations(id) ON DELETE SET NULL,
        INDEX idx_order (order_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    // Table des paiements
    "CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        amount DECIMAL(12,2) NOT NULL,
        transaction_id VARCHAR(100),
        payment_method VARCHAR(50) NOT NULL,
        status ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL,
        payment_details TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        INDEX idx_order (order_id),
        INDEX idx_transaction (transaction_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    // Table des livraisons
    "CREATE TABLE IF NOT EXISTS shipments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        shipping_method VARCHAR(50) NOT NULL,
        tracking_number VARCHAR(100),
        status ENUM('pending', 'shipped', 'in_transit', 'delivered') DEFAULT 'pending',
        estimated_delivery DATE,
        actual_delivery DATE,
        shipping_cost DECIMAL(10,2) NOT NULL,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        INDEX idx_order (order_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    // Table des favoris
    "CREATE TABLE IF NOT EXISTS wishlists (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        UNIQUE KEY uniq_wishlist (user_id, product_id),
        INDEX idx_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    // Table des conversations
    "CREATE TABLE IF NOT EXISTS conversations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user1_id INT NOT NULL,
        user2_id INT NOT NULL,
        last_message TEXT,
        last_message_at TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user1_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (user2_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user1 (user1_id),
        INDEX idx_user2 (user2_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    // Table des messages
    "CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        conversation_id INT NOT NULL,
        sender_id INT NOT NULL,
        message TEXT NOT NULL,
        read_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
        FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_conversation (conversation_id),
        INDEX idx_sender (sender_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    // Table des notifications
    "CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        is_read TINYINT(1) DEFAULT 0,
        url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    // Table des coupons
    "CREATE TABLE IF NOT EXISTS coupons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(20) NOT NULL UNIQUE,
        discount_type ENUM('percentage', 'fixed') NOT NULL,
        discount_value DECIMAL(10,2) NOT NULL,
        min_order DECIMAL(10,2) DEFAULT 0,
        max_discount DECIMAL(10,2),
        start_date DATETIME NOT NULL,
        end_date DATETIME NOT NULL,
        uses_limit INT,
        user_limit INT DEFAULT 1,
        vendor_id INT,
        for_categories TEXT,
        for_products TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (vendor_id) REFERENCES users(id) ON DELETE SET NULL,
        INDEX idx_code (code)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    // Table de l'utilisation des coupons
    "CREATE TABLE IF NOT EXISTS coupon_uses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        coupon_id INT NOT NULL,
        user_id INT NOT NULL,
        order_id INT,
        used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
        INDEX idx_coupon (coupon_id),
        INDEX idx_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
];

// Exécution des requêtes
foreach ($tables as $sql) {
    try {
        $pdo->exec($sql);
    } catch (PDOException $e) {
        error_log("Erreur lors de la création des tables: " . $e->getMessage());
        // Ne pas arrêter le script pour ne pas bloquer l'application
    }
}

// Insertion des données de base (catégories principales)
$checkCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
if ($checkCategories == 0) {
    $categories = [
        ['Mode & Accessoires', 'fas fa-tshirt'],
        ['Électronique', 'fas fa-laptop'],
        ['Maison & Cuisine', 'fas fa-home'],
        ['Beauté & Santé', 'fas fa-spa'],
        ['Téléphones', 'fas fa-mobile-alt'],
        ['Superette', 'fas fa-shopping-basket'],
        ['Enfants & Bébés', 'fas fa-baby'],
        ['Sports & Loisirs', 'fas fa-futbol']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO categories (name, icon, slug) VALUES (?, ?, ?)");
    
    foreach ($categories as $category) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $category[0])));
        $stmt->execute([$category[0], $category[1], $slug]);
    }
}
?>