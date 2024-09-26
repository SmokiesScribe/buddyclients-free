<?php
namespace BuddyClients\Admin;

/**
 * Settings page.
 *
 * Creates single settings page.
 */
class SettingsPage {
    
    /**
     * Setting data.
     * 
     * @var array Associative array of settings data.
     */
     private $data;
     
    /**
     * Key used to build slug.
     * 
     * @var string
     */
     private $key;
     
    /**
     * Name of settings group.
     * 
     * @var string
     */
     private $name;
    
    /**
     * Slug.
     * 
     * @var string.
     */
     private $slug;
     
    /**
     * Parent menu slug.
     * 
     * @var string.
     */
     private $parent_menu;
     
    /**
     * Page title.
     * 
     * @var string.
     */
     private $title;
     
    /**
     * Menu order.
     * 
     * @var int|null
     */
     private $menu_order;
     
    /**
     * Capability.
     * 
     * @var string Optional. Default 'manage_options'.
     */
     private $cap;

    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct( $args ) {
        
        // Get settings callback
        $callback = Settings::get_callback( $args['key'] );
        if ( is_callable( $callback ) ) {
            $this->data = call_user_func( $callback );
        }
        
        // Extract data
        $this->key = $args['key'];
        $this->title = $args['title'];
        $this->name = $this->build_settings_name();
        
        // Define hooks
        $this->define_hooks();
    }
    
    /**
     * Builds settings name.
     * 
     * @since 0.1.0
     */
    private function build_settings_name() {
        $key = str_replace('-', '_', $this->key );
        return 'bc_' . $key . '_settings';
    }
    
    /**
     * Registers hooks.
     * 
     * @since 0.1.0
     */
    private function define_hooks() {
        add_action('admin_init', [$this, 'register_settings']);
    }
    
    /**
     * Registers settings.
     * 
     * @since 0.1.0
     */
    public function register_settings() {
        register_setting( $this->name . '_group', $this->name );
        add_settings_section( $this->name . '_section', '', [$this, 'section_callback'], $this->name );
        
    }
    
