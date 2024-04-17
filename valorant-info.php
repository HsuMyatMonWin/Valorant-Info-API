<?php
/*
Plugin Name: Valorant Info Plugin
Description: A WordPress plugin to display Valorant agent and weapon data from the Valorant-API.
Version: 1.1
Author: Hsu Myat Mon Win
*/

// Disable error message display on webpage
error_reporting(E_ERROR | E_PARSE);

// Enqueue scripts and styles
function valorant_api_enqueue_scripts() {
    wp_enqueue_style('valorant-api-style', plugin_dir_url(__FILE__) . 'style.css');
}
add_action('wp_enqueue_scripts', 'valorant_api_enqueue_scripts');

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

            // Output GM data
            $output = '<div class="chess-gm-data">';
            foreach ($agents as $agent) {
                $output .= '<div class="player">';
                $output .= '<h2>' . $agent['displayName'] . '</h2>';
                $output .= '<img src=' . $agent['displayIcon'] .'>';
                $output .= '<p>Role: ' . $agent['role']['displayName'] . '</p>';
                $output .= '<p>Background: ' . $agent['description'] . '</p>';
                $output .= '</div>';
            }
            $output .= '</div>';

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

            // Output GM data
            $output = '<div class="chess-gm-data">';
            foreach ($weapons as $weapon) {

                $output .= '<div class="player">';
                $output .= '<h2>' . $weapon['displayName'] . '</h2>';
                $output .= '<img src=' . $weapon['displayIcon'] .'>';
                $output .= '<p>Category: ' . ltrim($weapon['category'], $filler_string) . '</p>';
                $output .= '<p>Fire Rate: ' . $weapon['weaponStats']['fireRate'] . '</p>';
                $output .= '<p>Magazine Size: ' . $weapon['weaponStats']['magazineSize'] . '</p>';
                $output .= '<p>Reload Speed: ' . $weapon['weaponStats']['reloadTimeSeconds'] . '</p>';
                $output .= '</div>';
            }
            $output .= '</div>';

            return $output;
        } else {
            return '<p>Failed to retrieve agent data from the API.</p>';
        }
    } else {
        return '<p>Failed to connect to the API.</p>';
    }
}
add_shortcode( 'valorant_weapon_data', 'valorant_api_weapon_data' );
?>