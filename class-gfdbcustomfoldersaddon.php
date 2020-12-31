<?php

GFForms::include_addon_framework();

class GFDBCustomFoldersAddOn extends GFAddOn
{

    protected $_version = GFDB_CUSTOM_FOLDERS_ADDON_VERSION;
    protected $_min_gravityforms_version = '1.9';
    protected $_slug = 'customfolders';
    protected $_path = 'wp-gfdp-custom-folders/wp-gfdb-custom-folders.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Gravity Forms Dropbox Custom folders Add-On';
    protected $_short_title = 'Dropbox Custom folders';

    private static $_instance = null;

    /**
     * Get an instance of this class.
     *
     * @return GFDBCustomFoldersAddOn
     */
    public static function get_instance()
    {
        if (self::$_instance == null) {
            self::$_instance = new GFDBCustomFoldersAddOn();
        }

        return self::$_instance;
    }

    /**
     * Handles hooks and loading of language files.
     */
    public function init()
    {
        parent::init();
        add_action('gform_dropbox_folder_path', array($this, 'dropbox_folder_path'), 10, 5);
    }

    // # ADMIN FUNCTIONS -----------------------------------------------------------------------------------------------

    /**
     * Configures the settings which should be rendered on the Form Settings > Simple Add-On tab.
     *
     * @return array
     */
    public function form_settings_fields($form)
    {
        return array(
            array(
                'title'  => esc_html__('GF Dropbox Custom folders Form Settings', 'simpleaddon'),
                'fields' => array(
                    array(
                        'label'   => esc_html__('Use custom folders structure?', 'simpleaddon'),
                        'type'    => 'checkbox',
                        'name'    => 'enabled',
                        'tooltip' => esc_html__('Enable this for use custom folders stucture in Dropbox', 'simpleaddon'),
                        'choices' => array(
                            array(
                                'label' => esc_html__('Enabled', 'simpleaddon'),
                                'name'  => 'enabled',
                            ),
                        ),
                    ),
                    array(
                        'label'   => esc_html__('File upload field id', 'simpleaddon'),
                        'type'    => 'field_select',
                        'name'    => 'gfdb_file',
                        'tooltip' => esc_html__('Select the field to upload to Dropbox', 'simpleaddon'),
                    ),
                    array(
                        'label'   => esc_html__('Company name field id', 'simpleaddon'),
                        'type'    => 'field_select',
                        'name'    => 'gfdb_company',
                        'tooltip' => esc_html__('Select the field with the name of the root directory (company name)', 'simpleaddon'),
                    ),
                    array(
                        'label'   => esc_html__('Project name field id', 'simpleaddon'),
                        'type'    => 'field_select',
                        'name'    => 'gfdb_project',
                        'tooltip' => esc_html__('Select the field with the name of the sub-directory (project name)', 'simpleaddon'),
                    ),
                    array(
                        'label'   => esc_html__('Project start day field id', 'simpleaddon'),
                        'type'    => 'field_select',
                        'name'    => 'gfdb_project_date',
                        'tooltip' => esc_html__('Select the field with the name of the sub-directory (project start date)', 'simpleaddon'),
                    ),
                    array(
                        'label'   => esc_html__('Worker name field id', 'simpleaddon'),
                        'type'    => 'field_select',
                        'name'    => 'gfdb_project_user',
                        'tooltip' => esc_html__('Select the field with the name of the sub-directory (worker name)', 'simpleaddon'),
                    ),
                ),
            ),
        );
    }


    // # CHANGE DROPBOX FOLDER PATH --------------------------------------------------------------------------------------

    public function dropbox_folder_path($folder_path, $form, $field_id, $entry, $feed)
    {
        $settings = $this->get_form_settings($form);
        if (isset($settings['enabled']) && true == $settings['enabled']) {
            $company_name = str_replace(' ', '-', $entry[$settings['gfdb_company']]);
            $company_name = preg_replace('/[^A-Za-z0-9-]/', '', $company_name);
            
            $project_name = str_replace(' ', '-', $entry[$settings['gfdb_project']]);
            $project_name = preg_replace('/[^A-Za-z0-9-]/', '', $project_name);
            
            $project_date = str_replace(' ', '-', $entry[$settings['gfdb_project_date']]);
            $project_date = preg_replace('/[^A-Za-z0-9-]/', '', $project_date);

            $user = str_replace(' ', '-', $entry[$settings['gfdb_project_user']]);
            $user = preg_replace('/[^A-Za-z0-9-]/', '', $user);

            $today = date("Y-m-d");
            $form_name = $form['title'];

            // Create a folder for the client to add their uploads to
            return $folder_path . '/' . $company_name . '/' . $project_name . $project_date . '/' . $form_name . '/' . $user . $today;
        }
    }

}
