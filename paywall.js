document.addEventListener('DOMContentLoaded', function() {
    var accessCloseButton = document.getElementById('vs-access-close');
    var accessContainer = document.getElementById('vs-access-container');
    var accessContent = document.getElementById('vs-content');
    var pageLinksContainers = document.querySelectorAll('.page-links-container');

    // Check if the access has already been closed
    var accessClosed = localStorage.getItem('accessClosed');

    // Function to hide the .page-links-container elements
    function hidePageLinksContainers() {
        if (pageLinksContainers.length > 0) {
            pageLinksContainers.forEach(function(container) {
                container.style.display = 'none';
                console.log('.page-links-container hidden');
            });
        }
    }

    // Function to show the .page-links-container elements
    function showPageLinksContainers() {
        if (pageLinksContainers.length > 0) {
            pageLinksContainers.forEach(function(container) {
                container.style.display = 'block';
                console.log('.page-links-container shown');
            });
        }
    }

    // Function to close the access
    function closeAccess() {
        if (accessContainer && accessContent && accessContainer.parentNode) {
            accessContainer.parentNode.insertBefore(accessContent, accessContainer);
            console.log('vs-content moved outside of vs-access-container');
        }

        if (accessContent) {
            accessContent.id = 'vs-content-hidden';
            console.log('vs-content renamed to vs-content-hidden');
        }

        if (accessContainer && accessContainer.parentNode) {
            accessContainer.parentNode.removeChild(accessContainer);
            console.log('vs-access-container removed');
        }

        // Show the .page-links-container elements
        showPageLinksContainers();

        // Save to localStorage for 3 days
        var expiryDate = new Date();
        expiryDate.setDate(expiryDate.getDate() + 3);
        localStorage.setItem('accessClosed', 'true');
        localStorage.setItem('accessExpiry', expiryDate.getTime());
        console.log('Access closed, expiry date set to:', expiryDate);
    }

    // Clear localStorage if the expiry date has passed
    var accessExpiry = localStorage.getItem('accessExpiry');
    if (accessExpiry && new Date().getTime() > accessExpiry) {
        localStorage.removeItem('accessClosed');
        localStorage.removeItem('accessExpiry');
        console.log('Access expiry date passed, localStorage cleared');
        // The access will be displayed again
        accessClosed = null;
    }

    // On page load
    if (accessClosed === 'true') {
        // Access has already been closed, apply changes directly
        if (accessContainer && accessContent && accessContainer.parentNode) {
            accessContainer.parentNode.insertBefore(accessContent, accessContainer);
            console.log('vs-content moved outside of vs-access-container (already closed)');
        }

        if (accessContent) {
            accessContent.id = 'vs-content-hidden';
            console.log('vs-content renamed to vs-content-hidden (already closed)');
        }

        if (accessContainer && accessContainer.parentNode) {
            accessContainer.parentNode.removeChild(accessContainer);
            console.log('vs-access-container removed (already closed)');
        }

        // Show the .page-links-container elements
        showPageLinksContainers();
    } else {
        // Access is visible, hide the .page-links-container elements
        if (accessContainer) {
            hidePageLinksContainers();
        }

        if (accessCloseButton) {
            accessCloseButton.addEventListener('click', function() {
                console.log('vs-access-close button clicked');
                closeAccess();
            });
        } else {
            console.log('vs-access-close button not found');
        }
    }
});