<?php
/**
 * Plugin Name: FloatMagazin Paywall
 * Plugin URI:  https://floatmagazin.de
 * Description: Automatically inserts a paywall into four custom post types (boote, leute, orte, dinge) with customizable settings: on/off, German/English titles/text, paragraph offset, minimum post age, WPML language, and more.
 * Version:     1.0
 * Author:      Your Name
 * Author URI:  https://floatmagazin.de
 * License:     GPL2
 */

if (!defined('ABSPATH')) {
    exit;
}

// Include the admin settings file (make sure the path is correct).
require_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';

/**
 * Main function to inject the paywall into the_content.
 */
function floatmagazin_paywall_inject($content) {
    // Only run on single pages of the specified CPTs, in the main loop, not in admin
    if (!is_singular(array('boote', 'leute', 'orte', 'dinge')) || !in_the_loop() || is_admin()) {
        return $content;
    }

    // Retrieve plugin options
    $options = get_option('floatmagazin_paywall_options', array());

    // Check the "enable paywall" option
    $paywall_active = isset($options['paywall_active']) ? (bool)$options['paywall_active'] : true;
    if (!$paywall_active) {
        // If paywall is disabled, do nothing
        return $content;
    }

    // Get titles and texts from options
    $titre_paywall      = isset($options['titre_paywall'])      ? $options['titre_paywall']      : 'float lebt von Luft und Liebe';
    $titre_paywall_en   = isset($options['titre_paywall_en'])   ? $options['titre_paywall_en']   : 'float lives from air and love';
    $texte_paywall      = isset($options['texte_paywall'])      ? $options['texte_paywall']      : '';
    $texte_paywall_en   = isset($options['texte_paywall_en'])   ? $options['texte_paywall_en']   : '';

    // Other settings
    $paragraphe_cible   = isset($options['paragraphe_cible'])   ? (int)$options['paragraphe_cible'] : 2;
    $age_minimum        = isset($options['age_minimum'])        ? (int)$options['age_minimum']       : 14;
    $lang_select        = isset($options['lang_select'])         ? $options['lang_select']            : 'de';

    // Detect current WPML language, default to 'de' if not present
    if (defined('ICL_LANGUAGE_CODE')) {
        $current_lang = ICL_LANGUAGE_CODE; // e.g. 'de', 'en', etc.
    } else {
        $current_lang = 'de';
    }

    $btn_label = 'Ich bin dabei!';
    if ($current_lang === 'en') {
        $btn_label = "I'm in!";
    }

    // Choose which title to display based on current language
    $titre_paywall_final = $titre_paywall;
    if ($current_lang === 'en' && !empty($titre_paywall_en)) {
        $titre_paywall_final = $titre_paywall_en;
    }

    // Choose which text to display based on current language
    $texte_paywall_final = $texte_paywall;
    if ($current_lang === 'en' && !empty($texte_paywall_en)) {
        $texte_paywall_final = $texte_paywall_en;
    }

    // Apply the language restriction set in the admin
    switch ($lang_select) {
        case 'de':
            // If user selected "German only", we skip if current_lang != 'de'
            if ($current_lang !== 'de') {
                return $content;
            }
            break;
        case 'en':
            // If user selected "English only", we skip if current_lang != 'en'
            if ($current_lang !== 'en') {
                return $content;
            }
            break;
        case 'all':
        default:
            // No restriction
            break;
    }

    // Check post age: only insert if older than X days
    $date_limit       = strtotime("-{$age_minimum} days");
    $publication_date = get_the_time('U');
    if ($publication_date > $date_limit) {
        // If it's more recent than the threshold, do not insert
        return $content;
    }

    // Split the content into paragraphs
    $paragraphs = explode('</p>', $content);
    if (count($paragraphs) < $paragraphe_cible) {
        // If we don't have enough paragraphs, skip
        return $content;
    }

    // Build the paywall block (start)
    ob_start();
    ?>
    <div id="vs-access-container">
        <!-- Paywall heading/message section -->
        <a id="vs-logo" href="https://floatmagazin.de" rel="nofollow" title="Float Magazin">
                <svg enable-background="new 0 0 677 221.1" viewBox="0 0 677 221.1" xmlns="http://www.w3.org/2000/svg">
                    <path d="m105.3 44.8h19.7v-44.8h-27.4c-25.6 0-72.6 6.2-72.6 58.1v3.7h-19.4v41.7h19.4v74.3h-25v43.2h112.8v-43.2h-34.3v-74.3h29.3v-41.7h-29.3v-2.2c0-13.6 16.3-14.8 26.8-14.8m54.4 176h49.4v-44.8h-11.7c-4.3 0-6.8-2.5-6.8-6.8v-146.7c0-15.7-6.8-22.5-22.6-22.5h-30.9v51.2.4 146.7c.1 15.7 6.9 22.5 22.6 22.5m181.9-79c0 23.8-16.7 38.3-36.1 38.3-19.5 0-36.1-14.5-36.1-38.3 0-24.1 16.7-39.2 36.1-39.2 19.4-.1 36.1 15.1 36.1 39.2m53.8 0c0-50-39.8-83.7-90.2-83.7-49.7 0-89.6 33.7-89.6 83.7 0 26.5 11.3 48.2 29.4 62.8 7-3.1 14.8-5.4 23.9-5.4 14.5 0 25.7 6 35.6 11.2 9.3 4.9 17.3 9.2 27.5 9.2 3.3 0 6.4-.5 9.4-1.3 31.9-11.8 54-39.5 54-76.5m105 14.8c0 13-12 28.1-27.2 28.1-9.9 0-15.1-6.8-15.1-13.9 0-13.3 19.5-18.5 36.4-18.5h5.9zm71.3 19.5h-11.7c-4.3 0-6.8-2.5-6.8-6.8v-46.3c0-37.7-18.2-64.9-75.7-64.9-16.4 0-61.5 2.2-61.5 37.4v16.5h48.8v-6c0-6.2 7.7-7.7 14.5-7.7 13.3 0 20.4 5.6 20.4 20.7v2.8h-2.2c-24.4 0-92.4 4.3-92.4 54.7 0 9.8 2.6 18.5 7.2 25.7 6.8 2.2 12.8 5.4 18.3 8.3 9.3 4.9 17.3 9.2 27.5 9.2s18.2-4.3 27.5-9.2c9.9-5.3 21.1-11.2 35.6-11.2s25.7 6 35.6 11.2c5.1 2.7 9.8 5.2 14.7 6.9zm69.9-22.2v-51.5h33.3v-41.6h-33.3v-42.8h-51.8v42.8h-21.9v41.6h20.3v58.6c0 53.3 49 60.1 74.3 60.1 8.6 0 14.5-.9 14.5-.9v-46.2s-2.8.3-7.1.3c-10.2 0-28.3-2.5-28.3-20.4">
                </path>
            </svg>
        </a>
        <div id="vs-access-message">
            <div>
                <h3><?php echo esc_html($titre_paywall_final); ?></h3>
                <span><?php echo nl2br(esc_html($texte_paywall_final)); ?></span>
                <!-- Call to action button -->
                <a id="vs-access-btn" href="https://floatmagazin.de/float-friends/">
                    <?php echo esc_html($btn_label); ?>
                </a>
                <!-- Close button -->
                <button id="vs-access-close">
                    <svg height="16.971" viewBox="0 0 16.971 16.971" width="16.971" xmlns="http://www.w3.org/2000/svg">
                        <g fill="none" stroke="#1a1a1a" stroke-linecap="round" stroke-width="2">
                            <path d="m0 0h20" transform="matrix(.70710678 .70710678 -.70710678 .70710678 1.4137717 1.41472351724)"></path>
                            <path d="m0 0v20" transform="matrix(.70710678 .70710678 -.70710678 .70710678 15.5559073 1.41472351724)"></path>
                        </g>
                    </svg>
                </button>
            </div>
        </div>
        <!-- Protected content starts here -->
        <div id="vs-content">
    <?php
    $paywall_html = ob_get_clean();

    // Paywall closing block
    $paywall_html_close = '
        </div>
    </div>';

    // Inject the paywall block after the specified paragraph
    $new_content = array();
    foreach ($paragraphs as $index => $paragraph) {
        $paragraph = trim($paragraph);
        if (!empty($paragraph)) {
            $paragraph .= '</p>';
        }
        $new_content[] = $paragraph;

        // Insert the paywall right after the chosen paragraph
        if ($index === ($paragraphe_cible - 1)) {
            $new_content[] = $paywall_html;
        }
    }

    // Recombine and close
    $new_content = implode('', $new_content);
    $new_content .= $paywall_html_close;

    return $new_content;
}
add_filter('the_content', 'floatmagazin_paywall_inject', 999);


/**
 * Enqueue paywall CSS/JS only for the 4 custom post types.
 */
function floatmagazin_paywall_enqueue_assets() {
    if (is_singular(array('boote', 'leute', 'orte', 'dinge'))) {
        // Refaire le test d'âge minimum
        $options = get_option('floatmagazin_paywall_options', array());
        $age_minimum = isset($options['age_minimum']) ? (int)$options['age_minimum'] : 14;
        $publication_date = get_the_time('U');
        $date_limit = strtotime("-{$age_minimum} days");
        
        if ($publication_date <= $date_limit) {
            // Seulement si l’article est plus vieux que le seuil
            wp_enqueue_style(
                'floatmagazin-paywall-style',
                plugins_url('assets/paywall.css', __FILE__),
                array(),
                '1.2'
            );
            wp_enqueue_script(
                'floatmagazin-paywall-script',
                plugins_url('assets/paywall.js', __FILE__),
                array(),
                '1.3',
                true
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'floatmagazin_paywall_enqueue_assets');