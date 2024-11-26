<?php
namespace BuddyClients\Admin;

use BuddyClients\Components\Booking\BookingIntent;
use BuddyClients\Admin\AdminChart;

/**
 * Generates the admin dashboard content.
 * 
 * @since 1.0.2
 */
class AdminDashboard {
    
    /**
     * BookingIntents filtered by date range.
     * 
     * @var array
     */
    private $booking_intents;
    
    /**
     * Data for booking intents in selected date range.
     * 
     * @var array
     */
    private $booking_data;
    
    /**
     * The date range data to filter by.
     * 
     * @var array
     */
    private $date_range_data;
    
    /**
     * An array of key metrics.
     * 
     * @var array
     */
    private $key_metrics;
    
    /**
     * Constructor method.
     * 
     * @since 1.0.2
     */
    public function __construct() {
        $this->get_data();
        $this->build();
    }
    
    /**
     * Retrieves necessary data.
     * 
     * @since 1.0.2
     */
    private function get_data() {
        $this->date_range_data  = $this->date_range_data();
        $this->booking_intents  = $this->get_booking_intents();
        $this->booking_data     = $this->get_booking_data();
        $this->key_metrics      = $this->get_key_metrics();
    }
    
    /**
     * Builds the dashboard content.
     * 
     * @since 1.0.2
     */
    private function build() {
        // Initialize
        $content = '';
        
        // Open wrap
        $content .= '<div class="wrap">';
        $content .= '<h1>' . __( 'Dashboard', 'buddyclients' ) . '</h1>';
        
        // Build items
        $content .= $this->filter_form();
        $content .= $this->overview_table();
        $content .= $this->revenue_chart();
        $content .= $this->key_metric( __( 'Average Booking Value', 'buddyclients' ), 'average_value' );
        $content .= $this->charts_row();
        
        // Close wrap
        $content .= '</div>';
        
        // Merge allowed html arrays
        $allowed_html = $this->allowed_html();
        
        // Escape and output content
        echo wp_kses( $content, $allowed_html );
    }

    /**
     * Defines the allowed html tags.
     * 
     * @since 1.0.17
     */
    private function allowed_html() {
        // Defined allowed html tags
        $allowed_html = buddyc_allowed_html_form();

        // Define tags for chart html
        $chart_html = [
            'div' => ['class' => true],
            'h3' => [],
            'canvas' => ['id' => true, 'class' => true],
            'script' => [],
        ];
        
        // Merge allowed html arrays
        return array_merge( $allowed_html, $chart_html );
    }
    
    /**
     * Defines the date range to filter by.
     * 
     * @since 1.0.2
     */
    private function define_date_range() {
        return buddyc_get_param( 'date_range_filter' ) ?? 'year_to_date';
    }
    
    /**
     * Retrieves and filters booking objects.
     * 
     * @since 1.0.2
     */
    private function get_booking_intents() {
        
        // Define date range data
        $dates = $this->date_range_data;
        
        // Get all booking intents
        $booking_intents = BookingIntent::get_all_booking_intents();
        
        // Initialize
        $matching_booking_intents = [];
        
        // Loop through BookingIntents
        if ( $booking_intents ) {
            foreach ( $booking_intents as $booking ) {
                // Make sure it's within range
                if ( $this->within_date_range( $booking->created_at ) ) {
                    // Add to array
                    $matching_booking_intents[] = $booking;
                }
            }
        }

        // Return filtered items
        return $matching_booking_intents;
    }
    
    /**
     * Builds the array of booking data.
     * 
     * @since 1.0.2
     */
    private function get_booking_data() {
        
        // Get filtered booking intents
        $booking_intents = $this->booking_intents;
    
        return [
            'completed' => [
                'ID'        => 'completed',
                'status'    => __( 'Completed', 'buddyclients' ),
                'count'     => $this->filter_objects( $booking_intents, 'status', 'succeeded' ),
                'total'     => $this->sum_property( $booking_intents, 'total_fee', 'status', 'succeeded' ),
                'net'       => $this->sum_property( $booking_intents, 'net_fee', 'status', 'succeeded' ),
            ],
            'abandoned' => [
                'ID'        => 'abandoned',
                'status'    => __( 'Abandoned', 'buddyclients' ),
                'count'     => $this->filter_objects( $booking_intents, 'status', 'incomplete' ),
                'total'     => $this->sum_property( $booking_intents, 'total_fee', 'status', 'incomplete' ),
                'net'       => $this->sum_property( $booking_intents, 'net_fee', 'status', 'incomplete' ),
            ]
        ];
    }
    
