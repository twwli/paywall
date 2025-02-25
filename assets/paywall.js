document.addEventListener('DOMContentLoaded', function () {
    console.log("paywall.js: DOM is ready.");
    attachPaywallCloseEvent();
    checkPaywallState();
});

// Attacher l'événement au bouton de fermeture
function attachPaywallCloseEvent() {
    console.log("paywall.js: Attaching click event to #vs-access-close...");

    document.addEventListener('click', function (event) {
        const closeBtn = event.target.closest('#vs-access-close');
        if (closeBtn) {
            console.log("paywall.js: Click detected on #vs-access-close.");
            event.preventDefault();
            closePaywall(); // Lorsque l'utilisateur clique, on « ferme » le paywall
        }
    });
}

// Ouvrir (afficher) le paywall et masquer le contenu/pagination
function openPaywall() {
    console.log("paywall.js: Opening paywall...");

    const paywallContainer = document.getElementById('vs-access-container');
    const paywallLogo = document.getElementById('vs-logo');
    const paywallMessage = document.getElementById('vs-access-message');
    const bottomPagination = document.getElementById('bottom-pagination');

    // Masquer le contenu
    if (paywallContainer) {
        paywallContainer.classList.remove('content-visible');
        console.log("paywall.js: #vs-access-container : content-visible removed.");
    }

    // Masquer la pagination du bas
    if (bottomPagination) {
        bottomPagination.style.setProperty('display', 'none', 'important');
        console.log("paywall.js: Bottom pagination hidden because paywall is open.");
    } else {
        waitForBottomPaginationAndHide();
    }
}

// Fermer (cacher) le paywall et afficher le contenu/pagination
function closePaywall() {
    console.log("paywall.js: Closing paywall...");

    const paywallContainer = document.getElementById('vs-access-container');
    const paywallLogo = document.getElementById('vs-logo');
    const paywallMessage = document.getElementById('vs-access-message');
    const bottomPagination = document.getElementById('bottom-pagination');

    // Supprimer les éléments du paywall
    if (paywallLogo) {
        paywallLogo.remove();
        console.log("paywall.js: #vs-logo removed.");
    }

    if (paywallMessage) {
        paywallMessage.remove();
        console.log("paywall.js: #vs-access-message removed.");
    }

    // Afficher le contenu
    if (paywallContainer) {
        paywallContainer.classList.add('content-visible');
        console.log("paywall.js: #vs-access-container now has .content-visible.");
    }

    // Réafficher la pagination du bas
    if (bottomPagination) {
        bottomPagination.style.display = 'block';
        console.log("paywall.js: Bottom pagination visible again because paywall is closed.");
    }

    // Enregistrer le fait que l'utilisateur a fermé le paywall
    localStorage.setItem('accessClosed', Date.now());
    console.log("paywall.js: Paywall state saved in localStorage with timestamp.");
}

// Observer la pagination si elle est chargée plus tard
function waitForBottomPaginationAndHide() {
    console.log("paywall.js: Waiting for bottom pagination to be added...");

    const observer = new MutationObserver(() => {
        const bottomPagination = document.getElementById('bottom-pagination');
        if (bottomPagination) {
            console.log("paywall.js: Bottom pagination detected by MutationObserver.", bottomPagination);
            bottomPagination.style.setProperty('display', 'none', 'important');
            console.log("paywall.js: Bottom pagination hidden via MutationObserver because paywall is open.");
            observer.disconnect();
        }
    });

    observer.observe(document.body, { childList: true, subtree: true });
}

// Vérifier si le paywall doit être ouvert ou fermé
function checkPaywallState() {
    console.log("paywall.js: Checking paywall state...");

    const accessClosedTimestamp = localStorage.getItem('accessClosed');
    if (accessClosedTimestamp) {
        const elapsedTime = Date.now() - parseInt(accessClosedTimestamp, 10);
        const twelveHours = 12 * 60 * 60 * 1000; // 12 heures en millisecondes

        console.log("paywall.js: Timestamp found in localStorage:", accessClosedTimestamp);
        console.log("paywall.js: Elapsed time since paywall was closed:", elapsedTime);

        if (elapsedTime < twelveHours) {
            // L'utilisateur a déjà fermé le paywall récemment
            console.log("paywall.js: Paywall is still valid. Closing paywall...");
            closePaywall();
        } else {
            // Le délai de 12 heures est dépassé
            console.log("paywall.js: 12 hours elapsed. Resetting paywall.");
            localStorage.removeItem('accessClosed'); 
            openPaywall();
        }
    } else {
        console.log("paywall.js: No timestamp found in localStorage. Opening paywall...");
        // L'utilisateur n’a jamais fermé le paywall -> on l’ouvre
        openPaywall();
    }
}