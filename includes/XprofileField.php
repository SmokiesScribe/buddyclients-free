<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Single Xprofile field.
 *
 * Retrieves data for an Xprofile field.
 * Updates the field settings.
 * 
 * @since 0.1.0
 * 
 * @see XprofileManager
 */
class XprofileField {
    
    /**
     * The Xprofile field key.
     *
     * @var string
     * @see fields()
     */
    public $field_key;
    
    /**
     * The args used to create the field.
     *
     * @var array
     */
    private $args;
    
    /**
     * The args used to create the field.
     *
     * @var array
     */
    private $field_args;
    
    /**
     * The ID of the first field group.
     *
     * @var int
     */
    private $first_field_group;
    
    /**
     * The Xprofile field ID.
     *
     * @var int
     */
    public $field_id;
    
    /**
     * The Xprofile field object.
     *
     * @var object
     */
    public $field;
    
    /**
     * The prefix for the lock transient.
     *
     * This constant is used to create a unique transient key for each Xprofile field
     * based on its field key. The transient is used to prevent simultaneous execution
     * of operations for the same field.
     */
    const LOCK_TRANSIENT_PREFIX = 'buddyclients_xprofile_lock_';
    
    /**
     * Constructor method.
     *
     * @since 0.1.0
     * @updated 0.1.3
     * 
     * @param   string  $field_key  The lookup key for the field.
     * @param array $args
     *     An array of args to create or update the Xprofile field.
     * 
     *     @type int    $field_id           The ID of the Xprofile field.
     *     @type int    $field_group_id     The ID of the Xprofile field group.
     *                                      Defaults to first field group.
     *     @type int    $field_order        The order of the Xprofile field.
     *                                      Defaults to last.
     *     @type string $field_type         The type of Xprofile field.
     *                                      Accepts 'checkbox', 'input', 'textbox', 'textarea', 'selectbox'.
     *                                      Defaults to 'textbox'.
     *     @type bool   $can_delete         Whether the Xprofile field is deletable.
     *                                      Defaults to true.
     *     @type string $field_name         The name of the Xprofile field.
     *     @type array  $field_options      An array of options for 'checkbox' and 'selectbox' fields.
     *     @type string $member_types       The key representing the field member types.
     *                                      Accepts 'team' and 'client'.
     */
    public function __construct( $field_key, $args = null ) {
        $this->field_key = $field_key;
        $this->args = $args;
        
        $this->initialize_field();
        
        // Hook into bp_init to ensure BuddyPress is fully initialized
        add_action( 'bp_init', [$this, 'initialize_field'] );
    }
        
    /**
     * Initializes the field after BuddyPress is fully loaded.
     * 
     * @since 0.1.3
     */
    public function initialize_field() {
        // Retrieve the first field group ID
        $this->first_field_group = $this->get_first_field_group_id();

        // Continue with other setup steps
        $this->setup_field();
    }

    /**
     * Sets up the Xprofile field based on provided arguments.
     * 
     * @since 0.1.3
     */
    private function setup_field() {
        // Get field ID
        $this->field_id = $this->get_field_id_by_key();

        // Check for lock
        if ( $this->is_locked() ) {
            return;
        }

        // Set lock
        $this->set_lock();
        
        // Fetch field if id exists
        $this->field = $this->field_id ? $this->get_field() : null;

        // Create field if necessary
        if ( ! $this->field ) {
            $this->create_field( $this->args );
        }

        // Exit if no field
        if ( ! $this->field ) {
            return;
        }

        // Update field options
        $this->update_options();

        // Update member types
        $this->update_member_types();
    }
    
    /**
     * Retrieves the ID of the first field group.
     * 
     * @since 0.1.0
     */
    private function get_first_field_group_id() {
        $field_groups = bp_profile_get_field_groups();

        // Check if field groups are available
        if (!empty($field_groups) && is_array($field_groups)) {
            // Return the ID of the first field group
            return $field_groups[0]->id;
        }

        // Handle the case where no field groups are found
        return null;
    }
    
    /**
     * Checks for existing field.
     * 
     * Creates a new field if necessary.
     * 
     * @since 0.1.0
     */
    private function get_field() {
        
        // Make sure field id exists
        if ( $this->field_id ) {
            
            // Attempt to fetch field object
            $existing_field = xprofile_get_field( $this->field_id );
            
            // Make sure the field exists
            if ( $existing_field && $existing_field->id !== null ) {
                return $existing_field;
            } else {
                return null;
            }
        }
    }
    
