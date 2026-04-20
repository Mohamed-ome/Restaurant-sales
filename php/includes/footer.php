</div> <!-- end main-content -->

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Lucide init -->
    <script>
      lucide.createIcons();

      // Theme Management
      function toggleTheme() {
        const body = document.body;
        const icon = document.querySelector('#themeToggle i');
        const isLight = body.classList.toggle('light-mode');
        
        localStorage.setItem('theme', isLight ? 'light' : 'dark');
        updateThemeIcon(isLight);
      }

      function updateThemeIcon(isLight) {
        const icon = document.querySelector('#themeToggle i');
        if (icon) {
          icon.setAttribute('data-lucide', isLight ? 'moon' : 'sun');
          icon.classList.remove('text-amber-500', 'text-zinc-500');
          icon.classList.add(isLight ? 'text-zinc-500' : 'text-amber-500');
          lucide.createIcons();
        }
      }

      // Restore Theme
      (function() {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
          document.body.classList.add('light-mode');
          updateThemeIcon(true);
        }
      })();
    </script>
</body>
</html>