    /**
     * Builds an array of key metrics
     * 
     * @since 1.0.2
     */
    private function get_key_metrics() {
        return [
            'conversion_rate'       => $this->calculate_conversion_rate(),
            'average_value'         => $this->calculate_average_value(),
        ];
    }
    
    /**
     * Calculates the conversion rate.
     * 
     * @since 1.0.2
     */
    private function calculate_conversion_rate() {
        // Get bookings data
        $completed_data = $this->booking_data['completed'];
        $abandoned_data = $this->booking_data['abandoned'];
        
        // Calculate conversion rate
        $total_bookings = $completed_data['count'] + $abandoned_data['count'];
        $conversion_rate = ( $total_bookings > 0 ) ? ( $completed_data['count'] / $total_bookings ) * 100 : 0;
        
        // Round to the nearest whole number
        return round( $conversion_rate ) . '%';
    }
    
    /**
     * Calculates the average completed booking value.
     * 
     * @since 1.0.2
     */
    private function calculate_average_value() {
        // Get bookings data
        $completed_data = $this->booking_data['completed'];

        // Calculate average value
        $value = ( $completed_data['count'] > 0 ) ? ( $completed_data['total'] / $completed_data['count']) : 0;
        
        return '$' . round( $value, 2 );
    }
    
    /**
     * Sums a property in an array of objects.
     * 
     * @since 1.0.2
     * 
     * @param   $objects            array       The array of objects.
     * @param   $property           string      The name of the property whose values to sum.
     * @param   $filter_property    string      Optional. The name of the property to filter by.
     * @param   $filter_value       mixed       Optional. The value of the property to filter by.
     */
    private function sum_property( $objects, $property, $filter_property = null, $filter_value = null ) {
        if ( empty( $objects ) || empty( $property ) ) {
            return 0;
        }
    
        $sum = array_reduce( $objects, function( $carry, $item ) use ( $property, $filter_property, $filter_value ) {
            // Check if the property exists in the item
            if ( !property_exists( $item, $property ) ) {
                return $carry;
            }
    
            // Check for filter
            if ( $filter_property && $filter_value !== null ) {
                if ( property_exists( $item, $filter_property ) && $item->{$filter_property} === $filter_value ) {
                    $carry += $item->{$property} ?? 0;
                }
            } else {
                $carry += $item->{$property} ?? 0;
            }
            
            return $carry;
        }, 0);
    
        return $sum;
    }
    
    /**
     * Filters and counts objects by property.
     * 
     * @since 1.0.2
     * 
     * @param   $objects            array       The array of objects.
     * @param   $property           string      The name of the property whose values to sum.
     * @param   $value              mixed       The value of the property to filter by.
     */
    private function filter_objects( $objects, $property, $value ) {
        if ( empty( $objects ) ) {
            return 0;
        }
        $count = count( array_filter( $objects, function( $item ) use ( $property, $value ) {
            return $item->{$property} === $value;
        }));
        
        return $count;
    }
    
    /**
     * Checks whether a date is within the selected range.
     * 
     * @since 1.0.2
     */
    private function within_date_range( $date ) {
        // Check if date range data is properly set
        if ( !isset( $this->date_range_data['start_date'] ) || !isset( $this->date_range_data['end_date'] ) ) {
            // Handle error or default behavior
            return false;
        }
    
        // Format input date
        $formatted_date = gmdate( 'Y-m-d', strtotime( $date ) );
    
        // Extract start and end dates
        $start_date = $this->date_range_data['start_date'];
        $end_date = $this->date_range_data['end_date'];
    
        // Ensure start and end dates are formatted correctly
        $start_date = gmdate( 'Y-m-d', strtotime( $start_date ) );
        $end_date = gmdate( 'Y-m-d', strtotime( $end_date ) );
    
        // Check if date is within the range
        return ( $formatted_date >= $start_date && $formatted_date <= $end_date );
    }
    
    /**
     * Builds an array of date range data based on the selection.
     * 
     * @since 1.0.2
     */
    private function date_range_data() {
        $date_range = $this->define_date_range();
        switch ( $date_range ) {
            case 'year_to_date':
                $start_date = gmdate('Y-01-01');
                $end_date = gmdate('Y-m-d');
                break;
            case 'month_to_date':
                $start_date = gmdate('Y-m-01');
                $end_date = gmdate('Y-m-d');
                break;
            case 'last_30_days':
                $start_date = gmdate('Y-m-d', strtotime('-30 days'));
                $end_date = gmdate('Y-m-d');
                break;
            case 'last_365_days':
                $start_date = gmdate('Y-m-d', strtotime('-365 days'));
                $end_date = gmdate('Y-m-d');
                break;
            case 'today_only':
                $start_date = gmdate('Y-m-d');
                $end_date = gmdate('Y-m-d');
                break;
            case 'yesterday_only':
                $start_date = gmdate('Y-m-d', strtotime('-1 day'));
                $end_date = gmdate('Y-m-d', strtotime('-1 day'));
                break;
            default:
                $start_date = '';
                $end_date = '';
                break;
        }
        
        return ['start_date' => $start_date, 'end_date' => $end_date];
    }
    
