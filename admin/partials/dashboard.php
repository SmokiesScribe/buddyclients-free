<?php
use BuddyClients\Components\Booking\BookingIntent;

use BuddyClients\Admin\AdminDashboard;

/**
 * Builds the bookings dashboard content.
 * 
 * @since 0.1.0
 */
function bc_bookings_dashboard() {
    
    new AdminDashboard();
    
    return;
    
    
    
    // Check if a date range is selected
    $selected_date_range = isset($_POST['date_range']) ? sanitize_text_field($_POST['date_range']) : __( 'year_to_date', 'buddyclients' );
    
    // Get the service fee and count for the selected date range and previous period
    $booking_data = get_bookings_service_fee($selected_date_range, $previous_date_range);
    
    // Calculate conversion rate
    $total_bookings = $booking_data['completed_count'] + $booking_data['abandoned_count'];
    $conversion_rate = ($total_bookings > 0) ? ($booking_data['completed_count'] / $total_bookings) * 100 : 0;
    
    // Calculate average booking value
    $average_booking_value = ($booking_data['completed_count'] > 0) ? ($booking_data['completed_total'] / $booking_data['completed_count']) : 0;
        
    // Get the booking data for the selected date range
    $revenue_data = get_bookings_revenue_over_time( $selected_date_range ); // New function for fetching revenue over time

    // Calculate total revenue for the date range
    $total_revenue = array_sum( $revenue_data['revenue'] );

    // Define column headers for the overview table
    $headers = [
        __( 'Status', 'buddyclients' ),
        __( 'Count', 'buddyclients' ),
        __( 'Total', 'buddyclients' ),
        __( 'Net', 'buddyclients' )
    ];

    ?>
    <div class="wrap">
        <h1><?php _e( 'Dashboard', 'buddyclients' ); ?></h1>
        
        <!-- Filter Form -->
        <?php bc_bookings_filter_form( $selected_date_range ); ?>
        
        <?php echo '<h3>' . esc_html( bc_bookings_dashboard_filter_options()[$selected_date_range] ) . '</h3>'; ?>
        
        <!-- KPIs Section -->
        <div class="kpi-section">
            <p><strong><?php _e('Conversion Rate:', 'buddyclients'); ?></strong> <?php echo number_format($conversion_rate, 2) . '%'; ?></p>
            <p><strong><?php _e('Average Booking Value:', 'buddyclients'); ?></strong> <?php echo '$' . number_format($average_booking_value, 2); ?></p>
        </div>

        <!-- KPI Table -->
        <table class="wp-list-table widefat striped booking-dashboard-table">
            <thead>
                <tr>
                    <?php foreach ( $headers as $title ) : ?>
                        <th scope="col"><?php echo esc_html( $title ); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="booking-dashboard-column-title"><?php _e( 'Completed Bookings', 'buddyclients' ); ?></td>
                    <td><span class="bc-dashboard-item"><?php echo esc_html( $booking_data['completed_count'] ); ?></span></td>
                    <td><span class="bc-dashboard-item"><?php echo esc_html( '$' . number_format( $booking_data['completed_total'], 2 ) ); ?></span></td>
                    <td><span class="bc-dashboard-item"><?php echo esc_html( '$' . number_format( $booking_data['completed_net'], 2 ) ); ?></span></td>
                </tr>
                <tr>
                    <td class="booking-dashboard-column-title"><?php _e( 'Abandoned Bookings', 'buddyclients' ); ?></td>
                    <td><span class="bc-dashboard-item"><?php echo esc_html( $booking_data['abandoned_count'] ); ?></span></td>
                    <td><span class="bc-dashboard-item"><?php echo esc_html( '$' . number_format( $booking_data['abandoned_total'], 2 ) ); ?></span></td>
                    <td><span class="bc-dashboard-item"><?php echo esc_html( '$' . number_format( $booking_data['abandoned_net'], 2 ) ); ?></span></td>
                </tr>
            </tbody>
        </table>

        <!-- Revenue Chart -->
        <h3><?php _e( 'Revenue Over Time', 'buddyclients' ); ?></h3>
        <canvas id="revenueChart"></canvas>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('revenueChart').getContext('2d');
                const revenueChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode($revenue_data['labels']); ?>, // Dates
                        datasets: [
                            {
                                label: 'Gross Revenue',
                                data: <?php echo json_encode($revenue_data['revenue']); ?>, // Gross revenue values
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                fill: true,
                            },
                            {
                                label: 'Net Revenue',
                                data: <?php echo json_encode($revenue_data['net']); ?>, // Net revenue values
                                borderColor: 'rgba(255, 99, 132, 1)',  // Red for net revenue
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                fill: true,
                            },
                            {
                                label: 'Completed Total',
                                data: <?php echo json_encode($revenue_data['completed_total']); ?>, // Completed bookings total
                                borderColor: 'rgba(54, 162, 235, 1)',  // Blue for completed total
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                fill: true,
                            }
                        ]
                    },
                    options: {
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Date'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Amount ($)'
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        return '$' + tooltipItem.raw.toFixed(2); // Format values as currency
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    </div>
    <?php
}