    /**
     * Retrieves field ID from key.
     * 
     * @since 0.1.0
     */
    public function get_field_id_by_key() {
        return get_option( 'buddyc_xprofile_field_' . $this->field_key );
    }
    
    /**
     * Creates an xprofile field.
     * 
     * @since 0.1.0
     * 
     * @param   array   $args
     */
    public function create_field( $args ) {
        
        // Exit if field exists
        if ( $this->field ) {
            return;
        }
        
        // Define field args
        $field_args = [
            'field_group_id'    => $args['field_group_id'] ?? $this->first_field_group,
            'type'              => $args['field_type'] ?? 'selectbox',
            'name'              => $args['field_name'] ?? '',
            'can_delete'        => $args['can_delete'] ?? true,
            'field_order'       => $args['field_order'] ?? 100,
        ];
        
        // Insert or update xprofile field
        $this->field_id = xprofile_insert_field( $field_args );
        
        if ( $this->field_id && $this->field_id !== '' ) {
        
            // Get field oject
            $this->field = xprofile_get_field( $this->field_id );
            
            // Update setting
            update_option( 'buddyc_xprofile_field_' . $this->field_key, $this->field_id );
        }
    }
    
    /**
     * Retreives options for existing field.
     * 
     * @since 0.1.0
     * 
     * @param   int     $field_id   The ID of the xprofile field.
     */
    public static function get_options( $field_id ) {
        
        // Initialize
        $options = [];
        
        // Attempt to fetch field by id
        $existing_field = xprofile_get_field( $field_id );
        
        // Make sure field exists
        if ( $existing_field ) {
            
            // Get children
            $children = $existing_field->get_children();
            
            if ( $children ) {
                foreach ( $children as $child ) {
                    // Add field to options array
                    $options[$child->id] = [
                        'value' => $child->id,
                        'label' => $child->name,
                        
                    ];
                }
            }
        }
        return $options;
    }
    
    /**
     * Updates field options.
     * 
     * @since 0.1.0
     */
    public function update_options() {
        
        // Exit if field type has no options
        if ( $this->field->type !== 'checkbox' && $this->field->type !== 'selectbox' ) {
            return;
        }
        
        // Add new options to the field
        if ( $this->args['field_options'] ) {
            
            // Clear existing options
            self::clear_existing_options( $this->field_id );
            
            // Loop through options and add to field
            foreach ( $this->args['field_options'] as $option ) {
                xprofile_insert_field(array(
                    'field_group_id'    => $this->field->group_id,
                    'parent_id'         => $this->field_id,
                    'type'              => 'option',
                    'name'              => $option,
                ));
            }
        }
    }
    
    /**
     * Clears existing options associated with the field.
     *
     * @since 0.1.0
     * 
     * @param   int     $field_id       The ID of the xprofile field.
     */
    private static function clear_existing_options( $field_id ) {
        
        // Get options
        $children = self::get_options( $field_id );
        
        // Make sure field ID and children exist
        if ( $field_id && $children ) {
            foreach ( $children as $child_id => $data ) {
                xprofile_delete_field( $child_id );
            }
        }
    }
    
    /**
     * Updates field member types.
     * 
     * @since 0.1.0
     */
    public function update_member_types() {
        
        // Make sure member types are defined
        if ( $this->args['member_types'] ) {
        
            // Get user types
            $member_types = buddyc_get_setting( 'general', $this->args['member_types'] . '_types' );

            // Replace existing associations
            $append = false;
            
            // Update the field member types
            $this->field->set_member_types( $member_types, $append );
        }
    }
    
    /**
     * Checks if the instance is locked.
     * 
     * @since 0.1.0
     */
    private function is_locked() {
        return get_transient(self::LOCK_TRANSIENT_PREFIX . $this->field_key);
    }
    
    /**
     * Sets a lock to prevent simultaneous execution for the same field.
     * 
     * @since 0.1.0
     */
    private function set_lock() {
        set_transient(self::LOCK_TRANSIENT_PREFIX . $this->field_key, true, 5);
    }

    /**
     * Releases the lock to allow a new instance to be created for the field.
     * 
     * @since 0.1.0
     */
    private function release_lock() {
        delete_transient(self::LOCK_TRANSIENT_PREFIX . $this->field_key);
    }
}
