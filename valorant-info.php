<?php
/*
Plugin Name: Valorant Info Plugin
Description: A WordPress plugin to display Valorant agent and weapon information from the Valorant-API.
Version: 1.2
Author: Hsu Myat Mon Win
*/

// Disable error message display on webpage
error_reporting(E_ERROR | E_PARSE);

// Enqueue scripts and styles
function valorant_api_enqueue_scripts() {
    wp_enqueue_style('valorant-api-style', plugin_dir_url(__FILE__) . 'assets/style.css');
}
add_action('wp_enqueue_scripts', 'valorant_api_enqueue_scripts');

// Add admin menu
function valorant_info_admin_menu() {
    add_menu_page(
        'Valorant Info Plugin Settings',
        'Valorant Info',
        'manage_options',
        'valorant-info-settings',
        'valorant_info_settings_page'
    );
}
add_action('admin_menu', 'valorant_info_admin_menu');


// Register settings
function valorant_info_register_settings() {
    register_setting('valorant-info-settings-group', 'valorant_show_agent_description');
    register_setting('valorant-info-settings-group', 'valorant_show_agent_abilities');
    register_setting('valorant-info-settings-group', 'valorant_show_weapon_category');
    register_setting('valorant-info-settings-group', 'valorant_show_weapon_magazine');
    register_setting('valorant-info-settings-group', 'valorant_show_weapon_fire_rate');
    register_setting('valorant-info-settings-group', 'valorant_show_weapon_reload');
    register_setting('valorant-info-settings-group', 'valorant_show_weapon_damage');
}
add_action('admin_init', 'valorant_info_register_settings');

