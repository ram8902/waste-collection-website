/**
 * Dark Mode Toggle Functionality
 * Saves theme preference to localStorage
 */

(function () {
    'use strict';

    // Update UI elements based on theme
    function updateUI(theme) {
        const isDark = theme === 'dark';
        const buttons = document.querySelectorAll('.theme-toggle-modern');

        buttons.forEach(btn => {
            const icon = btn.querySelector('.theme-icon');
            const text = btn.querySelector('.theme-text');

            if (isDark) {
                if (icon) icon.textContent = '🌙';
                if (text) text.textContent = 'Dark Mode';
            } else {
                if (icon) icon.textContent = '☀️';
                if (text) text.textContent = 'Light Mode';
            }
        });
    }

    // Initialize theme on page load
    function initTheme() {
        const theme = localStorage.getItem('theme') || 'light';
        if (theme === 'dark') {
            document.body.classList.add('dark-mode');
        } else {
            document.body.classList.remove('dark-mode');
        }
        updateUI(theme);
    }

    // Toggle theme
    function toggleTheme() {
        document.body.classList.toggle('dark-mode');
        let newTheme = 'light';

        if (document.body.classList.contains('dark-mode')) {
            newTheme = 'dark';
        }

        localStorage.setItem('theme', newTheme);
        updateUI(newTheme);
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTheme);
    } else {
        initTheme();
    }

    // Attach toggle function to all theme toggle buttons
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButtons = document.querySelectorAll('.theme-toggle');
        toggleButtons.forEach(function (button) {
            button.addEventListener('click', toggleTheme);
        });
    });

    // Make toggleTheme available globally for inline onclick handlers
    window.toggleTheme = toggleTheme;
})();
