/**
 * ملف JavaScript للبحث في التصنيفات
 */
document.addEventListener('DOMContentLoaded', function() {
    // البحث في التصنيفات
    const categorySearchInput = document.getElementById('category-search');
    if (categorySearchInput) {
        categorySearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const categoryCheckboxes = document.querySelectorAll('.categories-checkboxes .form-check');
            
            categoryCheckboxes.forEach(function(checkbox) {
                const label = checkbox.querySelector('label').textContent.toLowerCase();
                if (label.includes(searchTerm)) {
                    checkbox.style.display = 'block';
                } else {
                    checkbox.style.display = 'none';
                }
            });
        });
    }
});