    /**
     * Builds the filter form.
     * 
     * @since 1.0.2
     */
    private function filter_form() {
        // Initialize
        $content = '';

        // Start building the filter form
        $content .= '<form method="POST" style="margin-bottom: 20px;">';
        
        // Build the filter name
        $name = 'date_range_filter';
        
        // Filter label
        $content .= '<label for="' . $name . '">';
        $content .= esc_html__( 'Date Range ', 'buddyclients-free');
        $content .= '</label>';
        
        // Build the dropdown
        $content .= '<select name="' .  esc_attr( $name ) . '" id="' . esc_attr( $name ) . '" style="margin-right: 5px;">';
        
        // Loop through the options
        foreach ( $this->filter_options() as $option_key => $option_label ) {
            $date_range = buddyc_get_param( $name );
            $content .= '<option value="' . esc_attr( $option_key ) . '"' . ( ( $date_range == $option_key ) ? ' selected' : '' ) . '>' . esc_html( $option_label ) . '</option>';
        }
    
        // Close the dropdown
        $content .= '</select>';
        
        // Submission verification field
        $content .= '<input type="hidden" name="buddyc_admin_filter_key" value="buddyc_overview">';
        
        // Submit button
        $content .= '<button type="submit" class="button action" name="buddyc_overview_filter_submit">';
        $content .= esc_html__( 'Filter', 'buddyclients' );
        $content .= '</button>';
        
        // Close the form
        $content .= '</form>';

        return $content;
    }

    /**
     * Defines the filter options.
     * 
     * @since 1.0.4
     */
    private function filter_options() {
        return [
            'year_to_date'      => __( 'Year to Date', 'buddyclients' ),
            'month_to_date'     => __( 'Month to Date', 'buddyclients' ),
            'last_30_days'      => __( 'Last 30 Days', 'buddyclients' ),
            'last_365_days'     => __( 'Last 365 Days', 'buddyclients' ),
            'today_only'        => __( 'Today Only', 'buddyclients' ),
            'yesterday_only'    => __( 'Yesterday Only', 'buddyclients' ),
        ];
    }
    
    /**
     * Builds the overview table.
     * 
     * @since 1.0.2
     */
    private function overview_table() {
        // Initialize
        $content = '';
        
        // Define column headers for the overview table
        $headers = [
            __( 'Status', 'buddyclients' ),
            __( 'Count', 'buddyclients' ),
            __( 'Total', 'buddyclients' ),
            __( 'Net', 'buddyclients' )
        ];
        
        // Start building the content
        $content .= '<!-- Overview Table -->';
        $content .= '<table class="wp-list-table widefat striped booking-dashboard-table">';
        $content .= '<thead>';
        $content .= '<tr>';
        
        // Add column headers
        foreach ( $headers as $title ) {
            $content .= '<th scope="col">' . esc_html( $title ) . '</th>';
        }
        
        $content .= '</tr>';
        $content .= '</thead>';
        $content .= '<tbody>';
        
        // Add rows of booking data
        foreach ( $this->booking_data as $data ) {
            $content .= '<tr>';
            $content .= '<td class="booking-dashboard-column-title">' . esc_html( $data['status'] ) . '</td>';
            $content .= '<td><span class="buddyc-dashboard-item">' . esc_html( $data['count'] ) . '</span></td>';
            $content .= '<td><span class="buddyc-dashboard-item">' . esc_html( '$' . number_format( $data['total'], 2 ) ) . '</span></td>';
            $content .= '<td><span class="buddyc-dashboard-item">' . esc_html( '$' . number_format( $data['net'], 2 ) ) . '</span></td>';
            $content .= '</tr>';
        }
        
        $content .= '</tbody>';
        $content .= '</table>';

        // Return the collected content
        return $content;
    }
    
    /**
     * Outputs a key metric item.
     * 
     * @since 1.0.2
     * 
     * @param   $label  string  The metric label.
     * @param   $key    string  The metric key.
     */
    private function key_metric( $label, $key ) {
        $value = $this->key_metrics[$key] ?? 'N/A';
        $content = '<div class="buddyc-dashboard-key-metric">';
        $content .= '<span>' . $label . ': ' . $value . '</span>';
        $content .= '</div>';
        return $content;
    }
    
