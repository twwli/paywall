<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ajoute la page de réglages dans l’interface d’administration.
 */
function floatmagazin_paywall_add_admin_menu() {
    add_options_page(
        'Réglages Paywall',                // Titre de la page
        'FloatMagazin Paywall',            // Label du menu
        'manage_options',                  // Capacité requise
        'floatmagazin-paywall-settings',   // Slug
        'floatmagazin_paywall_options_page'// Fonction d’affichage
    );
}
add_action('admin_menu', 'floatmagazin_paywall_add_admin_menu');

/**
 * Enregistre les réglages de l’extension.
 */
function floatmagazin_paywall_register_settings() {
    register_setting(
        'floatmagazin_paywall_options_group', // Identifiant du groupe d’options
        'floatmagazin_paywall_options'        // Nom de l’option en base
    );

    // On peut aussi déclarer des sections et des champs pour mieux structurer.
    // Ici, on ira directement dans une page unique.
}
add_action('admin_init', 'floatmagazin_paywall_register_settings');

/**
 * Affiche la page de réglages.
 */
function floatmagazin_paywall_options_page() {
    // Vérifie la capacité
    if (!current_user_can('manage_options')) {
        return;
    }

    // Récupère les options existantes pour pré-remplir le formulaire
    $options = get_option('floatmagazin_paywall_options', array(
        'texte_paywall'    => '',
        'paragraphe_cible' => 2,
        'age_minimum'      => 14,
    ));

    // Si le formulaire est soumis, WordPress gère la sauvegarde via register_setting(), 
    // mais vous pouvez personnaliser le comportement en traitant $_POST si nécessaire.

    ?>
    <div class="wrap">
        <h1>Réglages du Paywall FloatMagazin</h1>
        <form method="post" action="options.php">
            <?php
                // Sécurise le formulaire grâce aux mécanismes WordPress
                settings_fields('floatmagazin_paywall_options_group');
                do_settings_sections('floatmagazin_paywall_options_group');
            ?>

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="texte_paywall">Texte du paywall</label></th>
                    <td>
                        <textarea
                            id="texte_paywall"
                            name="floatmagazin_paywall_options[texte_paywall]"
                            rows="4"
                            cols="50"
                        ><?php echo esc_textarea($options['texte_paywall']); ?></textarea>
                        <p class="description">
                            Saisissez le texte qui s’affiche dans le bloc paywall (ex : « ... und von Dir. Unterstütze uns: Werde jetzt... »).
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="paragraphe_cible">Paragraphe cible</label></th>
                    <td>
                        <input
                            type="number"
                            id="paragraphe_cible"
                            name="floatmagazin_paywall_options[paragraphe_cible]"
                            value="<?php echo esc_attr($options['paragraphe_cible']); ?>"
                            min="1"
                        />
                        <p class="description">Numéro du paragraphe après lequel insérer le paywall (par défaut : 2).</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="age_minimum">Âge minimum (en jours)</label></th>
                    <td>
                        <input
                            type="number"
                            id="age_minimum"
                            name="floatmagazin_paywall_options[age_minimum]"
                            value="<?php echo esc_attr($options['age_minimum']); ?>"
                            min="0"
                        />
                        <p class="description">
                            Nombre de jours à partir duquel l’article est considéré « ancien » et
                            le paywall sera injecté (par défaut : 14).
                        </p>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}