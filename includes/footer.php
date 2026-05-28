            </main>
        </div>
    </div>

    <!-- Mobile Sidebar JavaScript Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.querySelector('.sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (menuToggle && sidebar && sidebarOverlay) {
                function toggleSidebar() {
                    sidebar.classList.toggle('active');
                    sidebarOverlay.classList.toggle('active');
                }

                menuToggle.addEventListener('click', toggleSidebar);
                sidebarOverlay.addEventListener('click', toggleSidebar);
            }
        });
    </script>
</body>
</html>
