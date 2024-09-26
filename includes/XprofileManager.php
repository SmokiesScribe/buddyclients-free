<?php
namespace BuddyClients\Includes;

/**
 * Manages Xprofile fields.
 *
 * Creates and retrieves plugin-managed Xprofile fields.
 * 
 * @since 0.1.0
 * 
 * @see XprofileField
 */
class XprofileManager {
    
    /**
     * Field key.
     * 
     * The non-ID lookup key for core fields.
     * 
     * @var string
     */
    public $field_key;
	
    /**
     * Constructor method.
     *
     * @since 0.1.0
     */
    public function __construct() {
        // Define hooks
        $this->define_hooks();
    }
    
    /**
     * Defines hooks.
     *
     * @since 0.1.0
     */
    private function define_hooks() {
        add_action('save_post_bp-member-type', [$this, 'create_core']); // Profile types changed
        add_action('xprofile_fields_deleted_field', [$this, 'create_core']); // Profile field deleted
        add_action('save_post_bc_role', [$this, 'create_core']); // Role post saved
        add_action('bc_activated', [$this, 'create_core']);
        
        //add_action('init', [$this, 'create_core']);
    }
    
    /**
     * Creates core fields.
     * 
     * @since 0.1.0
     */
    public function create_core() {
        // Trigger roles field creation
        bc_roles_field_id();
    }
	
    /**
     * Retrieves all xprofile fields.
     * 
     * @since 0.1.0
     */
    public static function all_xprofile() {
        // Initialize
        $fields_array = [];
        
        // Get all xprofile data
        $xprofile_groups = bp_profile_get_field_groups();
        
        // Loop through groups
        foreach ($xprofile_groups as $xprofile_group) {
            $xprofile_group_name = $xprofile_group->name;
            $xprofile_group_id = $xprofile_group->id;
            $fields = $xprofile_group->fields ?? null;
            
            if ($fields) {
                foreach ($fields as $field) {
                    
                    // Add to array
                    $fields_array[$field->id] = [
                        'name' => $field->name,
                        'type' => $field->type,
                        'member_types' => $field->get_member_types(),
                    ];
                }
            }
        }
        return $fields_array;
    }
}
