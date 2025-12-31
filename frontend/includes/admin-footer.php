<?php
/**
 * Simplified Admin Footer Component
 * Include this file in your pages: <?php include 'sidebar/admin-footer.php'; ?>
 */

// Fetch user theme setting if user is logged in
$footer_theme = 'light'; // Default theme
if (isset($_SESSION['user']['id']) && isset($mysqli)) {
    $footer_user_id = $_SESSION['user']['id'];
    $footer_settings_stmt = $mysqli->prepare("SELECT theme FROM crime_department_user_settings WHERE admin_user_id = ? LIMIT 1");
    $footer_settings_stmt->bind_param("i", $footer_user_id);
    $footer_settings_stmt->execute();
    $footer_settings_result = $footer_settings_stmt->get_result();
    $footer_user_settings = $footer_settings_result->fetch_assoc();
    $footer_settings_stmt->close();

    if ($footer_user_settings && isset($footer_user_settings['theme'])) {
        $footer_theme = $footer_user_settings['theme'];
    }
}
?>

<!-- Admin Footer Component -->
 
<footer class="admin-footer">
    <div class="footer-bottom">

        <div class="footer-copyright">
            <p>&copy; <?php echo date('Y'); ?> Crime Department, AlerTaraQC. All rights reserved.</p>
        </div>

        <div class="footer-legal">
            <a href="#" class="footer-link">Privacy Policy</a>
            <a href="#" class="footer-link">Terms of Service</a>
            <a href="#" class="footer-link">Cookie Policy</a>
        </div>

        <div class="theme-toggle">
            <button class="theme-toggle-btn" data-theme="system">
                <i class="fas fa-desktop"></i>
            </button>
            <button class="theme-toggle-btn" data-theme="light">
                <i class="fas fa-sun"></i>
            </button>
            <button class="theme-toggle-btn" data-theme="dark">
                <i class="fas fa-moon"></i>
            </button>
        </div>

    </div>
</footer>


<script>
    // Theme Toggle functionality - Fixed version
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggleBtns = document.querySelectorAll('.theme-toggle .theme-toggle-btn');
        const htmlElement = document.documentElement;
        
        if (themeToggleBtns.length === 0) {
            console.warn('Theme toggle buttons not found. Check your HTML structure.');
            return;
        }
        
        // Load theme from database
        const dbTheme = '<?php echo htmlspecialchars($footer_theme); ?>';
        const savedTheme = dbTheme === 'auto' ? 'system' : dbTheme;

        htmlElement.setAttribute('data-theme', savedTheme);
        localStorage.setItem('theme', savedTheme);
        updateThemeButtons(savedTheme);
        
        themeToggleBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const theme = btn.getAttribute('data-theme');
                htmlElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
                updateThemeButtons(theme);

                // Apply system theme if selected
                if (theme === 'system') {
                    applySystemTheme();
                }

                // Save theme to database
                const formData = new FormData();
                formData.append('type', 'theme');
                formData.append('theme', theme === 'system' ? 'auto' : theme);

                fetch('../../api/action/update-user-settings.php', {
                    method: 'POST',
                    body: formData
                }).catch(error => console.error('Failed to save theme:', error));

                // Dispatch custom event for other components
                document.dispatchEvent(new CustomEvent('themeChanged', { detail: theme }));
            });
        });
        
        function updateThemeButtons(theme) {
            themeToggleBtns.forEach(btn => {
                if (btn.getAttribute('data-theme') === theme) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }
        
        function applySystemTheme() {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            htmlElement.setAttribute('data-theme', prefersDark ? 'dark' : 'light');
        }
        
        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (localStorage.getItem('theme') === 'system') {
                applySystemTheme();
            }
        });
        
        // Initialize system theme if needed
        if (savedTheme === 'system') {
            applySystemTheme();
        }
    });
</script>