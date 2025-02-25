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

// **Fermer le paywall immédiatement et stocker l'horodatage**
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

    // **Stocker l’horodatage de fermeture dans localStorage**
    localStorage.setItem('accessClosed', Date.now());
    console.log("paywall.js: Paywall state saved in localStorage with timestamp.");
}

// **Vérifier si le paywall doit être caché ou réactivé après 12 heures**
function checkPaywallState() {
    console.log("paywall.js: Checking paywall state...");
    
    const accessClosedTimestamp = localStorage.getItem('accessClosed');
    if (accessClosedTimestamp) {
        const elapsedTime = Date.now() - parseInt(accessClosedTimestamp, 10);
        const twelveHours = 12 * 60 * 60 * 1000; // 12 heures en millisecondes

        if (elapsedTime < twelveHours) {
            closePaywall();
        } else {
            localStorage.removeItem('accessClosed'); // Supprime l'entrée après 12h
            console.log("paywall.js: Paywall reset after 12 hours.");
        }
    }
}
