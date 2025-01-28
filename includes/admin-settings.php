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
 * Registers the plugin settings with a sanitize callback.
 */
function floatmagazin_paywall_register_settings() {
    register_setting(
        'floatmagazin_paywall_options_group',
        'floatmagazin_paywall_options',
        array(
            'sanitize_callback' => 'floatmagazin_paywall_sanitize_options'
        )
    );
}
add_action('admin_init', 'floatmagazin_paywall_register_settings');

/**
 * This sanitize callback ensures that if the checkbox paywall_active is unchecked,
 * we store 'false' instead of reusing an old value.
 *
 * You can also sanitize other fields here if needed.
 */
function floatmagazin_paywall_sanitize_options($input) {
    // If paywall_active is not set in the form submission, set it to false.
    if (!isset($input['paywall_active'])) {
        $input['paywall_active'] = false;
    } else {
        // Convert the string "1" to a boolean true
        $input['paywall_active'] = (bool)$input['paywall_active'];
    }

    // OPTIONAL: If you want to sanitize text fields further, do so here:
    // if (isset($input['titre_paywall'])) {
    //     $input['titre_paywall'] = sanitize_text_field($input['titre_paywall']);
    // }
    // if (isset($input['titre_paywall_en'])) {
    //     $input['titre_paywall_en'] = sanitize_text_field($input['titre_paywall_en']);
    // }
    // if (isset($input['texte_paywall'])) {
    //     $input['texte_paywall'] = wp_kses_post($input['texte_paywall']);
    // }
    // if (isset($input['texte_paywall_en'])) {
    //     $input['texte_paywall_en'] = wp_kses_post($input['texte_paywall_en']);
    // }
    // etc.

    return $input;
}

/**
 * Displays the settings page.
 */
function floatmagazin_paywall_options_page() {
    // Check user capability
    if (!current_user_can('manage_options')) {
        return;
    }

    // Retrieve existing options (or an empty array if none)
    $options = get_option('floatmagazin_paywall_options', array());

    // Read each option, providing a default value if it's not set
    $paywall_active    = isset($options['paywall_active'])  ? (bool)$options['paywall_active']  : true;
    $titre_paywall     = isset($options['titre_paywall'])   ? $options['titre_paywall']        : 'float lebt von Luft und Liebe';
    $titre_paywall_en  = isset($options['titre_paywall_en'])? $options['titre_paywall_en']     : 'float lives from air and love';
    $texte_paywall     = isset($options['texte_paywall'])   ? $options['texte_paywall']        : '';
    $texte_paywall_en  = isset($options['texte_paywall_en'])? $options['texte_paywall_en']     : '';
    $paragraphe_cible  = isset($options['paragraphe_cible'])? (int)$options['paragraphe_cible']: 2;
    $age_minimum       = isset($options['age_minimum'])     ? (int)$options['age_minimum']     : 14;
    $lang_select       = isset($options['lang_select'])      ? $options['lang_select']          : 'de';
    ?>
    <div class="wrap">
        <h1>FloatMagazin Paywall Settings</h1>
        <form method="post" action="options.php">
            <?php
                // Security: form nonce, etc.
                settings_fields('floatmagazin_paywall_options_group');
                do_settings_sections('floatmagazin_paywall_options_group');
            ?>

            <table class="form-table">
                <!-- Paywall activation -->
                <tr>
                    <th scope="row"><label for="paywall_active">Enable Paywall</label></th>
                    <td>
                        <input
                            type="checkbox"
                            id="paywall_active"
                            name="floatmagazin_paywall_options[paywall_active]"
                            value="1"
                            <?php checked($paywall_active, true); ?>
                        />
                        <p class="description">
                            Uncheck this box to disable the paywall entirely.
                        </p>
                    </td>
                </tr>

                <!-- Title (German) -->
                <tr>
                    <th scope="row"><label for="titre_paywall">Paywall Title (DE)</label></th>
                    <td>
                        <input
                            type="text"
                            id="titre_paywall"
                            name="floatmagazin_paywall_options[titre_paywall]"
                            value="<?php echo esc_attr($titre_paywall); ?>"
                            size="50"
                        />
                        <p class="description">
                            Enter the German title displayed at the top of the paywall block.
                        </p>
                    </td>
                </tr>

                <!-- Title (English) -->
                <tr>
                    <th scope="row"><label for="titre_paywall_en">Paywall Title (EN)</label></th>
                    <td>
                        <input
                            type="text"
                            id="titre_paywall_en"
                            name="floatmagazin_paywall_options[titre_paywall_en]"
                            value="<?php echo esc_attr($titre_paywall_en); ?>"
                            size="50"
                        />
                        <p class="description">
                            Enter the English title displayed at the top of the paywall block.
                        </p>
                    </td>
                </tr>

                <!-- Paywall text (German) -->
                <tr>
                    <th scope="row"><label for="texte_paywall">Paywall Text (DE)</label></th>
                    <td>
                        <textarea
                            id="texte_paywall"
                            name="floatmagazin_paywall_options[texte_paywall]"
                            rows="4"
                            cols="50"
                        ><?php echo esc_textarea($texte_paywall); ?></textarea>
                        <p class="description">
                            Text displayed for the German version (or default if WPML is not present).
                        </p>
                    </td>
                </tr>

                <!-- Paywall text (English) -->
                <tr>
                    <th scope="row"><label for="texte_paywall_en">Paywall Text (EN)</label></th>
                    <td>
                        <textarea
                            id="texte_paywall_en"
                            name="floatmagazin_paywall_options[texte_paywall_en]"
                            rows="4"
                            cols="50"
                        ><?php echo esc_textarea($texte_paywall_en); ?></textarea>
                        <p class="description">
                            Text displayed for the English version (if WPML language is "en").
                        </p>
                    </td>
                </tr>

                <!-- Target paragraph -->
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
                            The paragraph number after which the paywall is inserted.
                        </p>
                    </td>
                </tr>

                <!-- Minimum age -->
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
                            Number of days after which the post is considered "old" and triggers the paywall.
                        </p>
                    </td>
                </tr>

                <!-- Target language -->
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
                            Select on which language version of the site the paywall should appear.
                            Requires WPML to detect the current language.
                        </p>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}