    /**
     * Renders the settings page.
     * 
     * @since 0.1.0
     */
    public function render_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( $this->title ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( $this->name . '_group' ); ?>
                <?php do_settings_sections( $this->name ); ?>
                <?php submit_button( __('Save Settings', 'buddyclients') ); ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Renders the settings content.
     * 
     * @since 0.1.0
     */
     public function section_callback() {
        /**
         * Fires at the top of every BuddyClients settings page.
         *
         * @since 0.1.0
         *
         * @param string $settings_key  The key of the settings group.
         */
        do_action('bc_before_settings', $this->key);
        
        // Make sure we have an array of settings data
        if (is_array($this->data)) {
            // Loop through settings data
            foreach ($this->data as $section_key => $section_data) {
                // Output section header
                echo $this->section_group($section_key, $section_data);
            }
        // No settings data available
        } else {
            echo __('Not available.', 'buddyclients');
        }
    }
    
    /**
     * Displays section group.
     * 
     * @since 0.1.0
     */
     public function section_group(string $section_key, $section_data) {
        ?>
        <div class="buddyclients-settings-section">
            <div class="buddyclients-settings-section-title-wrap">
                <h2 class="buddyclients-settings-section-title"><?php echo esc_html($section_data['title']); ?></h2>
                <p class="description"><?php echo $section_data['description']; ?></p>
                <hr class="buddyclients-settings-section-title-divider">
            </div>
            
            <?php $this->section_group_field($section_key, $section_data); ?>
            
        </div>
        <?php
    }

    /**
     * Displays individual field.
     * 
     * @since 0.1.0
     */
    public function section_group_field($section_key, $section_data) {
        
        foreach ($section_data['fields'] as $field_id => $field_data) {
            // Output individual fields
            $type = $field_data['type'];
            $settings_key = $this->key;
            
            $value = bc_get_setting( $settings_key, $field_id );
            
            switch ($type) {
                case 'display':
                    echo $this->display($type, $field_id, $field_data, $value);
                    break;
                case 'checkboxes':
                    echo $this->checkbox_field($type, $field_id, $field_data, $value);
                    break;
                case 'checkbox_table':
                    echo $this->checkbox_table($type, $field_id, $field_data, $value);
                    break;
                case 'dropdown':
                    echo $this->select_field($type, $field_id, $field_data, $value);
                    break;
                case 'text':
                case 'number':
                case 'date':
                case 'email':
                    echo $this->input_field($type, $field_id, $field_data, $value);
                    break;
                case 'stripe_input':
                    echo $this->stripe_input_field($type, $field_id, $field_data, $value);
                    break;
                case 'stripe_dropdown':
                    echo $this->stripe_select_field($type, $field_id, $field_data, $value);
                    break;
                case 'hidden':
                    echo $this->hidden_field($type, $field_id, $field_data, $value);
                    break;
                case 'color':
                    echo $this->color_field($field_id, $field_data, $value);
                    break;
                case 'page':
                    echo $this->select_field($type, $field_id, $field_data, $value);
                    break;
                case 'legal':
                    echo $this->legal_field($type, $field_id, $field_data, $value);
                    break;
                case 'copy':
                    echo $this->copy_field($type, $field_id, $field_data);
                    break;
                default:
                    echo $this->input_field('text', $field_id, $field_data, $value);
                    break;
            }
        }        
    }
    
    /**
     * Retrieves setting value.
     * 
     * @since 0.1.0
     */
    public function get_setting_value($settings_key, $field_key) {
        $data = $this->data;
        foreach ($data as $section_key => $section_data) {
            foreach ($section_data['fields'] as $field_id => $field_data) {
                if ($field_id === $field_key) {
                    $curr_settings = get_option('bc_' . $settings_key . '_settings');
                    $field_value = $curr_settings[$field_id] ?? $field_data['default'] ?? '';
                    return $field_value;
                }
            }
        }
    }
    
    /**
     * Displays content directly.
     *
     * @since 0.1.0
     */
    public function display($type, $field_id, $field_data, $value) {
        ?>
        <div class="buddyclients-admin-field">
            <label for="<?php echo esc_attr($this->name . '[' . $field_id . ']'); ?>">
                <?php echo esc_html($field_data['label']); ?>
            </label>
            <div class="buddyclients-admin-field-input-wrap">
                <?php echo wp_kses_post($field_data['content']); ?>
                <p class="description"><?php echo $field_data['description']; ?></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Renders a checkbox field.
     *
     * @since 0.1.0
     */
    public function checkbox_field($type, $field_id, $field_data, $value) {
        ?>
        <div class="buddyclients-admin-field">
            <label for="<?php echo esc_attr($this->name . '[' . $field_id . ']'); ?>">
                <?php echo esc_html($field_data['label']); ?>
            </label>
            <div class="buddyclients-admin-field-input-wrap">
                <?php foreach ($field_data['options'] as $option_key => $option_label) : 
                    $checked = is_array($value) && in_array($option_key, $value) ? 'checked' : ''; ?>
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr($this->name . '[' . $field_id . '][]'); ?>" 
                               value="<?php echo esc_attr($option_key); ?>" <?php echo $checked; ?>>
                        <?php echo $option_label; ?>
                    </label><br>
                <?php endforeach; ?>
                <p class="description"><?php echo $field_data['description']; ?></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Renders a checkbox field as a table.
     *
     * @since 0.1.0
     */
    public function checkbox_table($type, $field_id, $field_data, $value) {
        ?>
        <div class="buddyclients-admin-field">
            <table class="bc-checkbox-table">
                <tbody>
                    <?php foreach ($field_data['options'] as $option_key => $option_label) : 
                        $required = in_array($option_key, $field_data['required_options']);
                        $checked = is_array($value) && in_array($option_key, $value) || $required ? 'checked' : ''; ?>
                        <tr class="<?php echo $checked ? 'checked' : ''; ?> <?php echo $required ? 'required' : ''; ?>">
                            <td>
                                <label>
                                    <input type="checkbox" name="<?php echo esc_attr($this->name . '[' . $field_id . '][]'); ?>" 
                                           value="<?php echo esc_attr($option_key); ?>" <?php echo $checked; ?>>
                                    <?php echo esc_html($option_label); ?>
                                </label>
                            </td>
                            <td>
                                <p class="description">
                                    <?php echo $required ? 'Required. ' : ''; ?>
                                    <?php echo isset($field_data['descriptions'][$option_key]) ? esc_html($field_data['descriptions'][$option_key]) : ''; ?>
                                </p>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
    
    /**
     * Renders a dropdown field.
     *
     * @since 0.1.0
     */
    public function select_field($type, $field_id, $field_data, $value) {
        ?>
        <div class="buddyclients-admin-field">
            <label for="<?php echo esc_attr($this->name . '[' . $field_id . ']'); ?>">
                <?php echo esc_html($field_data['label']); ?>
            </label>
            <div class="buddyclients-admin-field-input-wrap">
                <select name="<?php echo esc_attr($this->name . '[' . $field_id . ']'); ?>">
                    <?php foreach ($field_data['options'] as $option_key => $option_label) : 
                        $selected = ($value == $option_key) ? ' selected' : ''; ?>
                        <option value="<?php echo esc_attr($option_key); ?>" <?php echo $selected; ?>>
                            <?php echo esc_html($option_label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($type === 'page') {
                    self::page_button($field_id, $field_data, $value);
                } ?>
                <p class="description"><?php echo $field_data['description']; ?></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Renders an input field.
     *
     * @since 0.1.0
     *
     * @param string $type Accepts 'text', 'date', 'number'.
     */
    public function input_field($type, $field_id, $field_data, $value) {
        ?>
        <div class="buddyclients-admin-field">
            <label for="<?php echo esc_attr($this->name . '[' . $field_id . ']'); ?>">
                <?php echo esc_html($field_data['label']); ?>
            </label>
            <div class="buddyclients-admin-field-input-wrap">
                <input type="<?php echo esc_attr($type); ?>" 
                       name="<?php echo esc_attr($this->name . '[' . $field_id . ']'); ?>" 
                       value="<?php echo esc_attr($value); ?>" />
                <p class="description"><?php echo $field_data['description']; ?></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Renders a Stripe key input field.
     *
     * @since 0.1.0
     */
    public function stripe_input_field($type, $field_id, $field_data, $value) {
        $icon = isset($_GET['validate']) && $_GET['validate'] === 'stripe' 
            ? bc_stripe_valid_icon($field_data['stripe_key'] ?? null, $value) 
            : '';
        ?>
        <div class="buddyclients-admin-field">
            <label for="<?php echo esc_attr($this->name . '[' . $field_id . ']'); ?>">
                <?php echo esc_html($field_data['label']); ?>
            </label>
            <div class="buddyclients-admin-field-input-wrap">
                <input type="<?php echo esc_attr($type); ?>" 
                       name="<?php echo esc_attr($this->name . '[' . $field_id . ']'); ?>" 
                       value="<?php echo esc_attr($value); ?>" />
                <?php echo $icon; ?>
                <p class="description"><?php echo $field_data['description']; ?></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Renders a Stripe dropdown field.
     *
     * @since 0.1.0
     */
    public function stripe_select_field($type, $field_id, $field_data, $value) {
        $icon = isset($_GET['validate']) && $_GET['validate'] === 'stripe' 
            ? bc_stripe_mode_valid_icon() 
            : '';
        ?>
        <div class="buddyclients-admin-field">
            <label for="<?php echo esc_attr($this->name . '[' . $field_id . ']'); ?>">
                <?php echo esc_html($field_data['label']); ?>
            </label>
            <div class="buddyclients-admin-field-input-wrap">
                <select name="<?php echo esc_attr($this->name . '[' . $field_id . ']'); ?>">
                    <?php foreach ($field_data['options'] as $option_key => $option_label) : 
                        $selected = ($value == $option_key) ? ' selected' : ''; ?>
                        <option value="<?php echo esc_attr($option_key); ?>" <?php echo $selected; ?>>
                            <?php echo esc_html($option_label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php echo $icon; ?>
                <?php if ($type === 'page') {
                    self::page_button($field_id, $field_data, $value);
                } ?>
                <p class="description"><?php echo $field_data['description']; ?></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Renders a hidden field.
     *
     * @since 0.1.0
     */
    public function hidden_field($type, $field_id, $field_data, $value) {
        ?>
        <input type="hidden" name="<?php echo esc_attr($this->name . '[' . $field_id . ']'); ?>" value="<?php echo esc_attr($value); ?>" />
        <?php
    }
    
    /**
     * Renders a color input field.
     *
     * @since 0.1.0
     */
    public function color_field($field_id, $field_data, $value) {
        ?>
        <div class="buddyclients-admin-field">
            <label for="<?php echo esc_attr($this->name . '[' . $field_id . ']'); ?>">
                <?php echo esc_html($field_data['label']); ?>
            </label>
            <div class="buddyclients-admin-field-input-wrap">
                <input type="color" name="<?php echo esc_attr($this->name . '[' . $field_id . ']'); ?>" 
                       value="<?php echo esc_attr($value); ?>" class="color-field" />
                <p class="description"><?php echo $field_data['description']; ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * Renders a page dropdown field.
     * 
     * @since 0.1.0
     */
    public function page_button($field_id, $field_data, $value) {
        
        // Check if page is selected and published
        if ($value && get_post_status($value) === 'publish') {
            
            // Get page permalink
            $selected_page_permalink = ($value) ? get_permalink($value) : '#';
            
            // Create view page button
            $button = '<a href="' . esc_url($selected_page_permalink) . '" target="_blank"><button type="button" class="button button-secondary">' . __('View Page', 'buddyclients') . '</button></a>';
        } else {
            
            // Show create button
            $button = '<button onclick="createNewPage({
                page_key: \'' . esc_js($field_id) . '\',
                settings_key: \'' . esc_js('pages') . '\',
                post_title: \'' . esc_js($field_data['post_title']) . '\',
                post_content: \'' . esc_js($field_data['post_content']) . '\',
                post_type: \'' . esc_js('page') . '\',
                post_status: \'' . esc_js('publish') . '\'
            });" type="button" class="button button-secondary">' . __('Create Page', 'buddyclients') . '</button>';
        }

        // Create Page button...
        echo $button;
    }
    
    /**
     * Displays legal page field.
     * 
     * @since 0.1.0
     */
    public function legal_field($type, $field_id, $field_data, $value) {
        
        // Initialize
        $output = '';
        $view_button = '';
        $create_button = '';
        $edit_button = '';
    
        // Check if post exists
        $post = get_post($value);
        
        // If post exists, show view button
        if ( $post ) {
            $view_button = $post ? '<a href="' . get_permalink($value) . '" target="_blank"><button type="button" class="button button-primary" style="margin-right: 5px">' . sprintf(esc_html__('View Active %s', 'buddyclients'), $field_data['label']) . '</button></a>' : '';
        }
        
        // Continue editing button
        $draft_id = bc_get_setting('legal', $field_id . '_draft');
        if ( $draft_id ) {
            $edit_button = '<a href="' . get_edit_post_link($draft_id) . '"><button type="button" class="button button-secondary" style="margin-right: 5px">' . sprintf(esc_html__('Edit %s Draft', 'buddyclients'), $field_data['label']) . '</button></a>';
        } else {
            // Create button
            $create_button = '<button onclick="createNewPage({
                page_key: \'' . $field_id . '\',
                settings_key: \'' . 'legal' . '\',
                post_title: \'' . $field_data['label'] . '\',
                post_content: \'\',
                post_type: \'' . 'bc_legal' . '\',
                post_status: \'' . 'draft' . '\'
            });" type="button" class="button button-secondary" style="margin-right: 5px">' . sprintf(esc_html__('Create New %s', 'buddyclients'), $field_data['label']) . '</button>';
        }
        
        // Get previous version and deadline
        $version_trans_message = '';
        $prev_version = bc_get_setting('legal', $field_id . '_prev');
        if ( $prev_version ) {
            $curr_time = current_time('mysql');
            $publish_date = get_post_field('post_date', $value);
            $deadline_setting = bc_get_setting('legal', 'legal_deadline');
            if ($deadline_setting !== '') {
                $deadline = date('Y-m-d H:i:s', strtotime($publish_date . ' +' . $deadline_setting . ' days'));
                // Get the current date and time
                $current_datetime = date('Y-m-d H:i:s');
                
                // Compare the deadline with the current date and time
                if ($deadline > $current_datetime) {
                    $human_readable_deadline = date('F j, Y, g:i a', strtotime($deadline));
                    $version_trans_message = sprintf(esc_html__('Users have until %s to accept the new %s.', 'buddyclients'), $human_readable_deadline, $field_data['label']);
                }
    
            } else {
                $version_trans_message = sprintf(esc_html__('Users have forever to accept the new %s.', 'buddyclients'), $field_data['label']);
            }
        }
        
        // Build output
        $output .= '<div class="buddyclients-admin-field">';
        $output .= '<label for="' . esc_attr($this->name . '[' . $field_id . ']') . '">' . esc_html($field_data['label']) . '</label>';
        $output .= '<div class="buddyclients-admin-field-input-wrap" style="margin-bottom: 15px">';
        $output .= '<input type="hidden" name="' . esc_attr($this->name . '[' . $field_id . ']') . '" value="' . esc_attr($value) . '">';
    
        $output .= $view_button;
        $output .= $create_button;
        $output .= $edit_button;
        $output .= '<br>' . esc_html($version_trans_message);
        
        $output .= '</div>';
        $output .= '</div>';
        
        echo $output;
    }
    
    /**
     * Displays copy-to-clipboard text.
     * 
     * @since 0.1.0
     */
    public function copy_field($type, $field_id, $field_data) {
        ?>
        <div class="buddyclients-admin-field">
            <label for="<?php echo esc_attr($this->name . '[' . $field_id . ']'); ?>"><?php echo esc_html($field_data['label']); ?></label>
            <div class="buddyclients-admin-field-input-wrap">
                <?php echo bc_copy_to_clipboard($field_data['content'], $field_id); ?>
                <p class="description"><?php echo $field_data['description']; ?></p>
            </div>
        </div>
        <?php
    }
}