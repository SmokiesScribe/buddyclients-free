<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use DateTime;
use stdClass;

use BuddyClients\Components\Booking\BookingIntent;

use BuddyClients\Components\Contact\Lead\LeadStatusForm;

use BuddyClients\Components\Booking\BookedService\{
    BookedService,
    PaymentStatusForm,
    ServiceStatusForm,
    ReassignForm,
    Payment
};

use BuddyClients\Includes\PDF;
use BuddyEvents\Includes\Sponsor\SponsorIntent;

/**
 * Generates a single value for an admin table item.
 * 
 * @since 1.0.0
 */
class AdminTableItem extends AdminTable {
    
    /**
     * Echos the value without modification.
     * 
     * @since 0.1.0
     */
    protected static function direct( $property, $value ) {
        return $value;
    }
    
    /**
     * Makes value uppercase.
     * 
     * @since 0.1.0
     */
    protected static function uc_format( $value ) {
        if ( is_array( $value ) ) {
            return implode( ', ', $value );
        } else {
            if ( ! empty( $value ) ) {
                $value = str_replace( '_', ' ', $value );
                return ucwords( $value );
            }
        }
    }
    
    /**
     * Outputs the post title.
     * 
     * @since 0.4.0
     */
    protected static function post_title( $property, $value ) {
        if ( is_array( $value ) ) {
            $titles = [];
            foreach ( $value as $post_id ) {
                $titles[] = get_the_title( $post_id );
            }
            return implode( ', ', $titles );
        } else {
            return get_the_title( $value );
        }
    }
    
    /**
     * Outputs the post link.
     * 
     * @since 0.4.0
     */
    protected static function post_link( $property, $value ) {
        if ( is_array( $value ) ) {
            $links = [];
            foreach ( $value as $post_id ) {
                $title = get_the_title( $post_id );
                $link = get_permalink( $post_id );
                if ( $title ) {
                    $links[] = '<a href="' . esc_url( $link ) . '">' . esc_html( $title ) . '</a>';
                }
            }
            return implode( ', ', $links );
        } else {
            $title = get_the_title( $value );
            $link = get_permalink( $value );
            return '<a href="' . esc_url( $link ) . '">' . esc_html( $title ) . '</a>';
        }
    }

    /**
     * Outputs a link to service details. 
     * 
     * @since 1.0.20
     */
    protected static function service_names_link( $property, $value, $item_id ) {
        $status = buddyc_get_booking_intent_status( $item_id );
        if ( $status === 'incomplete' ) {
            return $value;
        }

        $url = admin_url( '/admin.php?page=buddyc-booked-services&booking_filter=' . $item_id );
        return '<a href="' . esc_url( $url ) . '">' . esc_html( $value ) . '</a>';
    }

    /**
     * Outputs a link to a Stripe transaction.
     * 
     * @since 1.0.21
     */
    protected static function stripe_transaction_link( $property, $value, $item_id ) {
        $payment_id = $value;
        if ( ! empty( $payment_id ) ) {
            $url = 'https://dashboard.stripe.com/payments/' . $payment_id;
            $icon = '<i class="fa-solid fa-arrow-up-right-from-square"></i>';
            /* translators: %s: Stripe, the name of the payment processor */
            $title = sprintf( __( 'View transaction on %s', 'buddyclients-free' ), 'Stripe' );
            $link = '<span class="buddyc-update-booking-icon"><a href="' . esc_url( $url ) . '" target="_blank" title="' . $title . '">' . $icon . '</a></span>';
            return $link;
        }
    }
    
    /**
     * Generates user link.
     * 
     * @since 0.1.0
     * @updated 0.4.0
     */
    protected static function user_link( $property, $value ) {
        // If the value is an array, process each element
        if ( is_array( $value ) ) {
            $results = array();
            foreach ( $value as $single_value ) {
                // Check if the user exists
                if ( get_user_by( 'id', $single_value ) ) {
                    $results[] = bp_core_get_userlink( $single_value );
                } else {
                    $results[] = esc_html( $single_value );
                }
            }
            return implode( ', ', $results );
        }
    
        // If the value is a single value
        // Check if the user exists
        if ( get_user_by( 'id', $value ) ) {
            return bp_core_get_userlink( $value );
        } else {
            return esc_html( $value );
        }
    }