// Settings page callback function
function valorant_info_settings_page() {
    ?>
    <div class="wrap">
        <h2>Valorant Info Plugin Settings</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('valorant-info-settings-group');
            do_settings_sections('valorant-info-settings-group');
            ?>
            <table class="form-table">
                <tr>
                    <td><h2>Agent Information Display Settings</h2></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Show Description</th>
                    <td><label for="valorant_show_agent_description"><input type="checkbox" id="valorant_show_agent_description" name="valorant_show_agent_description" value="1" <?php checked(get_option('valorant_show_agent_description'), 1); ?>> Show Agent Description</label></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Show Abilities</th>
                    <td><label for="valorant_show_agent_abilities"><input type="checkbox" id="valorant_show_agent_abilities" name="valorant_show_agent_abilities" value="1" <?php checked(get_option('valorant_show_agent_abilities'), 1); ?>> Show Agent Abilities</label></td>
                </tr>
                <tr>
                    <td><h2>Weapon Information Display Settings</h2></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Show Category</th>
                    <td><label for="valorant_show_weapon_category"><input type="checkbox" id="valorant_show_weapon_category" name="valorant_show_weapon_category" value="1" <?php checked(get_option('valorant_show_weapon_category'), 1); ?>> Show Weapon Category</label></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Show Magazine Size</th>
                    <td><label for="valorant_show_weapon_magazine"><input type="checkbox" id="valorant_show_weapon_magazine" name="valorant_show_weapon_magazine" value="1" <?php checked(get_option('valorant_show_weapon_magazine'), 1); ?>> Show Weapon Magazine</label></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Show Fire Rate</th>
                    <td><label for="valorant_show_weapon_fire_rate"><input type="checkbox" id="valorant_show_weapon_fire_rate" name="valorant_show_weapon_fire_rate" value="1" <?php checked(get_option('valorant_show_weapon_fire_rate'), 1); ?>> Show Weapon Fire Rate</label></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Show Reload Speed</th>
                    <td><label for="valorant_show_weapon_reload"><input type="checkbox" id="valorant_show_weapon_reload" name="valorant_show_weapon_reload" value="1" <?php checked(get_option('valorant_show_weapon_reload'), 1); ?>> Show Weapon Reload</label></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Show Damage</th>
                    <td><label for="valorant_show_weapon_damage"><input type="checkbox" id="valorant_show_weapon_damage" name="valorant_show_weapon_damage" value="1" <?php checked(get_option('valorant_show_weapon_damage'), 1); ?>> Show Weapon Damage</label></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Shortcode function to display agent data
function valorant_api_agent_data($atts) {
    // API endpoint URL
    $api_url = 'https://valorant-api.com/v1/agents';

    // Fetch data from the API
    $response = wp_remote_get($api_url);

    // Check if response is successful
    if (is_array($response) && !is_wp_error($response)) {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Check if data is retrieved successfully
        if ($data && isset($data['data'])) {
            $agents = $data['data'];

            // Output Agent data
            $output = '<div class="display-container">';
            foreach ($agents as $agent) {
                $output .= '<div class="agent">';
                $output .= '<img src=' . $agent['displayIcon'] .' class="icon">';
                $output .= '<div class="agent-details">';
                $output .= '<h2>' . $agent['displayName'] . '</h2>';
                if (get_option('valorant_show_agent_description') == 1) {
                    $output .= '<p>Description: ' . $agent['description'] . '</p>';
                }
                if (get_option('valorant_show_agent_abilities') == 1) {
                    $output .= '<p>Abilities: ' . $agent['abilities'][0]['displayName'] . ', ' . $agent['abilities'][1]['displayName'] . ', ' . $agent['abilities'][2]['displayName'] . ', ' . $agent['abilities'][3]['displayName'] . '</p>';
                }
                $output .= '</div>';
                $output .= '</div>';
            }
            $output .= '</div>'; // Close container

            return $output;
        } else {
            return '<p>Failed to retrieve agent data from the API.</p>';
        }
    } else {
        return '<p>Failed to connect to the API.</p>';
    }
}
add_shortcode( 'valorant_agent_data', 'valorant_api_agent_data' );

// Shortcode function to display weapon data
function valorant_api_weapon_data($atts) {
    // API endpoint URL
    $api_url = 'https://valorant-api.com/v1/weapons';

    // Fetch data from the API
    $response = wp_remote_get($api_url);

    // Check if response is successful
    if (is_array($response) && !is_wp_error($response)) {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Check if data is retrieved successfully
        if ($data && isset($data['data'])) {
            $weapons = $data['data'];
            $filler_string = 'EEquippableCategory::';

            // Output Weapon data
            $output = '<div class="display-container">';
            foreach ($weapons as $weapon) {
                $output .= '<div class="weapon">';
                $output .= '<img src=' . $weapon['displayIcon'] .' class="icon">';
                $output .= '<div class="weapon-details">';
                $output .= '<h2>' . $weapon['displayName'] . '</h2>';
                // Check if option to show weapon category is enabled
                if (get_option('valorant_show_weapon_category')) {
                    $output .= '<p>Category: ' . ltrim($weapon['category'], $filler_string) . '</p>';
                }
                // Check if option to show weapon magazine is enabled
                if (get_option('valorant_show_weapon_magazine')) {
                    $output .= '<p>Magazine Size: ' . $weapon['weaponStats']['magazineSize'] . '</p>';
                }
                // Check if option to show weapon fire rate is enabled
                if (get_option('valorant_show_weapon_fire_rate')) {
                    $output .= '<p>Fire Rate: ' . $weapon['weaponStats']['fireRate'] . '</p>';
                }
                // Check if option to show weapon reload is enabled
                if (get_option('valorant_show_weapon_reload')) {
                    $output .= '<p>Reload Speed: ' . $weapon['weaponStats']['reloadTimeSeconds'] . '</p>';
                }
                // Check if option to show weapon damage is enabled
                if (get_option('valorant_show_weapon_damage')) {
                    $output .= '<p>Damage (Head, Body, Leg): ' . $weapon['weaponStats']['damageRanges'][0]['headDamage'] . ', ' . $weapon['weaponStats']['damageRanges'][0]['bodyDamage'] . ', ' . $weapon['weaponStats']['damageRanges'][0]['legDamage'] . '</p>';
                }
                $output .= '</div>';
                $output .= '</div>';
            }
            $output .= '</div>'; // Close container

            return $output;
        } else {
            return '<p>Failed to retrieve weapon data from the API.</p>';
        }
    } else {
        return '<p>Failed to connect to the API.</p>';
    }
}
add_shortcode( 'valorant_weapon_data', 'valorant_api_weapon_data' );

?>
