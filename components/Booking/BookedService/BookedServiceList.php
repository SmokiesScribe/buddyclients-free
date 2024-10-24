<?php
namespace BuddyClients\Components\Booking\BookedService;

use BuddyClients\Components\Booking\BookingIntent;
use BuddyClients\Includes\Form\Form;
use BuddyClients\Includes\Pagination;

/**
 * List of booked services for a user or group.
 * 
 * Genreates a list of booked services for clients and team members.
 * Displays data and forms based on the user's type and permissions.
 * Allows team members to update the status of their services.
 *
 * @since 0.1.0
 */
class BookedServiceList {
    
    /**
     * Type of current user.
     * Accepts 'admin', 'client', and 'team'.
     * 
     * @var string
     */
    private $user_is;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        $this->project_id = bp_get_current_group_id();
    }
    
    /**
     * Builds table.
     * 
     * @since 0.1.0
     */
    public function build() {
        
        // Check current user role
        $this->user_is = $this->user_is();
        
        // Output table
        $table = $this->table();
        $allowed_html = bc_allowed_html_form();
        echo wp_kses( $table, $allowed_html );
    }
    
    /**
     * Checks current user.
     * 
     * @since 0.1.0
     */
    private function user_is() {  
        if ( bc_is_team() ) {
            return 'team';
        } else if ( bc_is_client() ) {
            return 'client';
        } else if ( bc_is_admin() ) {
            return 'admin';
        }
    }
    
    /**
     * Defines table columns.
     * 
     * @since 0.1.0
     */
    private function headers() {
        
        $admin_headers = [
            __( 'Date', 'buddyclients' ),
            __( 'Service', 'buddyclients' ),
            __( 'Client', 'buddyclients' ),
            __( 'Project', 'buddyclients' ),
            __( 'Team Member', 'buddyclients' ),
            __( 'Status', 'buddyclients' ),
            __( 'Files', 'buddyclients' ),
            __( 'Client Fee', 'buddyclients' ),
            __( 'Team Fee', 'buddyclients' ),
            __( 'Cancel', 'buddyclients' )
        ];
        
        $client_headers = [
            __( 'Date', 'buddyclients' ),
            __( 'Service', 'buddyclients' ),
            __( 'Project', 'buddyclients' ),
            __( 'Team Member', 'buddyclients' ),
            __( 'Status', 'buddyclients' ),
            __( 'Files', 'buddyclients' ),
            __( 'Client Fee', 'buddyclients' ),
            __( 'Cancel', 'buddyclients' )
        ];
        
        $team_headers = [
            __( 'Date', 'buddyclients' ),
            __( 'Service', 'buddyclients' ),
            __( 'Client', 'buddyclients' ),
            __( 'Project', 'buddyclients' ),
            __( 'Status', 'buddyclients' ),
            __( 'Files', 'buddyclients' ),
            __( 'Team Fee', 'buddyclients' ),
            __( 'Update Status', 'buddyclients' )
        ];
        
        switch ( $this->user_is ) {
            case 'admin':
                return $admin_headers;
                break;
            case 'team':
                return $team_headers;
                break;
            case 'client':
                return $client_headers;
                break;
        }
    }
    
    /**
     * Retrieves BookedServices.
     * 
     * @since 0.1.0
     */
    private function get_booked_services() {
        
        // Project services
        if ( $this->project_id ) {
            return BookedService::get_services_by( 'project_id', $this->project_id );

        // Client or team member
        } else if ( $this->user_is === 'client' || $this->user_is === 'team' ) {
            $client_services = BookedService::get_services_by( 'client_id', get_current_user_id() );
            $team_services = BookedService::get_services_by( 'team_id', get_current_user_id() );
            return array_merge( $client_services, $team_services );

        // Admin
        } else if ( $this->user_is === 'admin' ) {
            return BookedService::get_all_services();
        }
    }
    
    /**
     * Outputs table.
     * 
     * @since 0.1.0
     */
    private function table() {
        // Initialize content
        $content = '';
    
        // Get booked services
        $booked_services = $this->get_booked_services();
    
        // No booked services
        if (!$booked_services) {
            return __('You do not have any booked services.', 'buddyclients');
        }
    
        // Paginate
        $pagination = new Pagination( $booked_services );
    
        // Start table
        $content .= '<table class="bc-booked-services-table">';
        $content .= $this->render_table_header();
    
        // Render table rows
        $content .= $this->render_table_rows( $pagination->paginated_items );
    
        // End table
        $content .= '</table>';
    
        // Add pagination controls
        $content .= $pagination->controls();
    
        return $content;
    }
    
    /**
     * Get headers for the table based on the user type.
     *
     * @return array
     */
    private function get_headers() {
        return $this->headers();
    }
    
    /**
     * Render the table headers.
     *
     * @return string
     */
    private function render_table_header() {
        $headers = $this->get_headers();
        $header_content = '<tr>';
        foreach ($headers as $header) {
            $header_content .= '<th>' . $header . '</th>';
        }
        $header_content .= '</tr>';
        return $header_content;
    }
    
    /**
     * Render all rows of the table.
     *
     * @param array $items
     * @return string
     */
    private function render_table_rows($items) {
        $rows = '';
        foreach ($items as $item) {
            if (!property_exists($item, 'client_id') || !property_exists($item, 'project_id') || !property_exists($item, 'status')) {
                continue;
            }
            $rows .= $this->render_table_row($item);
        }
        return $rows;
    }
    
    /**
     * Defines the translated label for a status.
     *
     * @param string $status
     * @return string
     */
    private function get_status_label( $status ) {
        $status_map = [
            'pending'                   => __('Pending', 'buddyclients'),
            'in_progress'               => __('In Progress', 'buddyclients'),
            'cancellation_requested'    => __('Cancellation Requested', 'buddyclients'),
            'canceled'                  => __('Canceled', 'buddyclients'),
            'complete'                  => __('Complete', 'buddyclients'),
        ];

        $formatted_status = $status_map[$status]?? ucwords( str_replace('_', ' ', $status) );
        return $formatted_status;
    }

    /**
     * Retrieves the status icon.
     *
     * @param string $status
     * @return string
     */
    private function get_status_icon( $status ) {
        $icons = [
            'pending'                   => 'default',
            'in_progress'               => 'ready',
            'cancellation_requested'    => 'ready',
            'canceled'                  => 'x',
            'complete'                  => 'check'
        ];

        $icon = isset( $icons[$status] ) ? bc_admin_icon( $icons[$status] ) : '';
        return $icon . ' ';
    }

    /**
     * Builds the status string.
     *
     * @param string $status
     * @return string
     */
    private function build_status( $status ) {
        $icon = $this->get_status_icon( $status );
        $label = $this->get_status_label( $status );
        return $icon . $label;
    }
    
    /**
     * Get the table columns for a single item.
     *
     * @param object $item
     * @return array
     */
    private function get_table_columns($item) {    
        return [
            __('Date', 'buddyclients')            => $item->created_at ? gmdate('F j, Y', strtotime($item->created_at)) : '',
            __('Service', 'buddyclients')         => $item->name,
            __('Client', 'buddyclients')          => bp_core_get_userlink($item->client_id),
            __('Project', 'buddyclients')         => bc_group_link( $item->project_id ),
            __('Team Member', 'buddyclients')     => bp_core_get_userlink($item->team_id),
            __('Status', 'buddyclients')          => $this->build_status($item->status),
            __('Files', 'buddyclients')           => bc_download_links($item->file_ids, true),
            __('Client Fee', 'buddyclients')      => '$' . $item->client_fee,
            __('Team Fee', 'buddyclients')        => '$' . $item->team_fee,
            __('Cancel', 'buddyclients')          => (new CancelRequestForm($item->ID))->build(),
            __('Update Status', 'buddyclients')   => (new ServiceStatusForm)->build(['update_status' => $item->status, 'booked_service_id' => $item->ID]),
        ];
    }
    
    /**
     * Render a single row of the table.
     *
     * @param object $item
     * @return string
     */
    private function render_table_row($item) {
        $columns = $this->get_table_columns($item);
        $headers = $this->get_headers();
        $row = '<tr>';
        foreach ($columns as $header => $value) {
            if (in_array($header, $headers)) {
                $row .= '<td>' . $value . '</td>';
            }
        }
        $row .= '</tr>';
        return $row;
    }
    
    /**
     * Builds form.
     * 
     * @since 0.1.0
     */
    public function form( $key, $values = null ) {
        
        $forms = [
            'cancel' => [
                'key'               => 'cancel_service',
                'fields_callback'   => [$this, 'cancellation_fields'],
                'submit_text'       => 'Request Cancellation',
                'submission_class'  => __NAMESPACE__ . '/CancelRequestSubmission',
                'values'            => $values
            ],
            'update_status' => [
                'key'               => 'update_service_status',
                'fields_callback'   => [$this, 'status_fields'],
                'submission_class'  => __NAMESPACE__ . '/ServiceStatusSubmission',
                'submit_text'       => 'Update Status',
                'values'            => $values
            ],
        ];
        
        return ( new Form( $forms[$key] ) )->build();
    }
    
    /**
     * Builds cancellation form fields.
     * 
     * @since 0.1.0
     */
    public function cancellation_fields( $values = null ) {
        
        // Define field args
         $args = [
             [
                'key'           => 'cancellation_reason',
                'type'          => 'text',
                'placeholder'   => 'Cancellation reason...',
                'required'      => true
            ],
            [
                'key'           => 'booked_service_id',
                'type'          => 'hidden',
                'value'         => $values['booked_service_id'] ?? '',
                'required'      => true
            ]
        ];
        
        return $args;
    }
    
    /**
     * Builds status form fields.
     * 
     * @since 0.1.0
     */
    public function status_fields( $values = null ) {
        
        // Initialize
        $fields = '';
        
        // Status dropdown options
        $options = [
            '' => [
                'label' => 'Update Status',
                'value' => '',
            ],
            'pending' => [
                'label' => 'Pending',
                'value' => 'pending',
            ],
            'complete' => [
                'label' => 'Complete',
                'value' => 'complete',
            ],
        ];
        
        // Status dropdown
         $args = [
             'update_status' => [
                'key'           => 'update_status',
                'type'          => 'dropdown',
                'options'       => $options,
                'value'         => $values['update_status'] ?? '',
                'required'      => true
            ],
            'booked_service_id' => [
                'key'           => 'booked_service_id',
                'type'          => 'hidden',
                'value'         => $values['booked_service_id'] ?? '',
                'required'      => true
            ]
        ];
        
        foreach ( $args as $key => $field_args ) {
            $fields .= ( new FormField( $field_args ) )->build();
        }
        return $fields;
    }
}