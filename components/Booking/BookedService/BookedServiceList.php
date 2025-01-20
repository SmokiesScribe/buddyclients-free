<?php
namespace BuddyClients\Components\Booking\BookedService;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
        if ( function_exists( 'bp_get_current_group_id' ) ) {
            $this->project_id = bp_get_current_group_id();
        }
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
        $allowed_html = buddyc_allowed_html_form();
        if ( ! isset( $allowed_html['td']['data-label'] ) ) {
            $allowed_html['td']['data-label'] = [];
        }
        echo wp_kses( $table, $allowed_html );
    }
    
    /**
     * Checks current user.
     * 
     * @since 0.1.0
     */
    private function user_is() {  
        if ( buddyc_is_admin() ) {
            return 'admin';
        } else if ( buddyc_is_team() ) {
            return 'team';
        } else if ( buddyc_is_client() ) {
            return 'client';
        }
    }
    
    /**
     * Defines table columns.
     * 
     * @since 0.1.0
     */
    private function headers() {        
        
        $admin_headers = [
            __( 'Date', 'buddyclients-free' ),
            __( 'Service', 'buddyclients-free' ),
            __( 'Project', 'buddyclients-free' ),
            __( 'Client Fee', 'buddyclients-free' ),
            __( 'Team Fee', 'buddyclients-free' ),
            __( 'Client', 'buddyclients-free' ),
            __( 'Team Member', 'buddyclients-free' ),
            __( 'Status', 'buddyclients-free' ),
            __( 'Files', 'buddyclients-free' ),
        ];
        
        $client_headers = [
            __( 'Date', 'buddyclients-free' ),
            __( 'Service', 'buddyclients-free' ),
            __( 'Client Fee', 'buddyclients-free' ),
            __( 'Project', 'buddyclients-free' ),
            __( 'Team Member', 'buddyclients-free' ),
            __( 'Status', 'buddyclients-free' ),
            __( 'Files', 'buddyclients-free' ),
        ];
        
        $team_headers = [
            __( 'Date', 'buddyclients-free' ),
            __( 'Service', 'buddyclients-free' ),
            __( 'Client', 'buddyclients-free' ),
            __( 'Project', 'buddyclients-free' ),
            __( 'Team Member', 'buddyclients-free' ),
            __( 'Status', 'buddyclients-free' ),
            __( 'Files', 'buddyclients-free' ),
            __( 'Team Fee', 'buddyclients-free' ),
            __( 'Payment Status', 'buddyclients-free' ),
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
        if ( ! $booked_services ) {
            return __('You do not have any booked services.', 'buddyclients-free');
        }

        // Open container
        $content .= '<div class="buddyc-booked-services-table-container">';
    
        // Paginate
        $pagination = new Pagination( $booked_services );
    
        // Start table
        $content .= '<table class="buddyc-booked-services-table">';
        $content .= $this->render_table_header();
    
        // Render table rows
        $content .= $this->render_table_rows( $pagination->paginated_items );
    
        // End table
        $content .= '</table>';
    
        // Add pagination controls
        $content .= $pagination->controls();

        // Close container
        $content .= '</div>';
    
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
        $header_content = '<thead>';
        $header_content .= '<tr>';
        foreach ( $headers as $header ) {
            $header_content .= '<th>' . $header . '</th>';
        }
        $header_content .= '</tr>';
        $header_content .= '</thead>';
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
     * Get the table columns for a single item.
     *
     * @param object $item
     * @return array
     */
    private function get_table_columns($item) {    
        return [
            __('Date', 'buddyclients-free')            => $item->created_at ? gmdate('F j, Y', strtotime($item->created_at)) : '',
            __('Service', 'buddyclients-free')         => $item->name,
            __('Client', 'buddyclients-free')          => bp_core_get_userlink($item->client_id),
            __('Client Fee', 'buddyclients-free')      => '$' . $item->client_fee,
            __('Project', 'buddyclients-free')         => buddyc_group_link( $item->project_id ),
            __('Team Member', 'buddyclients-free')     => bp_core_get_userlink($item->team_id),
            __('Status', 'buddyclients-free')          => $this->service_status( $item ),
            __('Files', 'buddyclients-free')           => buddyc_download_links($item->file_ids, false),
            __('Team Fee', 'buddyclients-free')        => $this->team_fee( $item ),
            __('Payment Status', 'buddyclients-free')  => $this->team_payment_status( $item->ID ),
        ];
    }

    /**
     * Outputs the team fee.
     * 
     * @since 1.0.21
     * 
     * @param   BookedService   $booked_service     The BookedService object.
     */
    private function team_fee( $booked_service ) {
        // Make sure the user is allowed to view the fee
        if ( $this->user_is === 'admin' || $booked_service->team_id === get_current_user_id() ) {
            return '$' . $booked_service->team_fee;
        }        
    }
    
    /**
     * Render a single row of the table.
     *
     * @param object $item
     * @return string
     */
    private function render_table_row( $item ) {
        $columns = $this->get_table_columns( $item );
        $headers = $this->get_headers();
        $row = '<tr>';
        foreach ( $headers as $header ) {
            if ( array_key_exists( $header, $columns ) ) {
                $row .= '<td data-label="' . $header . '">' . $columns[$header] . '</td>';
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
        // Initialize fields string
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

        // Arguments for the fields
        $args = [
            'update_status' => [
                'key'      => 'update_status',
                'type'     => 'dropdown',
                'options'  => $options,
                'value'    => $values['update_status'] ?? '',
                'required' => true,
            ],
            'booked_service_id' => [
                'key'      => 'booked_service_id',
                'type'     => 'hidden',
                'value'    => $values['booked_service_id'] ?? '',
                'required' => true,
            ]
        ];

        // Build each field and append to $fields
        foreach ( $args as $key => $field_args ) {
            $fields .= ( new FormField( $field_args ) )->build();
        }

        return $fields;
    }

    /**
     * Defines the translated label for a status.
     *
     * @param string $status The status to get the label for.
     * @return string The translated label for the status.
     */
    private function get_status_label( $status ) {
        $status_map = [
            'pending'                => __('Pending', 'buddyclients-free'),
            'in_progress'            => __('In Progress', 'buddyclients-free'),
            'cancellation_requested' => __('Cancellation Requested', 'buddyclients-free'),
            'canceled'               => __('Canceled', 'buddyclients-free'),
            'complete'               => __('Complete', 'buddyclients-free'),
            'eligible'               => __('Eligible', 'buddyclients-free'),
            'paid'                   => __('Paid', 'buddyclients-free'),
        ];

        // If the status exists in the map, return it; otherwise, format it
        $formatted_status = $status_map[$status] ?? ucwords( str_replace( '_', ' ', $status ) );
        return $formatted_status;
    }

    /**
     * Retrieves the status icon.
     *
     * @param string $status The status to get the icon for.
     * @return string The icon corresponding to the status.
     */
    private function get_status_icon( $status ) {
        $icons = [
            'pending'                => 'default',
            'in_progress'            => 'ready',
            'cancellation_requested' => 'ready',
            'canceled'               => 'x',
            'complete'               => 'check',
        ];

        // Retrieve the corresponding icon or return an empty string if not found
        $icon = isset( $icons[$status] ) ? buddyc_admin_icon( $icons[$status] ) : '';
        return $icon . ' ';
    }

    /**
     * Builds the status HTML for a given item.
     *
     * @param object $item The item object containing status information.
     * @return string The status HTML for the item.
     */
    private function build_status( $item ) {
        $status = $item->status ?? null;
        if ( ! $status ) {
            return '';
        }

        // Get status label and formatted status HTML
        $label = $this->get_status_label( $status );
        $formatted_status = '<div class="buddyc-service-status ' . esc_attr( $status ) . '">' . esc_html( $label ) . '</div>';

        // Add any date note for the status
        $date_note = self::service_status_date_note( $item, $status );
        return $formatted_status . $date_note;
    }

    /**
     * Builds the service status content.
     *
     * @param BookedService $booked_service The BookedService object.
     * @return string The service status HTML.
     */
    private function service_status( $booked_service ) {
        // Initialize date note
        $date_note = '';

        // Get service status
        $service_status = $booked_service->status;

        // Get update form
        $update_form = $this->update_service_status_form( $service_status, $booked_service );

        // Get cancel form
        $cancel_form = $this->cancel_form( $booked_service );

        // Get status label and formatted status HTML
        $label = $this->get_status_label( $service_status );
        $formatted_status = '<div class="buddyc-service-status ' . esc_attr( $service_status ) . '">' . esc_html( $label ) . ( $update_form['button'] ?? '' ) . ( $cancel_form['button'] ?? '' ) . '</div>';

        // Add date completed note if the status is complete
        if ( $service_status === 'complete' ) {
            $date_note = $this->service_status_date_note( $booked_service, $service_status );
        }

        return $formatted_status . $date_note . ( $update_form['form'] ?? '' ) . ( $cancel_form['form'] ?? '' );
    }

    /**
     * Outputs the update service status form.
     *
     * @param   string          $service_status     The status of the BookedService.
     * @param   BookedService   $booked_service     The BookedService object.
     */
    private function update_service_status_form( $service_status, $booked_service ) {
        $form = (new ServiceStatusForm)->build(['update_status' => $service_status, 'booked_service_id' => $booked_service->ID]);
        if ( $form ) {
            $icon = buddyc_icon( 'edit' );
            $form_container_id = 'buddyc-update-service-form-container-' . $booked_service->ID;
            $title = __( 'Edit service status', 'buddyclients-free' );
            $edit_button = '<a class="buddyc-service-edit-button" title="' . $title . '" onclick="buddycShowElement(\'' . $form_container_id . '\')">' . $icon . '</a>';
            $wrapped_form = '<div id="' . esc_attr( $form_container_id ) . '" class="buddyc-update-service-form-container">' . $form . '</div>';

            return ['form' => $wrapped_form, 'button' => $edit_button];
        }
    }

    /**
     * Outputs the cancellation request form.
     *
     * @param   BookedService   $booked_service     The BookedService object.
     */
    private function cancel_form( $booked_service ) {
        $form = ( new CancelRequestForm( $booked_service->ID ) )->build();
        if ( $form ) {
            $icon = buddyc_icon( 'x' );
            $form_container_id = 'buddyc-cancel-service-form-container-' . $booked_service->ID;
            $title = __( 'Request service cancellation', 'buddyclients-free' );
            $edit_button = '<a class="buddyc-service-edit-button" title="' . $title . '" onclick="buddycShowElement(\'' . $form_container_id . '\')">' . $icon . '</a>';
            $wrapped_form = '<div id="' . esc_attr( $form_container_id ) . '" class="buddyc-update-service-form-container">' . $form . '</div>';

            return ['form' => $wrapped_form, 'button' => $edit_button];
        }
    }

    /**
     * Generates a note for the date a service was completed.
     * 
     * @since 1.0.21
     * 
     * @param BookedService $booked_service The BookedService object.
     * @param string $status The status of the BookedService.
     * @return string The HTML note with the date of completion, if applicable.
     */
    private function service_status_date_note( $booked_service, $status ) {
        // Return early if the status is not 'complete'
        if ( $status !== 'complete' ) {
            return '';
        }

        // Get completed date and return an empty string if not available
        $timestamp = $booked_service->complete_date ?? null;
        if ( ! $timestamp ) {
            return '';
        }

        // Format and return the date note HTML
        $prefix = __( 'Complete ', 'buddyclients-free' );
        $formatted_date = gmdate( 'M j, Y', $timestamp );
        return '<div class="buddyc-service-status-date">' . $prefix . $formatted_date . '</div>';
    }

    /**
     * Retrieves the status of the associated team payment.
     * 
     * @since 0.1.0
     * 
     * @param int $booked_service_id The ID of the BookedService.
     * @return string The payment status HTML.
     */
    private function team_payment_status( $booked_service_id ) {
        // Initialize date note
        $date_note = '';

        // Get payment status and payment object
        $payment_status = BookedService::team_payment_status( $booked_service_id );
        $payment = BookedService::team_payment_object( $booked_service_id );
        $formatted_status = $payment_status ? $this->build_status( $payment ) : '';

        // Add date note for eligible or paid status
        if ( $payment_status === 'pending' || $payment_status === 'paid' ) {
            $date_note = $this->payment_status_date_note( $payment, $payment_status );
        }

        return $formatted_status . $date_note;
    }

    /**
     * Generates a note for the date a payment is eligible or paid.
     * 
     * @since 0.1.0
     * 
     * @param Payment $payment The Payment object.
     * @param string $payment_status The status of the Payment.
     * @return string The HTML note with the payment date, if applicable.
     */
    private function payment_status_date_note( $payment, $payment_status ) {
        switch ( $payment_status ) {
            case 'pending':
                $timestamp = $payment->time_eligible ?? null;
                $prefix = __( 'Eligible ', 'buddyclients-free' );
                break;
            case 'paid':
                $timestamp = strtotime( $payment->paid_date ) ?? null;
                $prefix = __( 'Paid ', 'buddyclients-free' );
                break;
            default:
                return '';  // Return early if the status doesn't match
        }

        // Return an empty string if no timestamp is available
        if ( ! $timestamp ) {
            return '';
        }

        // Format and return the payment date note HTML
        $formatted_date = gmdate( 'M j, Y', $timestamp );
        return '<div class="buddyc-service-status-date">' . $prefix . $formatted_date . '</div>';
    }
}