    /**
     * Outputs user link and email
     * 
     * @since 0.1.21
     */
    protected static function booking_user_link_email( $property, $value, $item_id ) {
        $user_link = self::user_link($property, $value );
        return $user_link;
    }
    
    /**
     * Generates service agreement download link.
     * 
     * @since 0.2.6
     */
    protected static function service_agreement( $property, $value, $booking_id ) {
        
        // Initialize
        $pdf_link = null;
        
        // Get booking intent
        $booking_intent = buddyc_get_booking_intent( $booking_id );
        
        // Check for existing PDF link
        if ( $booking_intent->terms_pdf_link ) {
            $pdf_link = $booking_intent->terms_pdf_link;
            
        } else {
        
            // Terms version exists
            if ( $value ) {
                
                // Generate PDF
                $pdf_link = buddyc_generate_service_agreement_pdf( $booking_intent );
                
                // Update booking intent
                buddyc_update_booking_intent( $booking_intent->ID, 'terms_pdf_link', $pdf_link );
            }
        }
            
        // Generate download link
        if ( $pdf_link ) {
            $download_link = '<a href="' . esc_url( $pdf_link ) . '" download><i class="fa-solid fa-download"></i> ' . __( 'Download PDF', 'buddyclients-free' ) . '</a>';
            return $download_link;
        }
    }
    
    /**
     * Generates a service agreement download link.
     * 
     * @since 0.4.0
     */
    protected static function terms_pdf( $property, $value ) {
        if( class_exists( PDF::class ) ) {
            return PDF::download_link( $value, 'Agreement' );
        }
    }
    
    /**
     * Generates an agreement download link.
     * 
     * @since 0.4.0
     * 
     * @param   string      $type       The type of agreement.
     *                                  Accepts 'team', 'affiliate', and 'faculty'.
     */
    protected static function agreement_download( $property, $value, $type ) {
        if ( function_exists( 'buddyc_latest_user_agreement' ) ) {
            $user_id = $value;
            $agreement = buddyc_latest_user_agreement( $user_id, $type );
            if ( $agreement && isset( $agreement->pdf ) ) {
                return PDF::download_link( $agreement->pdf, $type );
            }
        }
    }
    
    /**
     * Generates team and affiliate agreement links.
     * 
     * @since 0.2.6
     * @updated 0.4.0
     */
    protected static function agreements( $property, $value ) {
        // Initialize
        $agreements = [];
        
        // Define agreement types
        $types = [
            'team'     => __( 'Team', 'buddyclients-free' ),
            'faculty'  => __( 'Faculty', 'buddyclients-free' ),
            'affiliate'=> __( 'Affiliate', 'buddyclients-free' )
        ];
        
        // Loop through types
        foreach ( $types as $type_key => $type_label ) {
            // Build download links
            $download_link = self::agreement_download( $property, $value, $type_key );
            
            // Make sure the link exists
            if ( $download_link ) {
                // Add to array
                $agreements[] = $download_link;
            }
        }
    
        // Implode the array to a string
        $agreements_string = implode( '<br>', $agreements );
        return $agreements_string;
    }

    /**
     * Generates a download link from a PDF ID.
     * 
     * @since 1.0.17
     */
    protected static function pdf_download( $property, $value ) {
        $pdf_id = $value;
        if ( ! empty( $pdf_id ) ) {
            return PDF::download_link( $pdf_id );
        }
    }

    /**
     * Retrieves the status of a user agreement.
     * 
     * @since 1.0.17
     */
    protected static function agreement_status( $property, $value ) {
        $agreement_id = $value;
        if ( function_exists( 'buddyc_agreement_status' ) ) {
            $status = buddyc_agreement_status( $agreement_id );
            if ( $status ) {
                $status = ucwords( $status );
            } else {
                $status = 'Not Current';
            }

            $icons = [
                'Current'       => 'check',
                'Active'        => 'ready',
                'Not Current'   => 'x'
            ];

            $icon = isset( $icons[$status] ) ? buddyc_admin_icon( $icons[$status] ) . ' ' : '';

            return $icon . $status;
        }
    }
    
