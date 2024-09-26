<?php
namespace BuddyClients\Admin;

use BuddyClients\Includes\Loader as Loader;

/**
 * Generates a single repair button.
 *
 * @since 0.1.0
 */
class RepairButton {
    
    /**
     * Key.
     * 
     * @var string
     */
    private $key;
    
    /**
     * Post type slug.
     * 
     * @var string
     */
    private $post_type;
    
    /**
     * The callback function.
     * 
     * @var callable|null
     */
    private $callback;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct( $key, $args ) {
        $this->key          = $key;
        $this->post_type    = $args['post_type'];
        $this->callback     = $args['callback'] ?? null;
        
        // Define hooks
        $this->define_hooks();
    }
    
    /**
     * Retrieves post type label.
     * 
     * @since 0.1.0
     */
    private function get_label() {
        
        // Get the post type object
        $post_type_object = get_post_type_object( $this->post_type );
        
        // Check if the post type object exists
        if ( $post_type_object ) {
            
            // Retrieve the labels from the post type object
            $labels = $post_type_object->labels;
            return $labels->name;
        }
    }
    
    /**
     * Defines hooks.
     * 
     * @since 0.1.0
     */
    private function define_hooks() {
        add_action( 'admin_notices', [$this, 'form'] );
        add_action( 'admin_post_repair_' . $this->key, [$this, 'submission'] );
        add_action( 'admin_post_nopriv_repair_' . $this->key, [$this, 'submission'] );
    }
    
    /**
     * Builds the form.
     * 
     * @since 0.1.0
     */
    public function form() {
        global $pagenow;
    
        // Check if it's the admin page for the post type
        if ( $pagenow == 'edit.php' && isset( $_GET['post_type']) && $_GET['post_type'] == $this->post_type ) {
            
            $form = '';
            $form .= '<br>';
            $form .= '<form method="post" action="' . admin_url("admin-post.php") . '">';
            $form .= '    <input type="hidden" name="action" value="repair_' . esc_attr( $this->key ) . '">';
            $form .= '    <button type="submit" name="repair_' . esc_attr( $this->key ) . '_submit" class="button button-secondary">';
            $form .= esc_html__( 'Repair', 'buddyclients' ) . ' ' . esc_html( $this->get_label() );
            $form .= '</button>';
            $form .= '</form>';
            
            echo $form;
        }
    }
    
    /**
     * Handles submission.
     * 
     * @since 0.1.0
     */
    public function submission() {
        // Check if the form has been submitted
        if (isset($_POST['repair_' . esc_attr( $this->key ) . '_submit'])) {
            
            // If a repair callback function is provided, call it
            if (is_callable($this->callback)) {
                call_user_func($this->callback);
            }
            
            // Redirect to post type list
            wp_redirect(admin_url( 'edit.php?post_type=' . esc_attr( $this->post_type ) ));
            exit;
        }
    }
}