/**
 * Defines the filter options.
 * 
 * @since 0.1.0
 */
function bc_bookings_dashboard_filter_options() {
    // Define filter options
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
 * Displays filter form.
 * 
 * @since 0.1.0
 * 
 * @param   string  $selected_date_range The current selected date range.
 */
function bc_bookings_filter_form( $selected_date_range ) {
    ?>
    <form method="post" style="margin-bottom: 20px;">
        <label for="date_range"><?php _e( 'Select Date Range:', 'buddyclients' ); ?></label>
        <select name="date_range" id="date_range">
            <?php foreach ( bc_bookings_dashboard_filter_options() as $key => $label ) : ?>
                <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $selected_date_range, $key ); ?>><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="<?php _e( 'Submit', 'buddyclients' ); ?>" class="button button-secondary">
    </form>
    <?php
}

/**
 * Defines start and end dates for date range keys.
 * 
 * @since 0.1.0
 * 
 * @param   string  $date_range The date range key.
 */
function bc_date_range_dates( $date_range ) {
    
    switch ($date_range) {
        case 'year_to_date':
            $start_date = date('Y-01-01');
            $end_date = date('Y-m-d');
            break;
        case 'month_to_date':
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-d');
            break;
        case 'last_30_days':
            $start_date = date('Y-m-d', strtotime('-30 days'));
            $end_date = date('Y-m-d');
            break;
        case 'last_365_days':
            $start_date = date('Y-m-d', strtotime('-365 days'));
            $end_date = date('Y-m-d');
            break;
        case 'today_only':
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d');
            break;
        case 'yesterday_only':
            $start_date = date('Y-m-d', strtotime('-1 day'));
            $end_date = date('Y-m-d', strtotime('-1 day'));
            break;
        default:
            $start_date = '';
            $end_date = '';
            break;
    }
    
    return ['start_date' => $start_date, 'end_date' => $end_date];
}

/**
 * Retrieves the bookings dashboard data.
 * 
 * @since 0.1.0
 *
 * @param string $date_range The date range for filtering the bookings.
 * @param string|null $compare_date_range Optional. The date range for comparing revenue growth.
 */
