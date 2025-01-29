document.addEventListener('DOMContentLoaded', function () {
    console.log("paywall-simple.js: DOM is ready.");

    // Sélection des éléments clés
    const paywallContainer = document.getElementById('vs-access-container');
    const paywallLogo = document.getElementById('vs-logo');
    const paywallMessage = document.getElementById('vs-access-message');
    const closeBtn = document.getElementById('vs-access-close');

    // Vérification initiale des éléments
    if (!paywallContainer) {
        console.warn("paywall-simple.js: #vs-access-container NOT found. Paywall may not be present.");
        return;
    }
    console.log("paywall-simple.js: #vs-access-container detected.");

    if (!closeBtn) {
        console.warn("paywall-simple.js: #vs-access-close button NOT found.");
    } else {
        console.log("paywall-simple.js: #vs-access-close button detected.");
    }

    // Fonction pour activer immédiatement le contenu et supprimer les éléments du paywall
    function closePaywall() {
        console.log("paywall-simple.js: Closing paywall...");

        if (paywallContainer) {
            paywallContainer.classList.add('content-visible');
            console.log("paywall-simple.js: Added .content-visible to #vs-access-container.");
        } else {
            console.warn("paywall-simple.js: #vs-access-container is missing, cannot add .content-visible.");
        }

        // Supprimer immédiatement #vs-logo s'il existe
        if (paywallLogo) {
            paywallLogo.remove();
            console.log("paywall-simple.js: #vs-logo removed.");
        } else {
            console.warn("paywall-simple.js: #vs-logo not found, skipping.");
        }

        // Supprimer immédiatement #vs-access-message s'il existe
        if (paywallMessage) {
            paywallMessage.remove();
            console.log("paywall-simple.js: #vs-access-message removed.");
        } else {
            console.warn("paywall-simple.js: #vs-access-message not found, skipping.");
        }

        // Sauvegarde dans localStorage pour empêcher le retour du paywall
        const expiryDate = new Date();
        expiryDate.setDate(expiryDate.getDate() + 3);
        localStorage.setItem('accessClosed', 'true');
        localStorage.setItem('accessExpiry', expiryDate.getTime());
        console.log("paywall-simple.js: Access closed, expiry date set to:", expiryDate);
    }

    // Attacher l'événement au bouton #vs-access-close (si trouvé)
    if (closeBtn) {
        closeBtn.addEventListener('click', function (event) {
            event.preventDefault();
            console.log("paywall-simple.js: Click detected on #vs-access-close.");
            closePaywall();
        });
    }

    // Vérification du localStorage pour cacher immédiatement le paywall si déjà fermé
    const accessClosed = localStorage.getItem('accessClosed');
    const accessExpiry = localStorage.getItem('accessExpiry');

    if (accessClosed === 'true' && accessExpiry && new Date().getTime() < accessExpiry) {
        console.log("paywall-simple.js: Paywall is already closed based on localStorage.");
        closePaywall();
    } else {
        console.log("paywall-simple.js: Paywall is visible.");
    }
});