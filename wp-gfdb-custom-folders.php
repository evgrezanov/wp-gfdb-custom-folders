<?php

/**
 * Plugin Name: WP_GFDP_custom_folders
 * Description: Add-on for GFrorms Dropbox plugin. New users has his folder, and all the pictures uploaded by this user would be stored in his folder.
 * Plugin URI: https://github.com/evgrezanov/wp_gfdb_custom_folders
 * Version: 2.1.1-min
 * Author: Evgeniy Rezanov
 * Author URI: https://github.com/evgrezanov/
 */
define('GFDB_CUSTOM_FOLDERS_ADDON_VERSION', '2.1.1');

add_action('gform_loaded', array('GFDB_Custom_Folders_AddOn_Bootstrap', 'load'), 5);
class GFDB_Custom_Folders_AddOn_Bootstrap{

    public static function load()
    {

        if (!method_exists('GFForms', 'include_addon_framework')) {
            return;
        }

        require_once('class-gfdbcustomfoldersaddon.php');

        GFAddOn::register('GFDBCustomFoldersAddOn');
    }
}

function gf_simple_addon()
{
    return GFDBCustomFoldersAddOn::get_instance();
}

?>