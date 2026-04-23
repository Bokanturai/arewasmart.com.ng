/**
 * Dark Mode Toggle Logic for Arewa Smart
 */

document.addEventListener('DOMContentLoaded', function() {
    const themeToggleBtns = document.querySelectorAll('.theme-toggle-btn');
    const htmlElement = document.documentElement;
    const bodyElement = document.body;
    
    // Function to apply theme
    const applyTheme = (theme) => {
        if (theme === 'dark') {
            bodyElement.setAttribute('data-theme', 'dark');
            htmlElement.setAttribute('data-theme', 'dark');
            themeToggleBtns.forEach(btn => {
                // If it's the dropdown item (has text), update text too
                if (btn.classList.contains('dropdown-item')) {
                    btn.innerHTML = '<i class="ti ti-sun me-2"></i>Light Mode';
                } else {
                    btn.innerHTML = '<i class="ti ti-sun"></i>';
                }
                btn.setAttribute('title', 'Switch to Light Mode');
            });
        } else {
            bodyElement.removeAttribute('data-theme');
            htmlElement.removeAttribute('data-theme');
            themeToggleBtns.forEach(btn => {
                if (btn.classList.contains('dropdown-item')) {
                    btn.innerHTML = '<i class="ti ti-moon me-2"></i>Dark Mode';
                } else {
                    btn.innerHTML = '<i class="ti ti-moon"></i>';
                }
                btn.setAttribute('title', 'Switch to Dark Mode');
            });
        }
    };

    // Load saved theme
    const savedTheme = localStorage.getItem('theme') || 'light';
    applyTheme(savedTheme);

    // Toggle event listener
    themeToggleBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const currentTheme = bodyElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            localStorage.setItem('theme', newTheme);
            applyTheme(newTheme);
        });
    });
});
