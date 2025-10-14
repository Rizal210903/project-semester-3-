    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"></script>
    <script>
    function showCategory(category) {
        const items = document.querySelectorAll('.gallery-item');
        items.forEach(item => {
            item.style.display = 'none';
            if (category === 'all' || item.classList.contains(category)) {
                item.style.display = 'block';
            }
        });
    }
    </script>
    <footer class="bg-light text-center p-3 mt-5" style="background: #E0F6FF; color: #333;">
        <p class="mb-0">&copy; 2025 TK Pertiwi. Semua hak cipta dilindungi.</p>
    </footer>
</body>
</html>