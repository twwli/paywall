document.addEventListener('DOMContentLoaded', function () {
    console.log("paywall.js: DOM is ready.");
    attachPaywallCloseEvent();
    checkPaywallState();
});

// **Attacher l'événement au bouton de fermeture**
function attachPaywallCloseEvent() {
    console.log("paywall.js: Attaching click event to #vs-access-close...");

    document.addEventListener('click', function (event) {
        const closeBtn = event.target.closest('#vs-access-close');
        if (closeBtn) {
            console.log("paywall.js: Click detected on #vs-access-close.");
            event.preventDefault();
            closePaywall();
        }
    });
}

// **Fermer le paywall immédiatement**
function closePaywall() {
    console.log("paywall.js: Closing paywall...");

    const paywallContainer = document.getElementById('vs-access-container');
    const paywallLogo = document.getElementById('vs-logo');
    const paywallMessage = document.getElementById('vs-access-message');

    if (paywallContainer) {
        paywallContainer.classList.add('content-visible');
        console.log("paywall.js: #vs-access-container now has .content-visible.");
    }

    if (paywallLogo) {
        paywallLogo.remove();
        console.log("paywall.js: #vs-logo removed.");
    }

    if (paywallMessage) {
        paywallMessage.remove();
        console.log("paywall.js: #vs-access-message removed.");
    }

    // **Stocker l’état du paywall dans localStorage**
    localStorage.setItem('accessClosed', 'true');
    console.log("paywall.js: Paywall state saved in localStorage.");
}

// **Vérifier si le paywall doit être caché immédiatement**
function checkPaywallState() {
    console.log("paywall.js: Checking paywall state...");
    if (localStorage.getItem('accessClosed') === 'true') {
        closePaywall();
    }
}