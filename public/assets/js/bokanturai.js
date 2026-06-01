document.addEventListener('DOMContentLoaded', function () {
    // Balance show/hide toggle
    const toggleBtn = document.getElementById('toggle-balance');
    const balanceEl = document.getElementById('wallet-balance');
    const syncBtn = document.getElementById('sync-balance-btn');

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

    // Handle manual sync button click
    if (syncBtn) {
        syncBtn.addEventListener('click', function () {
            document.dispatchEvent(new CustomEvent('trigger-wallet-balance-sync'));
        });
    }

    // Handle sync animation states
    document.addEventListener('wallet-balance-syncing', function () {
        if (syncBtn) {
            const syncIcon = syncBtn.querySelector('.sync-icon');
            if (syncIcon) syncIcon.classList.add('spinning');
        }
    });

    document.addEventListener('wallet-balance-synced', function () {
        if (syncBtn) {
            const syncIcon = syncBtn.querySelector('.sync-icon');
            if (syncIcon) {
                // Ensure it spins for at least a brief moment so the user feels the action
                setTimeout(() => {
                    syncIcon.classList.remove('spinning');
                }, 500);
            }
        }
    });

    // Listen for custom wallet balance updates
    document.addEventListener('wallet-balance-updated', function (e) {
        const formattedBalance = '₦' + parseFloat(e.detail.balance).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        
        const oldBalance = balanceEl.getAttribute('data-real-balance');
        
        balanceEl.setAttribute('data-real-balance', formattedBalance);
        if (!hidden) {
            balanceEl.textContent = formattedBalance;
        }

        // Trigger flash glow animation if balance changed
        if (oldBalance && oldBalance !== formattedBalance) {
            balanceEl.classList.remove('flash-update');
            void balanceEl.offsetWidth; // Force reflow to restart animation
            balanceEl.classList.add('flash-update');
            setTimeout(() => {
                balanceEl.classList.remove('flash-update');
            }, 800);
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
