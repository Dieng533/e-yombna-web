document.addEventListener('DOMContentLoaded', function() {
    // Filtrage des produits
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const productsContainer = document.getElementById('productsContainer');
    
    if (searchInput && categoryFilter && productsContainer) {
        searchInput.addEventListener('keyup', filterProducts);
        categoryFilter.addEventListener('change', filterProducts);
    }
    
    function filterProducts() {
        const searchText = searchInput.value.toLowerCase();
        const category = categoryFilter.value;
        
        Array.from(productsContainer.children).forEach(productCard => {
            const productText = productCard.textContent.toLowerCase();
            const productCategory = productCard.querySelector('.category-badge').textContent;
            
            const matchesSearch = searchText === '' || productText.includes(searchText);
            const matchesCategory = category === '' || productCategory === category;
            
            productCard.style.display = (matchesSearch && matchesCategory) ? 'block' : 'none';
        });
    }
    
    // Animation des cards
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const img = this.querySelector('.product-image');
            if (img) img.style.transform = 'scale(1.03)';
        });
        
        card.addEventListener('mouseleave', function() {
            const img = this.querySelector('.product-image');
            if (img) img.style.transform = 'scale(1)';
        });
    });
});