    /**
     * Generates download links.
     * 
     * @since 0.1.0
     */
    protected static function files( $property, $value ) {
        return buddyc_download_links( $value, true );
    }
    
    /**
     * Generates a link to faculty sessions.
     * 
     * @since 0.1.0
     */
    protected static function faculty_sessions_link( $property, $value, $item_id ) {
        // Make sure the user has sessions
        if ( $value && is_array( $value ) && ! empty( $value ) ) {
            $url = admin_url( '/admin.php?page=buddyc-sessions&faculty_ids_filter=' . $item_id );
            return '<a href="' . esc_url( $url ) . '">' . __( 'Sessions', 'buddyclients-free' ) . '</a>';
        }
    }
    
    /**
     * Generates an item from a faculty form.
     * 
     * @since 0.1.0
     */
    protected static function faculty_form_item( $property, $value, $item_id, $key ) {
        // Tax form check
        if ( $key === 'tax_form' ) {
            return ( isset( $value[$key] ) && $value[$key] ) ? buddyc_admin_icon( 'check' ) : buddyc_admin_icon( 'x' );
            
        // Receipt downloads
        } else if ( $key === 'expense_receipts') {
            if ( isset( $value[$key] ) ) {
                return buddyc_download_links( $value[$key], true );
            }
        }
        
        // Default to echo
        return isset( $value[$key] ) && $value[$key] ? ucfirst( $value[$key] ) : '';
    }
    
    /**
     * Generates payment status form.
     * 
     * @since 0.1.0
     */
    protected static function payment_form( $property, $value, $item_id ) {
        $values = ['payment_status' => $value, 'payment_id' => $item_id];
        return ( new PaymentStatusForm )->build( $values );
    }
    
    /**
     * Generates service status form.
     * 
     * @since 0.1.0
     */
    protected static function status_form( $property, $value, $item_id ) {
        $values = ['update_status' => $value, 'booked_service_id' => $item_id];
        return ( new ServiceStatusForm )->build( $values );
    }
    
    /**
     * Generates reassign team form.
     * 
     * @since 0.1.0
     */
    protected static function reassign_form( $property, $value, $item_id ) {
        $values = ['team_id' => $value, 'booked_service_id' => $item_id];
        return ( new ReassignForm )->build( $values );
    }
    
     /**
     * Generates icon with value.
     * 
     * @since 0.1.0
     */
    protected static function icons( $property, $value ) {
        
        $icons = [
            'pending'                   => 'x',
            'eligible'                  => 'ready',
            'paid'                      => 'check',
            'incomplete'                => 'x',
            'complete'                  => 'check',
            'succeeded'                 => 'check',
            'cancellation_requested'    => 'x',
            'canceled'                  => 'x',
            'in_progress'               => 'ready',
            'unpaid'                    => 'ready',
        ];
        
        // Return icon if match or value if no matching array key
        return isset($icons[$value]) ? buddyc_admin_icon($icons[$value]) . ' ' . self::uc_format( $value ) : self::uc_format( $value );
    }

    /**
     * Generates status for BookingIntent.
     * 
     * @since 1.0.20
     */
    protected static function booking_intent_status( $property, $value, $item_id ) {
        $icon = self::icons( $property, $value );
        $update = self::update_booking_intent_status( $property, $value, $item_id );
        return '<span class="buddyc-admin-booking-status">' . $icon . $update . '</span>';        
    }

