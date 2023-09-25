<?php
/*
Plugin Name: Custom Database Post type
Description: Custom database functionality.
Version: 1.0
Author: Rowell Mark M. Blanca
*/

// Activation hook to create the custom table
register_activation_hook(__FILE__, 'create_custom_table');

// Function to create the custom database table
function create_custom_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_data';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        post_title text NOT NULL,
        post_content longtext NOT NULL,
        post_author bigint(20) NOT NULL,
        post_date datetime NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Function to insert data into the custom table
function insert_custom_data($post_title, $post_content, $post_author) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_data';

    $wpdb->insert(
        $table_name,
        array(
            'post_title' => $post_title,
            'post_content' => $post_content,
            'post_author' => $post_author,
            'post_date' => current_time('mysql'),
        )
    );
}

// Function to retrieve data from the custom table
function get_custom_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_data';

    $query = "SELECT * FROM $table_name";
    return $wpdb->get_results($query, ARRAY_A);
}

// Hook to add a menu item in the WordPress admin menu
add_action('admin_menu', 'custom_data_menu');

// Function to create a custom admin menu page
function custom_data_menu() {
    add_menu_page(
        'Custom Data',
        'Custom Data',
        'manage_options',
        'custom-data',
        'custom_data_page'
    );
}

// Function to display the custom data page in the admin menu
function custom_data_page() {
    echo '<div class="wrap">';
    echo '<h2>Custom Data</h2>';

    if (isset($_POST['submit'])) {
        $post_title = sanitize_text_field($_POST['post_title']);
        $post_content = sanitize_text_field($_POST['post_content']);
        $post_author = get_current_user_id();

        insert_custom_data($post_title, $post_content, $post_author);
        echo '<div class="updated"><p>Data inserted successfully!</p></div>';
    }

    echo '<form method="post" action="">';
    echo '<label for="post_title">Title:</label><br>';
    echo '<input type="text" id="post_title" name="post_title" required><br><br>';
    echo '<label for="post_content">Content:</label><br>';
    echo '<textarea id="post_content" name="post_content" required></textarea><br><br>';
    echo '<input type="submit" name="submit" value="Insert Data">';
    echo '</form>';

    echo '<h2>Custom Data List</h2>';
    $data = get_custom_data();
    if (!empty($data)) {
        echo '<ul>';
        foreach ($data as $item) {
            echo '<li>';
            echo 'Title: ' . esc_html($item['post_title']) . '<br>';
            echo 'Content: ' . esc_html($item['post_content']);
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No custom data found.</p>';
    }
    echo '</div>';
}