function get_bookings_service_fee($date_range = 'year_to_date', $compare_date_range = null) {
    $dates = bc_date_range_dates($date_range);
    
    // Optional: Get the previous date range for revenue growth comparison
    if ($compare_date_range) {
        $compare_dates = bc_date_range_dates($compare_date_range);
    }
    
    // Get all booking intents
    $booking_intents = BookingIntent::get_all_booking_intents();
    
    // Initialize variables
    $completed_count = 0;
    $abandoned_count = 0;
    $completed_total = 0;
    $abandoned_total = 0;
    $completed_net = 0;
    $abandoned_net = 0;
    $service_fee = 0;
    
    $previous_total = 0; // Total for comparison period
    
    // Loop through BookingIntents
        if ( $booking_intents ) {
        foreach ($booking_intents as $booking) {
            $date = date('Y-m-d', strtotime($booking->created_at));
            
            if ($date >= $dates['start_date'] && $date <= $dates['end_date']) {
                $service_fee = $booking->total_fee ?? 0;
                $net_fee = $booking->net_fee ?? 0;
                
                if ($booking->status === 'succeeded') {
                    $completed_count++;
                    $completed_total += $service_fee;
                    $completed_net += $net_fee;
                } else {
                    $abandoned_count++;
                    $abandoned_total += $service_fee;
                    $abandoned_net += $net_fee;
                }
            }

            // Compare revenue for the previous period
            if ($compare_date_range && $date >= $compare_dates['start_date'] && $date <= $compare_dates['end_date']) {
                if ($booking->status === 'succeeded') {
                    $previous_total += $service_fee;
                }
            }
        }
    }

    return [
        'completed_count' => $completed_count,
        'abandoned_count' => $abandoned_count,
        'completed_total' => $completed_total,
        'abandoned_total' => $abandoned_total,
        'completed_net' => $completed_net,
        'abandoned_net' => $abandoned_net,
        'previous_total' => $previous_total, // For revenue growth comparison
    ];
}








/**
 * Enqueues chart javascript.
 * 
 * @since 1.0.2
 */
function bc_enqueue_chart_js() {
    wp_enqueue_script( 'chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true );
}
//add_action( 'admin_enqueue_scripts', 'bc_enqueue_chart_js' );


function get_bookings_revenue_over_time( $date_range ) {
    // Get start and end dates for the date range
    $dates = bc_date_range_dates( $date_range );
    
    // Initialize arrays to store revenue data
    $revenue_data = [
        'labels' => [],
        'revenue' => [],     // Gross revenue
        'net' => [],         // Net revenue
        'completed_total' => [] // Total for completed bookings
    ];

    // Get all booking intents
    $booking_intents = BookingIntent::get_all_booking_intents();
    
    // Initialize arrays to track revenue by date
    $daily_revenue = [];
    $daily_net = [];
    $daily_completed_total = [];

    if ( $booking_intents ) {
        foreach ( $booking_intents as $booking ) {
            $date = date('Y-m-d', strtotime( $booking->created_at ) );
            
            // Check if the date is within the selected range
            if ( $date >= $dates['start_date'] && $date <= $dates['end_date'] ) {
                $gross_revenue = $booking->total_fee ?? 0;
                $net_revenue = $booking->net_fee ?? 0; // Assuming `net_fee` is the net revenue in the booking object
                
                // Track completed total only for completed bookings
                $completed_total = ($booking->status == 'completed') ? $booking->total_fee : 0;
                
                // Add to daily gross revenue
                if ( isset( $daily_revenue[$date] ) ) {
                    $daily_revenue[$date] += $gross_revenue;
                } else {
                    $daily_revenue[$date] = $gross_revenue;
                }
                
                // Add to daily net revenue
                if ( isset( $daily_net[$date] ) ) {
                    $daily_net[$date] += $net_revenue;
                } else {
                    $daily_net[$date] = $net_revenue;
                }

                // Add to daily completed total revenue
                if ( isset( $daily_completed_total[$date] ) ) {
                    $daily_completed_total[$date] += $completed_total;
                } else {
                    $daily_completed_total[$date] = $completed_total;
                }
            }
        }
    }
    
    // Sort the revenue by date
    ksort( $daily_revenue );
    ksort( $daily_net );
    ksort( $daily_completed_total );

    // Populate the chart data
    foreach ( $daily_revenue as $date => $revenue ) {
        $revenue_data['labels'][] = $date;
        $revenue_data['revenue'][] = $revenue; // Gross revenue
        $revenue_data['net'][] = $daily_net[$date]; // Net revenue
        $revenue_data['completed_total'][] = $daily_completed_total[$date]; // Completed bookings total
    }

    return $revenue_data;
}

