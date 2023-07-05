<?php

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class SWPFEController
{
    public function __construct()
    {
        register_activation_hook( SWPFE_PLUGIN_BASENAME, array( $this, 'swpfe_activation_hook' ));
        add_action('admin_menu', array( $this, 'swpfe_admin_menu' ));
        add_action('admin_menu', array( $this, 'swpfe_register_custom_admin_page' ));
        add_action('wpforms_process_complete', array($this, 'swpfe_save_wpf_entry'), 10, 4);
        add_action('admin_enqueue_scripts', array( $this, 'swpfe_enqueue_admin_scripts'));
        // add_filter('admin_title', array( $this, 'swpfe_custom_admin_title'), 10, 2);
    }

    public function swpfe_admin_menu() {
        add_menu_page(
            __('WPF Entries', 'wpf-entries'),
            __('WPF Entries', 'wpf-entries'),
            'manage_options',
            'manage-wpf-entries',
            'SWPFEDataController::index',
            'dashicons-database',
            6
        );
    }

    function swpfe_register_custom_admin_page() {
        add_submenu_page(
            'wpf-entries', // hidden submenu
            __('Form Entries', 'wpf-entries-list'),
            __('Form Entries', 'wpf-entries-list'),
            'manage_options',
            'wpf-entries-list',
            'SWPFEDataController::form_entry_details'
        );
    }

    public function swpfe_activation_hook() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'wpf_entries';
        $sql = "CREATE TABLE  $table_name (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, 
            `post_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        $table_name = $wpdb->prefix . 'wpf_entry_meta';
        $sql = "CREATE TABLE  $table_name (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, 
            `wpf_entry_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `meta_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `meta_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sql);
    }

    public static function swpfe_set_query_vars( $args )
    {
        global $wp_query;
        $wp_query->set("data", $args);

    }

    // Function to save the WP forms entry
    function swpfe_save_wpf_entry($form_fields, $entry, $form_data, $entry_id) {

        global $wpdb;

        $form_id = $form_data['id'];

        $table = $wpdb->prefix . 'wpf_entries';
        $entry_data = array( 'post_id' => $form_id, );
        
        $wpdb->insert($table, $entry_data);
        $entry_id = $wpdb->insert_id;

        if( $entry_id ) {
            foreach ($form_fields as $field) {
                $field = apply_filters( 'wpforms_process_entry_field', $field, $form_data, $entry_id );
                if ( isset( $field['value'] ) && '' !== $field['value'] ) {
                    $field_value = is_array( $field['value'] ) ? serialize( $field['value'] ) : $field['value'];
                    $table = $wpdb->prefix . 'wpf_entry_meta';
                    $entry_metadata = array(
                        'wpf_entry_id'   => $entry_id,
                        'meta_key'   => $field['name'],
                        'meta_value' => $field_value,
                    );
                    $wpdb->insert($table, $entry_metadata);
                }
            }
        }
        
    }

    function swpfe_enqueue_admin_scripts () {
        global $pagenow;
        if ($pagenow === 'admin.php' && $_GET['page'] === 'wpf-entries-list') {
            wp_enqueue_script('data_tables', 'https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js', array('jquery'), '1.10.25', true);
            wp_enqueue_style('data_tables_style', 'https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css');
        }
        wp_enqueue_style('plugin-style', SWPFE_PLUGIN_URL . '/lib/assets/style.css');
    }

    // function swpfe_custom_admin_title($admin_title, $title) {
    //     global $pagenow;
    //     if ($pagenow === 'admin.php' && $_GET['page'] === 'wpf-entries-list') {
    //         return 'Form Entries Listing ‹ ' . get_bloginfo('name');
    //     }
    //     return $admin_title;
    // }
}