    /**
     * Outputs the revenue chart.
     * 
     * @since 1.0.2
     */
    private function revenue_chart() {
        $revenue_data = $this->revenue_chart_data();
        
        // Define the data and options for the chart
        $data = [
            'labels' => $revenue_data['labels'],
            'datasets' => [
                [
                    'label' => 'Gross Revenue',
                    'data' => $revenue_data['revenue'],
                    'borderColor' => 'rgba(3, 122, 173, 1)',
                    'backgroundColor' => 'rgba(3, 122, 173, 0.2)',
                    'fill' => true,
                ],
                [
                    'label' => 'Net Revenue',
                    'data' => $revenue_data['net'],
                    'borderColor' => 'rgba(6, 127, 6, 1)',
                    'backgroundColor' => 'rgba(6, 127, 6, 0.2)',
                    'fill' => true,
                ],
            ]
        ];
        
        $labels = [
            'x_label' => 'Date',
            'y_label' => 'Amount ($)',
        ];
        
        $args = [
            'type'              => 'line',
            'data'              => $data,
            'labels'            => $labels,
            'tooltip_format'    => 'currency',
            'canvas_id'         => 'revenueChart',
            'title'             => __('Revenue Over Time', 'buddyclients-free')
        ];
        
        // Create and display the chart
        $chart = new AdminChart( $args );
        return $chart->render_chart();
    }
    
    /**
     * Retrieves data for the revenue chart.
     * 
     * @since 1.0.2
     */
    private function revenue_chart_data() {
        $booking_intents = $this->booking_intents;
        
        // Initialize arrays to store revenue data
        $revenue_data = [
            'labels' => [],
            'revenue' => [],     // Gross revenue
            'net' => [],         // Net revenue
        ];
        
        // Initialize arrays to track revenue by date
        $daily_revenue = [];
        $daily_net = [];
    
        if ( $booking_intents ) {
            foreach ( $booking_intents as $booking_intent ) {
                $date = gmdate( 'Y-m-d', strtotime( $booking_intent->created_at ) );
                $formatted_date = gmdate( 'M j, Y', strtotime( $date ) ); // Human-readable date
                $gross_revenue = $booking_intent->total_fee ?? 0;
                $net_revenue = $booking_intent->net_fee ?? 0;
                
                // Aggregate revenue by date
                $daily_revenue[$formatted_date] = ($daily_revenue[$formatted_date] ?? 0) + $gross_revenue;
                $daily_net[$formatted_date] = ($daily_net[$formatted_date] ?? 0) + $net_revenue;
            }
        }
        
        // Sort the revenue by date
        ksort( $daily_revenue );
        ksort( $daily_net );
    
        // Populate the chart data
        foreach ( $daily_revenue as $date => $revenue ) {
            $revenue_data['labels'][] = $date;
            $revenue_data['revenue'][] = $revenue;
            $revenue_data['net'][] = $daily_net[$date] ?? 0; // Ensure net revenue is set
        }
        
        return $revenue_data;
    }
    
    /**
     * Outputs the abandoned bookings chart.
     * 
     * @since 1.0.2
     */
    private function abandonment_chart() {
        $revenue_data = $this->abandonment_chart_data();
        
        // Define the data and options for the chart
        $data = [
            'labels' => $revenue_data['labels'],
            'datasets' => [
                [
                    'label' => 'Completed Bookings',
                    'data' => $revenue_data['completed'],
                    'borderColor' => 'rgba(3, 122, 173, 1)',
                    'backgroundColor' => 'rgba(3, 122, 173, 0.2)',
                    'fill' => true,
                ],
                [
                    'label' => 'Abandoned Bookings',
                    'data' => $revenue_data['abandoned'],
                    'borderColor' => 'rgba(6, 127, 6, 1)',
                    'backgroundColor' => 'rgba(6, 127, 6, 0.2)',
                    'fill' => true,
                ],
            ]
        ];
        
        $labels = [
            'x_label' => 'Date',
            'y_label' => 'Count',
        ];
        
        $args = [
            'type'              => 'line',
            'data'              => $data,
            'labels'            => $labels,
            'canvas_id'         => 'abandonmentChart',
            'title'             => __('Booking Success Over Time', 'buddyclients-free')
        ];
        
        // Create and display the chart
        $chart = new AdminChart( $args );
        return $chart->render_chart();
    }
    
