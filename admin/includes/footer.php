    </main>
    
    <!-- Clean Minimal Footer -->
    <footer class="border-t border-slate-200 bg-white py-3 px-4 sm:px-6 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <span class="font-bold text-[#0e1e2e]">YourGoodGuide</span>
            <span class="text-slate-300">/</span>
            <span>Admin Console &copy; <?= date('Y') ?></span>
        </div>
        <div class="flex items-center gap-3 text-[11px] text-slate-400">
            <span class="inline-flex items-center gap-1 text-emerald-600 font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Online
            </span>
            <span>v2.5</span>
        </div>
    </footer>
</div>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- UI Interaction Scripts -->
<script>
    // Sidebar Mobile Toggle
    const sidebarOpenBtn = document.getElementById('sidebarOpenBtn');
    const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
    const adminSidebar = document.getElementById('adminSidebar');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');

    function openSidebar() {
        if (adminSidebar && sidebarBackdrop) {
            adminSidebar.classList.remove('-translate-x-full');
            sidebarBackdrop.classList.remove('hidden');
            document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
        }
    }

    function closeSidebar() {
        if (adminSidebar && sidebarBackdrop) {
            adminSidebar.classList.add('-translate-x-full');
            sidebarBackdrop.classList.add('hidden');
            document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
        }
    }

    if (sidebarOpenBtn) sidebarOpenBtn.addEventListener('click', openSidebar);
    if (sidebarCloseBtn) sidebarCloseBtn.addEventListener('click', closeSidebar);
    if (sidebarBackdrop) sidebarBackdrop.addEventListener('click', closeSidebar);

    // Profile Dropdown Toggle
    const profileBtn = document.getElementById('profileDropdownBtn');
    const profileMenu = document.getElementById('profileDropdownMenu');
    if (profileBtn && profileMenu) {
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            profileMenu.classList.toggle('hidden');
            if (notifMenu) notifMenu.classList.add('hidden');
        });
    }

    // Notification Dropdown Toggle
    const notifBtn = document.getElementById('notificationDropdownBtn');
    const notifMenu = document.getElementById('notificationDropdownMenu');
    if (notifBtn && notifMenu) {
        notifBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notifMenu.classList.toggle('hidden');
            if (profileMenu) profileMenu.classList.add('hidden');
        });
    }

    // Close Dropdowns on Outside Click
    document.addEventListener('click', (e) => {
        if (profileMenu && !profileMenu.contains(e.target) && !profileBtn.contains(e.target)) {
            profileMenu.classList.add('hidden');
        }
        if (notifMenu && !notifMenu.contains(e.target) && !notifBtn.contains(e.target)) {
            notifMenu.classList.add('hidden');
        }
    });

    // Close on Escape Key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeSidebar();
            if (profileMenu) profileMenu.classList.add('hidden');
            if (notifMenu) notifMenu.classList.add('hidden');
        }
    });
</script>
</body>
</html>
