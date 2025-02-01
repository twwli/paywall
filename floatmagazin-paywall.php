document.addEventListener('DOMContentLoaded', function () {
    console.log("paywall.js: DOM is ready.");
    initializePaywall();
});

// Fonction principale pour initialiser le paywall
function initializePaywall() {
    console.log("paywall.js: Initializing paywall...");

    // Sélection des éléments clés
    const paywallContainer = document.getElementById('vs-access-container');
    const paywallLogo = document.getElementById('vs-logo');
    const paywallMessage = document.getElementById('vs-access-message');
    
    // Vérifier si le conteneur du paywall est bien présent
    if (!paywallContainer) {
        console.warn("paywall.js: #vs-access-container not found.");
        return;
    } else {
        console.log("paywall.js: #vs-access-container is present.");
    }

    // Fonction pour fermer le paywall
    function closePaywall() {
        console.log("paywall.js: Closing paywall...");

        // Ajouter la classe .content-visible à #vs-access-container
        paywallContainer.classList.add('content-visible');
        console.log("paywall.js: #vs-access-container now has .content-visible.");

        // Supprimer #vs-logo
        if (paywallLogo) {
            paywallLogo.remove();
            console.log("paywall.js: #vs-logo removed.");
        }

        // Supprimer #vs-access-message
        if (paywallMessage) {
            paywallMessage.remove();
            console.log("paywall.js: #vs-access-message removed.");
        }

        // Sauvegarde dans localStorage
        const expiryDate = new Date();
        expiryDate.setDate(expiryDate.getDate() + 3);
        localStorage.setItem('accessClosed', 'true');
        localStorage.setItem('accessExpiry', expiryDate.getTime());
        console.log("paywall.js: Access closed, expiry date set to:", expiryDate);
    }

    // Méthode alternative pour capturer tous les clics sur #vs-access-close
    document.addEventListener('click', function (event) {
        if (event.target.closest('#vs-access-close')) {
            console.log("paywall.js: Click detected on #vs-access-close.");
            event.preventDefault();
            closePaywall();
        }
    });

    // Vérifier si le paywall doit être supprimé immédiatement (via localStorage)
    const accessClosed = localStorage.getItem('accessClosed');
    const accessExpiry = localStorage.getItem('accessExpiry');

    if (accessClosed === 'true' && accessExpiry && new Date().getTime() < accessExpiry) {
        console.log("paywall.js: Paywall is already closed based on localStorage.");
        closePaywall();
    } else {
        console.log("paywall.js: Paywall is visible.");
    }
}