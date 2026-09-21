    </main>
    
    <footer class="bg-white border-top py-3 px-4 text-center text-muted small">
        &copy; <?= date('Y') ?> Finchskills Institute. Admin Dashboard System.
    </footer>
</div>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebarToggle = document.getElementById('sidebarToggle');
    const adminSidebar = document.getElementById('adminSidebar');
    if (sidebarToggle && adminSidebar) {
        sidebarToggle.addEventListener('click', () => {
            adminSidebar.classList.toggle('show');
        });
    }
</script>
</body>
</html>