    /**
     * Generates a button to update the status of a BookingIntent.
     * 
     * @since 1.0.20
     */
    protected static function update_booking_intent_status( $property, $value, $item_id ) {
        $values = [
            'unpaid'    => 'succeeded',
            'succeeded' => 'unpaid'
        ];

        if ( ! isset( $values[$value] ) ) {
            return '';
        }

        // Build update url
        $update_url = admin_url( 'admin.php?page=buddyc-dashboard&action=update_booking&booking_id=' . $item_id . '&booking_property=status&booking_value=' . $values[$value] );

        // Define confirmation message
        $message = sprintf(
            /* translators: %s: the new status */
            __( 'Are you sure you want to update this booking to %s?', 'buddyclients-free' ),
            strtoupper( $values[$value] )
        );

        // Output button
        $title = __( 'Edit status', 'buddyclients-free' );
        $edit_icon = buddyc_icon( 'edit' );
        $update_button = '<span class="buddyc-update-booking-icon"><a href="#" title="' . $title . '" onclick="return buddycConfirmAction(\'' . esc_url( $update_url ) . '\', \'' . esc_js( $message ) . '\');">' . $edit_icon . '</a></span>';

        return $update_button;     
    }
    
    /**
     * Formats USD value.
     * 
     * @since 0.1.0
     */
    protected static function usd( $property, $value ) {
        return '$' . number_format((float)$value, 2, '.', '');
    }
    
    /**
     * Generates check icon from bool value.
     * 
     * @since 0.1.0
     */
    protected static function check( $property, $value ) {
        return $value ? buddyc_admin_icon( 'check' ) : ' ';
    }
    
    /**
     * Generates a delete button.
     * 
     * @since 0.2.4
     */
    protected static function delete( $property, $value ) {
        // Build delete url
        $delete_url = admin_url( 'admin.php?page=buddyc-dashboard&action=delete_booking&booking_id=' . $value );

        // Define confirmation message
        $message = __( 'Are you sure you want to delete this booking? This action cannot be undone.', 'buddyclients-free' );

        // Output button
        $title = __( 'Delete booking', 'buddyclients-free' );
        $delete_button = '<span class="buddyc-update-booking-icon"><a href="#" title="' . $title . '" onclick="return buddycConfirmAction(\'' . esc_url( $delete_url ) . '\', \'' . esc_js( $message ) . '\');"><i class="fa-solid fa-trash"></i></a></span>';

        return $delete_button;
    }

    /**
     * Outputs buttons for all booking intentactions.
     * 
     * @since 0.2.21
     */
    protected static function booking_actions( $property, $value, $item_id ) {
        // Initialize
        $buttons = [];

        // Get BookingIntent
        $booking_intent = BookingIntent::get_booking_intent( $item_id );

        // Email        
        $icon = '<i class="fa-solid fa-envelope"></i>';
        $email = $booking_intent->client_email;
        $email_link = ! empty( $email ) ? '<a href="mailto:' . $email . '" title="' . $email . '">' . $icon . '</a>' : '';
        $buttons['email'] = $email_link;

        // Download PDF
        if( class_exists( PDF::class ) ) {
            $pdf_id = $booking_intent->terms_pdf;
            $pdf_download = PDF::download_icon( $pdf_id );
            $buttons['pdf'] = $pdf_download;
        }

        // Stripe transaction
        $stripe_payment_id = $booking_intent->payment_intent_id;
        $external = $stripe_payment_id ? self::stripe_transaction_link( 'payment_intent_id', $stripe_payment_id, $item_id ) : '';
        $buttons['stripe'] = $external;

        // Delete
        $delete = self::delete( $property, $value );
        $buttons['delete'] = $delete;

        // Add styling
        $styled_actions = [];
        foreach ( $buttons as $button ) {
            $styled_actions[] = '<span class="buddyc-admin-booking-action">' . $button . '</span>';
        }

        $actions = implode( ' ', $styled_actions );

        return '<div class="buddyc-admin-booking-actions">' . $actions . '</div>';
    }
    
    /**
     * Generates a date range.
     * 
     * @since 0.1.0
     */
    protected static function date_range( $property, $value ) {
        if ( ! is_array( $value ) || empty( $value ) ) {
            return '';
        }
        $start = isset( $value['start'] ) ? date_i18n( 'M d, Y', strtotime( $value['start'] ) ) : '';
        $end = isset( $value['end'] ) ? date_i18n( 'M d, Y', strtotime( $value['end'] ) ) : '';
        return $start . ( $start && $end ? ' - ' : '' ) . $end;
    }
    