    /**
     * Retrieves data for completed and abandoned bookings over time.
     * 
     * @since 1.0.2
     * 
     * @return array An array containing labels, completed bookings, and abandoned bookings data.
     */
    private function abandonment_chart_data() {
        $booking_intents = $this->booking_intents;
    
        // Initialize arrays to store booking data
        $chart_data = [
            'labels' => [],
            'completed' => [], // Completed bookings
            'abandoned' => []  // Abandoned bookings
        ];
    
        // Initialize arrays to track completed and abandoned bookings by date
        $completed_bookings = [];
        $abandoned_bookings = [];
    
        if ( $booking_intents ) {
            foreach ( $booking_intents as $booking_intent ) {
                $date = gmdate( 'Y-m-d', strtotime( $booking_intent->created_at ) );
                
                // Use a human-readable date format for labels
                $formatted_date = gmdate( 'M j, Y', strtotime( $date ));
    
                // Track the number of completed and abandoned bookings
                if ( $booking_intent->status === 'succeeded' ) {
                    if ( isset( $completed_bookings[$formatted_date] ) ) {
                        $completed_bookings[$formatted_date]++;
                    } else {
                        $completed_bookings[$formatted_date] = 1;
                    }
                } elseif ( $booking_intent->status === 'incomplete' ) {
                    if ( isset( $abandoned_bookings[$formatted_date] ) ) {
                        $abandoned_bookings[$formatted_date]++;
                    } else {
                        $abandoned_bookings[$formatted_date] = 1;
                    }
                }
            }
        }
    
        // Merge and sort the dates
        $dates = array_merge( array_keys( $completed_bookings ), array_keys( $abandoned_bookings ) );
        $dates = array_unique( $dates );
        sort( $dates );
    
        // Populate the data arrays
        foreach ( $dates as $date ) {
            $chart_data['labels'][] = $date;
            $chart_data['completed'][] = isset($completed_bookings[$date]) ? $completed_bookings[$date] : 0;
            $chart_data['abandoned'][] = isset($abandoned_bookings[$date]) ? $abandoned_bookings[$date] : 0;
        }
    
        return $chart_data;
    }
    
    /**
     * Outputs the row with two charts.
     * 
     * @since 1.0.2
     */
    private function charts_row() {
        // Initialize content
        $content = '';

        // Start building the content
        $content .= '<div class="charts-row">';
        $content .= '<div class="chart-container pie">';

        // Add the booking success pie chart and key metric
        $content .= $this->booking_success_pie_chart();
        $content .= $this->key_metric( __( 'Conversion Rate', 'buddyclients' ), 'conversion_rate' );
        
        $content .= '</div>'; // Close pie chart container

        // Add the abandonment chart
        $content .= '<div class="chart-container">';
        $content .= $this->abandonment_chart();
        $content .= '</div>'; // Close chart container

        $content .= '</div>'; // Close charts row

        // Return the collected content
        return $content;
    }
    
    /**
     * Outputs the booking status pie chart.
     * 
     * @since 1.0.2
     */
    private function booking_success_pie_chart() {
        $data = $this->booking_success_pie_chart_data();
        
        $chart_data = [
            'labels' => $data['labels'],
            'datasets' => [
                [
                    'label' => 'Booking Status',
                    'data' => $data['counts'],
                    'backgroundColor' => [
                        'rgba(3, 122, 173, 0.2)',
                        'rgba(6, 127, 6, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    'borderColor' => [
                        'rgba(3, 122, 173, 1)',
                        'rgba(6, 127, 6, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    'borderWidth' => 2
                ]
            ]
        ];
        
        $args = [
            'type'              => 'pie',
            'data'              => $chart_data,
            'canvas_id'         => 'bookingStatusBreakdownChart',
            'title'             => __('Booking Success', 'buddyclients-free')
        ];
        
        $chart = new AdminChart($args);
        return $chart->render_chart();
    }
    
    /**
     * Retrieves data for the booking status pie chart.
     * 
     * @since 1.0.2
     */
    private function booking_success_pie_chart_data() {
        $booking_intents = $this->booking_intents;
        $data = [
            'labels' => [],
            'counts' => []
        ];
        
        $status_counts = [];
        
        if ($booking_intents) {
            foreach ($booking_intents as $booking_intent) {
                $status = $booking_intent->status;
                
                // Count the number of bookings in each status
                $status_counts[$status] = ($status_counts[$status] ?? 0) + 1;
            }
        }
        
        foreach ($status_counts as $status => $count) {
            $data['labels'][] = ucfirst($status); // Capitalize status names
            $data['counts'][] = $count;
        }
        
        return $data;
    }


}