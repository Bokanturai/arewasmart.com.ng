document.addEventListener('DOMContentLoaded', function () {
    // Balance show/hide toggle
    const toggleBtn = document.getElementById('toggle-balance');
    const balanceEl = document.getElementById('wallet-balance');

    if (!toggleBtn || !balanceEl) return; // Exit if elements not found

    // Set the initial balance on the data attribute
    if (!balanceEl.hasAttribute('data-real-balance')) {
        balanceEl.setAttribute('data-real-balance', balanceEl.textContent.trim());
    }

    let hidden = false;

    toggleBtn.addEventListener('click', function () {
        hidden = !hidden;

        if (hidden) {
            balanceEl.textContent = '₦••••••';
            toggleBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
            balanceEl.textContent = balanceEl.getAttribute('data-real-balance');
            toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
        }
    });

    // Listen for custom wallet balance updates (e.g. from the real-time credit check poll)
    document.addEventListener('wallet-balance-updated', function (e) {
        const formattedBalance = '₦' + parseFloat(e.detail.balance).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        balanceEl.setAttribute('data-real-balance', formattedBalance);
        if (!hidden) {
            balanceEl.textContent = formattedBalance;
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