    /**
     * Generates a memo field.
     * 
     * @since 0.1.0
     */
    protected static function copy_memo( $property, $value, $item_id ) {
        ob_start();
        $field_id = $property . '-copy-' . $item_id;
        ?>
        <div>
            <p><span id="<?php echo esc_attr( $field_id ) ?>">
            <?php echo esc_html( $value ) ?>
            </span></p>
            <button onclick="buddycCopyToClipboard('<?php echo esc_attr( $field_id ) ?>')" type="button" class="button button-secondary">Copy Memo</button>
            <div class="buddyc-copy-success"></div>
        </div>
        <?php

        return ob_get_clean();
    }
    
    /**
     * Generates a date and time range.
     * 
     * @since 0.1.0
     */
    protected static function date_time_range( $property, $value ) {
        if ( ! is_array( $value ) || empty( $value ) ) {
            return '';
        }
        $start = isset( $value['start'] ) ? date_i18n( 'M d, Y g:i a', strtotime( $value['start'] ) ) : '';
        $end = isset( $value['end'] ) ? date_i18n( 'M d, Y g:i a', strtotime( $value['end'] ) ) : '';
        return $start . ( $start && $end ? ' - ' : '' ) . $end;
    }
    
    /**
     * Formats time as a human-readable string.
     * 
     * @since 0.1.0
     */
    protected static function format_time( $property, $value ) {
        return date_i18n( 'g:i a', strtotime( $value ) );
    }
    
    /**
     * Formats a list of times.
     * 
     * @since 0.1.0
     */
    protected static function format_times( $property, $value ) {
        if ( ! is_array( $value ) || empty( $value ) ) {
            return '';
        }
        return implode( ', ', array_map( [ __CLASS__, 'format_time' ], $value ) );
    }
    
    /**
     * Generates a user profile link.
     * 
     * @since 0.1.0
     */
    protected static function profile_link( $property, $value ) {
        $user = get_user_by( 'ID', $value );
        if ( $user ) {
            return '<a href="' . esc_url( get_edit_user_link( $user->ID ) ) . '">' . esc_html( $user->display_name ) . '</a>';
        }
        return esc_html( $value );
    }
    
    /**
     * Generates group link.
     * 
     * @since 0.1.0
     */
    protected static function group_link( $property, $value ) {
        if ( function_exists( 'bp_group_link' ) ) {
            ob_start();
            bp_group_link( groups_get_group( $value ) );
            return ob_get_clean();
        }
    }
    
    /**
     * Displays the booking payment status.
     * 
     * @since 0.1.0
     */
    protected static function payment_status( $property, $value ) {
        $status = '';
        switch ( $value ) {
            case 'paid':
                $status = __( 'Paid', 'buddyclients-free' );
                break;
            case 'pending':
                $status = __( 'Pending', 'buddyclients-free' );
                break;
            case 'failed':
                $status = __( 'Failed', 'buddyclients-free' );
                break;
        }
        return $status;
    }
    
    /**
     * Formats a meta field as a link.
     * 
     * @since 0.1.0
     */
    protected static function link( $property, $value ) {
        return '<a href="' . esc_url( $value ) . '">' . __( 'View', 'buddyclients-free' ) . '</a>';
    }
    
    /**
     * Retrieves the booking date in a human-readable format.
     * 
     * @since 0.1.0
     */
    protected static function booking_date( $property, $value ) {
        return date_i18n( 'M d, Y', strtotime( $value ) );
    }
    
    /**
     * Formats timestamp to date.
     * 
     * @since 0.1.0
     */
    protected static function date( $property, $value ) {
        if ( $value && $value !== '0000-00-00 00:00:00') {
            return gmdate( 'M j, Y', strtotime( $value ) );
        }
    }

