<?php
/**
 * Plugin Name: FloatMagazin Paywall
 * Plugin URI:  https://floatmagazin.de
 * Description: Insertion automatique d'un paywall dans 4 CPTs (boote, leute, orte, dinge) selon des réglages personnalisables.
 * Version:     1.0
 * Author:      Votre Nom
 * Author URI:  https://floatmagazin.de
 * License:     GPL2
 */

if (!defined('ABSPATH')) {
    exit;
}

// Inclure le fichier contenant la page de réglages
require_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';

/**
 * Enfile les CSS/JS si nécessaire.
 */
function floatmagazin_paywall_enqueue_assets() {
    // Vérifie si on affiche un CPT parmi ceux ciblés
    if (is_singular(array('boote','leute','orte','dinge'))) {
        wp_enqueue_style(
            'floatmagazin-paywall-style',
            plugins_url('assets/paywall.css', __FILE__),
            array(),
            '1.0'
        );
        wp_enqueue_script(
            'floatmagazin-paywall-script',
            plugins_url('assets/paywall.js', __FILE__),
            array(),
            '1.0',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'floatmagazin_paywall_enqueue_assets');

/**
 * Filtre the_content : insère le paywall si les conditions sont remplies.
 */
function floatmagazin_paywall_inject($content) {
    // Vérifie si on est dans la boucle, sur l'interface publique,
    // et si on affiche bien l'un des 4 CPTs.
    if (!is_singular(array('boote','leute','orte','dinge')) || !in_the_loop() || is_admin()) {
        return $content;
    }

    // Récupère les réglages depuis la base de données
    $options = get_option('floatmagazin_paywall_options', array());
    $texte_paywall     = isset($options['texte_paywall']) ? $options['texte_paywall'] : '';
    $paragraphe_cible  = isset($options['paragraphe_cible']) ? (int)$options['paragraphe_cible'] : 2;
    $age_minimum       = isset($options['age_minimum']) ? (int)$options['age_minimum'] : 14; // défaut : 14 jours

    // Vérifie la date de publication de l'article courant
    $date_limite = strtotime("-{$age_minimum} days");
    $date_publication = get_the_time('U');
    if ($date_publication > $date_limite) {
        // L'article est trop récent, on n'ajoute pas de paywall
        return $content;
    }

    // Découpe le contenu en paragraphes
    $paragraphes = explode('</p>', $content);
    if (count($paragraphes) < $paragraphe_cible) {
        // Si le nombre de paragraphes est insuffisant, on ne modifie rien
        return $content;
    }

    // Code HTML principal du paywall (avec le texte personnalisé)
    $paywall_html = '
    <div id="vs-access-container">
        <a id="vs-logo" href="https://floatmagazin.de" rel="nofollow" title="Float Magazin">
            <svg enable-background="new 0 0 677 221.1" viewBox="0 0 677 221.1" xmlns="http://www.w3.org/2000/svg">
                    <path d="m105.3 44.8h19.7v-44.8h-27.4c-25.6 0-72.6 6.2-72.6 58.1v3.7h-19.4v41.7h19.4v74.3h-25v43.2h112.8v-43.2h-34.3v-74.3h29.3v-41.7h-29.3v-2.2c0-13.6 16.3-14.8 26.8-14.8m54.4 176h49.4v-44.8h-11.7c-4.3 0-6.8-2.5-6.8-6.8v-146.7c0-15.7-6.8-22.5-22.6-22.5h-30.9v51.2.4 146.7c.1 15.7 6.9 22.5 22.6 22.5m181.9-79c0 23.8-16.7 38.3-36.1 38.3-19.5 0-36.1-14.5-36.1-38.3 0-24.1 16.7-39.2 36.1-39.2 19.4-.1 36.1 15.1 36.1 39.2m53.8 0c0-50-39.8-83.7-90.2-83.7-49.7 0-89.6 33.7-89.6 83.7 0 26.5 11.3 48.2 29.4 62.8 7-3.1 14.8-5.4 23.9-5.4 14.5 0 25.7 6 35.6 11.2 9.3 4.9 17.3 9.2 27.5 9.2 3.3 0 6.4-.5 9.4-1.3 31.9-11.8 54-39.5 54-76.5m105 14.8c0 13-12 28.1-27.2 28.1-9.9 0-15.1-6.8-15.1-13.9 0-13.3 19.5-18.5 36.4-18.5h5.9zm71.3 19.5h-11.7c-4.3 0-6.8-2.5-6.8-6.8v-46.3c0-37.7-18.2-64.9-75.7-64.9-16.4 0-61.5 2.2-61.5 37.4v16.5h48.8v-6c0-6.2 7.7-7.7 14.5-7.7 13.3 0 20.4 5.6 20.4 20.7v2.8h-2.2c-24.4 0-92.4 4.3-92.4 54.7 0 9.8 2.6 18.5 7.2 25.7 6.8 2.2 12.8 5.4 18.3 8.3 9.3 4.9 17.3 9.2 27.5 9.2s18.2-4.3 27.5-9.2c9.9-5.3 21.1-11.2 35.6-11.2s25.7 6 35.6 11.2c5.1 2.7 9.8 5.2 14.7 6.9zm69.9-22.2v-51.5h33.3v-41.6h-33.3v-42.8h-51.8v42.8h-21.9v41.6h20.3v58.6c0 53.3 49 60.1 74.3 60.1 8.6 0 14.5-.9 14.5-.9v-46.2s-2.8.3-7.1.3c-10.2 0-28.3-2.5-28.3-20.4">
                </path>
            </svg>
        </a>
        <div id="vs-access-message">
            <div>
                <h3>float lebt von Luft und Liebe</h3>
                <span>' . esc_html($texte_paywall) . '</span>
                <a id="vs-access-btn" href="https://floatmagazin.de/float-friends/">Ich bin dabei!</a>
                <button id="vs-access-close">
                    <!-- Bouton de fermeture (SVG) -->
                </button>
            </div>
        </div>
        <div id="vs-content">
            <!-- Le contenu suivant est protégé -->
    ';

    // Fermeture du paywall
    $paywall_html_close = '
        </div>
    </div>';

    // Insère le bloc paywall après le paragraphe défini
    $nouveau_contenu = array();
    foreach ($paragraphes as $index => $paragraph) {
        if (!empty(trim($paragraph))) {
            $paragraph .= '</p>';
        }
        $nouveau_contenu[] = $paragraph;

        // Injection après le "paragraphe_cible"
        if ($index === ($paragraphe_cible - 1)) {
            $nouveau_contenu[] = $paywall_html;
        }
    }

    // Reconstiture le contenu final et ferme la balise du paywall
    $nouveau_contenu = implode('', $nouveau_contenu);
    $nouveau_contenu .= $paywall_html_close;

    return $nouveau_contenu;
}
add_filter('the_content', 'floatmagazin_paywall_inject', 999);