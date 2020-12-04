<?php
/**
 * Plugin Name: WP_GFDP_custom_folders
 * Description: Modicifation for GFrorms Dropbox add-on. New users we create his folder, and all the pictures uploaded by this user would be stored in his folder. Ideal would to make so there would be sub folders for the forms we use to to the file upload.
 * Plugin URI: https://github.com/uptimizt/inscom-mvp
 * Version: 1.0
 * Author: Evgeniy Rezanov
 * Author URI: https://github.com/uptimizt/inscom-mvp
 * 
 */

defined('ABSPATH') || exit;
define( 'WP_GFDB_CUSTOM_FOLDERS_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_GFDB_CUSTOM_FOLDERS_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_GFDB_CUSTOM_FOLDERS_VERSION', '1.0' );


class GFDBCustomFolders {
    public static function init(){
        // Update the dropbox upload path on a specific form (uploads form)
        $uploads_form_id = '9';
        add_filter( 'gform_dropbox_folder_path_' . $uploads_form_id, array(__CLASS__, 'upload_to_client_dir'), 10, 5 );
        // Settings pages
        add_filter( 'gform_settings_menu', array(__CLASS__, 'add_custom_settings_tab') );
        add_filter( 'gform_form_settings_menu', array(__CLASS__, 'my_custom_form_settings_menu_item') );
        add_action( 'gform_form_settings_page_my_custom_form_settings_page', array(__CLASS__, 'my_custom_form_settings_page') );

    }

    // Organize the uploads in our uploads directory by client_name
    public static function upload_to_client_dir( $folder_path, $form, $field_id, $entry, $feed ) {
        // ID of the field used to collect the client name
        $uploads_field_id = '2';

        // Replace spaces with a hyphen and remove all non-alphanumerics
        $client_dir = str_replace( ' ', '-', rgar( $entry, $uploads_field_id ) );
        $client_dir = preg_replace( '/[^A-Za-z0-9-]/', '', $client_dir );

        // Create a folder for the client to add their uploads to
        return $folder_path . '/' . $client_dir;
    }

    // add Settings page
    public static function add_custom_settings_tab( $tabs ) {
        $tabs[] = array( 'name' => 'gfdb_custom_folders_settings', 'label' => 'My Settings' );
        return $tabs;
    }

    // add a custom menu item to the Form Settings page menu
    public static function my_custom_form_settings_menu_item( $menu_items ) {
        $menu_items[] = array(
            'name' => 'my_custom_form_settings_page',
            'label' => __( 'My Custom Form Settings Page' )
        );
    
        return $menu_items;
    }
 
    // handle displaying content for our custom menu when selected
    public static function my_custom_form_settings_page() {
 
        GFFormSettings::page_header();
    
        echo 'My Custom Form Settings!';
    
        GFFormSettings::page_footer();
 
    }
}
CFDBCustomFolders::init();
?>