const category = document.getElementById('category_id');
const ccArea = document.getElementById('cc_box');

category.addEventListener('change', () => {
    if (category.value == '1') {
        ccArea.style.display = 'block';
    } else {
        ccArea.style.display = 'none';
    }
});