    /**
     * Formats timestamp to date and time.
     * 
     * @since 0.1.21
     */
    protected static function date_time( $property, $value ) {
        if ( $value && $value !== '0000-00-00 00:00:00') {
            return gmdate( 'M j, Y, g:i A', strtotime( $value ) );
        }
    }

    /**
     * Outputs Email details.
     * 
     * @since 1.0.24
     */
    protected static function email_details( $property, $value ) {
        // Get Email object
        $email_id = $value;
        $email = buddyc_get_email( $email_id );

        // Initialize string to return
        $items = '';

        // Define items to include
        $info = [
            'created_at'    => [ // property
                'function'  => 'date_time',
                'label'     => 'Created At'
            ],
            'to_user_id'    => [
                'function'  => 'user_link',
                'label'     => 'To'
            ],
            'to_email'    => [
                'function'  => 'direct',
                'label'     => 'Email'
            ],
            'subject'    => [
                'function'  => 'direct',
                'label'     => 'Subject'
            ]
        ];

        // Make sure an email object was found
        if ( ! empty( $email ) ) {
            // Loop through info items
            foreach ( $info as $property => $data ) {
                $function = $data['function'] ?? null;
                $label = $data['label'] ?? '';

                // Make sure the method exists in this class
                if ( method_exists( self::class, $function ) && property_exists( $email, $property ) ) {
                    // Get the value from the defined method
                    $value = self::$function( $property, $email->{$property} );
                    // Add the item to the string
                    $items .= '<p><span class="buddyc-text-bold">' . $label . '</span>: ' . $value . '</p>';
                }
            }            
        }
        return $items;
    }

    /**
     * Outputs Lead status.
     * 
     * @since 1.0.24
     */
    protected static function lead_status( $property, $value, $item_id ) {
        $statuses = [
            'active'    => [
                'label' => __( 'Active', 'buddyclients-free' ),
                'icon'  => 'ready'
            ],
            'won'       => [
                'label' => __( 'Won', 'buddyclients-free' ),
                'icon'  => 'check'
            ],
            'lost'      => [
                'label' => __( 'Lost', 'buddyclients-free' ),
                'icon'  => 'x'
            ],
            'spam'      => [
                'label' => __( 'Spam', 'buddyclients-free' ),
                'icon'  => 'x'
            ],
        ];

        // Exit if no status
        if( empty( $value ) ) {
            return;
        }

        // Edit ID
        $div_id = 'buddyc-lead-status-' . $item_id;

        // Hover title
        $title = __( 'Edit status', 'buddyclients-free' );

        // Icon
        $content = buddyc_icon( $statuses[$value]['icon'] );

        // Open edit link
        $content .= '<a href="#" title="' . esc_attr($title) . '" onclick="buddycShowElement(\'' . esc_js($div_id) . '\')">';

        // Status label
        $content .= isset( $statuses[$value] ) ? $statuses[$value]['label'] : uc_first( $value );

        // Close link
        $content .= '</a>';

        // Update status form
        $content .= '<div id="' . esc_attr( $div_id ) . '" class="buddyc-hidden">';
        $content .= self::lead_status_form( $property, $value, $item_id );
        $content .= '</div>';

        return $content;
    }

    /**
     * Generates Lead status form.
     * 
     * @since 1.0.24
     */
    protected static function lead_status_form( $property, $value, $item_id ) {
        if ( class_exists( LeadStatusForm::class ) ) {
            $values = ['status' => $value, 'lead_id' => $item_id];
            return ( new LeadStatusForm )->build( $values );
        }
    }

    /**
     * Outputs the status of the Lead auto email.
     * 
     * @since 1.0.24
     */
    protected static function lead_auto_email( $property, $value, $item_id ) {
        return $value ? buddyc_icon( 'check' ) : buddyc_icon( 'x' );
    }

    /**
     * Outputs the user's payment preference.
     * 
     * @since 1.0.25
     */
    protected static function legal_payment_preference( $property, $value, $item_id ) {
        $user_id = $value;
        $payment = Payment::get_payment( $item_id );
        $type = $payment->type;
        return buddyc_get_user_payment_method_human( $user_id, $type );
    }
}
