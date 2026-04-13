document.addEventListener('DOMContentLoaded', function () {
    // Balance show/hide toggle
    const toggleBtn = document.getElementById('toggle-balance');
    const balanceEl = document.getElementById('wallet-balance');

    if (!toggleBtn || !balanceEl) return; // Exit if elements not found

    let hidden = false;
    const realBalance = balanceEl.textContent.trim();

    toggleBtn.addEventListener('click', function () {
        hidden = !hidden;

        if (hidden) {
            balanceEl.textContent = '₦••••••';
            toggleBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
            balanceEl.textContent = realBalance;
            toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
        }
    });
});

// Global Helpers
function copyToClipboard(text, btn) {
    if (!text) return;
    
    const originalContent = btn.innerHTML;
    
    navigator.clipboard.writeText(text).then(() => {
        btn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Copied!';
        btn.classList.remove('btn-light');
        btn.classList.add('btn-success', 'text-white');
        
        setTimeout(() => {
            btn.innerHTML = originalContent;
            btn.classList.remove('btn-success', 'text-white');
            btn.classList.add('btn-light');
        }, 2000);
    });
}
