<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Adds the settings page to the WordPress admin interface.
 */
function floatmagazin_paywall_add_admin_menu() {
    add_options_page(
        'Paywall Settings',                // Page Title
        'FloatMagazin Paywall',            // Menu Label
        'manage_options',                  // Required Capability
        'floatmagazin-paywall-settings',   // Slug
        'floatmagazin_paywall_options_page'// Display Callback
    );
}
add_action('admin_menu', 'floatmagazin_paywall_add_admin_menu');

/**
 * Registers the plugin settings.
 */
function floatmagazin_paywall_register_settings() {
    register_setting(
        'floatmagazin_paywall_options_group',   // Options group ID
        'floatmagazin_paywall_options'          // Option name in the database
    );
}
add_action('admin_init', 'floatmagazin_paywall_register_settings');

/**
 * Displays the settings page.
 */
function floatmagazin_paywall_options_page() {
    // Check user capability
    if (!current_user_can('manage_options')) {
        return;
    }

    // Retrieve existing options to pre-fill the form;
    // if absent, we define default values.
    $options = get_option('floatmagazin_paywall_options', array());

    // Securely retrieve each key,
    // defining a default if it does not exist.
    $titre_paywall     = isset($options['titre_paywall'])    ? $options['titre_paywall']    : 'float lebt von Luft und Liebe';
    $texte_paywall     = isset($options['texte_paywall'])    ? $options['texte_paywall']    : '';
    $paragraphe_cible  = isset($options['paragraphe_cible']) ? (int)$options['paragraphe_cible'] : 2;
    $age_minimum       = isset($options['age_minimum'])      ? (int)$options['age_minimum']      : 14;
    $lang_select       = isset($options['lang_select'])       ? $options['lang_select']           : 'de';

    ?>
    <div class="wrap">
        <h1>FloatMagazin Paywall Settings</h1>
        <form method="post" action="options.php">
            <?php
                // Secure the form using WordPress mechanisms
                settings_fields('floatmagazin_paywall_options_group');
                do_settings_sections('floatmagazin_paywall_options_group');
            ?>

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="titre_paywall">Paywall Title</label></th>
                    <td>
                        <input
                            type="text"
                            id="titre_paywall"
                            name="floatmagazin_paywall_options[titre_paywall]"
                            value="<?php echo esc_attr($titre_paywall); ?>"
                            size="50"
                        />
                        <p class="description">
                            Enter the title displayed at the beginning of the paywall block (default: "float lebt von Luft und Liebe").
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="texte_paywall">Paywall Text</label></th>
                    <td>
                        <textarea
                            id="texte_paywall"
                            name="floatmagazin_paywall_options[texte_paywall]"
                            rows="4"
                            cols="50"
                        ><?php echo esc_textarea($texte_paywall); ?></textarea>
                        <p class="description">
                            Enter the text displayed inside the paywall block (e.g., "... und von Dir. Unterstütze uns: Werde jetzt...").
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="paragraphe_cible">Target Paragraph</label></th>
                    <td>
                        <input
                            type="number"
                            id="paragraphe_cible"
                            name="floatmagazin_paywall_options[paragraphe_cible]"
                            value="<?php echo esc_attr($paragraphe_cible); ?>"
                            min="1"
                        />
                        <p class="description">
                            The paragraph number after which the paywall is inserted (default: 2).
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="age_minimum">Minimum Age (days)</label></th>
                    <td>
                        <input
                            type="number"
                            id="age_minimum"
                            name="floatmagazin_paywall_options[age_minimum]"
                            value="<?php echo esc_attr($age_minimum); ?>"
                            min="0"
                        />
                        <p class="description">
                            Number of days after which the post is considered "old," triggering the paywall injection (default: 14).
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="paywall_lang_select">Target Language</label></th>
                    <td>
                        <select
                            id="paywall_lang_select"
                            name="floatmagazin_paywall_options[lang_select]"
                        >
                            <option value="all" <?php selected($lang_select, 'all'); ?>>
                                All languages
                            </option>
                            <option value="de" <?php selected($lang_select, 'de'); ?>>
                                German only (de)
                            </option>
                            <option value="en" <?php selected($lang_select, 'en'); ?>>
                                English only (en)
                            </option>
                        </select>
                        <p class="description">
                            Select the site version on which the paywall should appear.
                            Requires WPML to detect the active language.
                        </p>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}