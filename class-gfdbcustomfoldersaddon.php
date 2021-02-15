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
    protected $_short_title = 'Dropbox Custom folders Add-On';

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
        //add_filter('gform_submit_button', array($this, 'form_submit_button'), 10, 2);
        //add_action('gform_pre_submission', array($this, 'pre_submission'), 10, 2);
        add_action('gform_dropbox_folder_path', array($this, 'dropbox_folder_path'), 10, 5);
        //add_action('gform_after_submission', array($this, 'after_submission'), 10, 2);
    }


    // # SCRIPTS & STYLES -----------------------------------------------------------------------------------------------

    /**
     * Return the scripts which should be enqueued.
     *
     * @return array
     */
    public function scripts()
    {
        $scripts = array(
            array(
                'handle'  => 'gfdbcf_script_js',
                'src'     => $this->get_base_url() . '/js/gfdbcf_script.js',
                'version' => $this->_version,
                'deps'    => array('jquery'),
                'strings' => array(
                    'first'  => esc_html__('First Choice', 'simpleaddon'),
                    'second' => esc_html__('Second Choice', 'simpleaddon'),
                    'third'  => esc_html__('Third Choice', 'simpleaddon')
                ),
                'enqueue' => array(
                    array(
                        'admin_page' => array('form_settings'),
                        'tab'        => 'customfolders'
                    )
                )
            ),

        );

        return array_merge(parent::scripts(), $scripts);
    }

    /**
     * Return the stylesheets which should be enqueued.
     *
     * @return array
     */
    public function styles()
    {
        $styles = array(
            array(
                'handle'  => 'gfdbcf_styles_css',
                'src'     => $this->get_base_url() . '/css/gfdbcf_styles.css',
                'version' => $this->_version,
                'enqueue' => array(
                    array('field_types' => array('poll'))
                )
            )
        );

        return array_merge(parent::styles(), $styles);
    }


    // # FRONTEND FUNCTIONS --------------------------------------------------------------------------------------------

    /**
     * Add the text in the plugin settings to the bottom of the form if enabled for this form.
     *
     * @param string $button The string containing the input tag to be filtered.
     * @param array $form The form currently being displayed.
     *
     * @return string
     */
    function form_submit_button($button, $form)
    {
        $settings = $this->get_form_settings($form);
        if (isset($settings['enabled']) && true == $settings['enabled']) {
            
            $text   = $this->get_plugin_setting('mytextbox');
            $button = "<div>{$text}</div>" . $button;
        }

        return $button;
    }


    // # ADMIN FUNCTIONS -----------------------------------------------------------------------------------------------

    /**
     * Creates a custom page for this add-on.
     */
    public function plugin_page()
    {
        echo 'This page appears in the Forms menu';
        echo '<p>- Company name</p>';
        echo '<p>-- Project name {project start day} (users selects it)</p>';
        echo '<p>--- Form name where the project was submitted</p>';
        echo '<p>---- User (who uploaded) / date of upload.</p>';
        echo '<p>------ there the pictures has to land.</p>';
        echo '<ul><li><a href="https://docs.gravityforms.com/creating-a-custom-dropbox-app/#create-an-app" target="_blank">Create Dropbox App</a></li>
        </ul>';
    }

    /**
     * Configures the settings which should be rendered on the add-on settings tab.
     *
     * @return array
     */
    public function plugin_settings_fields()
    {
        return array(
            array(
                'title'  => esc_html__('Dropbox Custom Folders Add-On Settings', 'simpleaddon'),
                'fields' => array(
                    array(
                        'name'              => 'mytextbox',
                        'tooltip'           => esc_html__('This is the tooltip', 'simpleaddon'),
                        'label'             => esc_html__('This is the label', 'simpleaddon'),
                        'type'              => 'text',
                        'class'             => 'large',
                        'feedback_callback' => array($this, 'is_valid_setting'),
                    )
                )
            )
        );
    }

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
                    /*array(
                        'label'   => esc_html__('File upload field id', 'simpleaddon'),
                        'type'    => 'field_select',
                        'name'    => 'gfdb_file',
                        'tooltip' => esc_html__('Select the field to upload to Dropbox', 'simpleaddon'),
                    ),*/
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

    /**
     * Define the markup for the my_custom_field_type type field.
     *
     * @param array $field The field properties.
     * @param bool|true $echo Should the setting markup be echoed.
     */
    public function settings_my_custom_field_type($field, $echo = true)
    {
        echo '<div>' . esc_html__('My custom field contains a few settings:', 'simpleaddon') . '</div>';

        // get the text field settings from the main field and then render the text field
        $text_field = $field['args']['text'];
        $this->settings_text($text_field);

        // get the checkbox field settings from the main field and then render the checkbox field
        $checkbox_field = $field['args']['checkbox'];
        $this->settings_checkbox($checkbox_field);
    }


    // # SIMPLE CONDITION EXAMPLE --------------------------------------------------------------------------------------

    /**
     * Define the markup for the custom_logic_type type field.
     *
     * @param array $field The field properties.
     * @param bool|true $echo Should the setting markup be echoed.
     */
    public function settings_custom_logic_type($field, $echo = true)
    {

        // Get the setting name.
        $name = $field['name'];

        // Define the properties for the checkbox to be used to enable/disable access to the simple condition settings.
        $checkbox_field = array(
            'name'    => $name,
            'type'    => 'checkbox',
            'choices' => array(
                array(
                    'label' => esc_html__('Enabled', 'simpleaddon'),
                    'name'  => $name . '_enabled',
                ),
            ),
            'onclick' => "if(this.checked){jQuery('#{$name}_condition_container').show();} else{jQuery('#{$name}_condition_container').hide();}",
        );

        // Determine if the checkbox is checked, if not the simple condition settings should be hidden.
        $is_enabled      = $this->get_setting($name . '_enabled') == '1';
        $container_style = !$is_enabled ? "style='display:none;'" : '';

        // Put together the field markup.
        $str = sprintf(
            "%s<div id='%s_condition_container' %s>%s</div>",
            $this->settings_checkbox($checkbox_field, false),
            $name,
            $container_style,
            $this->simple_condition($name)
        );

        echo $str;
    }

    /**
     * Build an array of choices containing fields which are compatible with conditional logic.
     *
     * @return array
     */
    public function get_conditional_logic_fields()
    {
        $form   = $this->get_current_form();
        $fields = array();
        foreach ($form['fields'] as $field) {
            if ($field->is_conditional_logic_supported()) {
                $inputs = $field->get_entry_inputs();

                if ($inputs) {
                    $choices = array();

                    foreach ($inputs as $input) {
                        if (rgar($input, 'isHidden')) {
                            continue;
                        }
                        $choices[] = array(
                            'value' => $input['id'],
                            'label' => GFCommon::get_label($field, $input['id'], true)
                        );
                    }

                    if (!empty($choices)) {
                        $fields[] = array('choices' => $choices, 'label' => GFCommon::get_label($field));
                    }
                } else {
                    $fields[] = array('value' => $field->id, 'label' => GFCommon::get_label($field));
                }
            }
        }

        return $fields;
    }

    /**
     * Evaluate the conditional logic.
     *
     * @param array $form The form currently being processed.
     * @param array $entry The entry currently being processed.
     *
     * @return bool
     */
    public function is_custom_logic_met($form, $entry)
    {
        if ($this->is_gravityforms_supported('2.0.7.4')) {
            // Use the helper added in Gravity Forms 2.0.7.4.

            return $this->is_simple_condition_met('custom_logic', $form, $entry);
        }

        // Older version of Gravity Forms, use our own method of validating the simple condition.
        $settings = $this->get_form_settings($form);

        $name       = 'custom_logic';
        $is_enabled = rgar($settings, $name . '_enabled');

        if (!$is_enabled) {
            // The setting is not enabled so we handle it as if the rules are met.

            return true;
        }

        // Build the logic array to be used by Gravity Forms when evaluating the rules.
        $logic = array(
            'logicType' => 'all',
            'rules'     => array(
                array(
                    'fieldId'  => rgar($settings, $name . '_field_id'),
                    'operator' => rgar($settings, $name . '_operator'),
                    'value'    => rgar($settings, $name . '_value'),
                ),
            )
        );

        return GFCommon::evaluate_conditional_logic($logic, $form, $entry);
    }

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
            return $folder_path . '/' . $company_name . '/' . $project_name . '/' . $project_date . '/' . $form_name . '/' . $user . $today;
        }
    }

    /**
     * Performing a custom action at the end of the form submission process.
     *
     * @param array $entry The entry currently being processed.
     * @param array $form The form currently being processed.
     */
    public function after_submission($entry, $form)
    {
        // Evaluate the rules configured for the custom_logic setting.
        $result = $this->is_custom_logic_met($form, $entry);

        if ($result) {
            // Do something awesome because the rules were met.
        }
    }


    // # HELPERS -------------------------------------------------------------------------------------------------------

    /**
     * The feedback callback for the 'mytextbox' setting on the plugin settings page and the 'mytext' setting on the form settings page.
     *
     * @param string $value The setting value.
     *
     * @return bool
     */
    public function is_valid_setting($value)
    {
        return strlen($value) < 300;
    }
}
