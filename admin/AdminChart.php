<?php
namespace BuddyClients\Admin;

use BuddyClients\Components\Booking\BookingIntent;

/**
 * Generates a chart for the admin area.
 * 
 * @since 1.0.2
 */
class AdminChart {
    
    /** 
     * The chart type (e.g. line, bar)
     * 
     * @var string
     */
    private $chart_type;

    /**
     * The data to be displayed on the chart.
     * 
     * @var array
     */
    private $data;

    /**
     * Additional labels for the chart.
     * 
     * @var array
     */
    private $labels;

    /**
     * The ID of the canvas element.
     * 
     * @var string
     */
    private $canvas_id;

    /**
     * The title of the chart.
     * 
     * @var string
     */
    private $title;

    /**
     * Constructor to initialize chart settings.
     *
     * @param array $args {
     *     An associative array of chart settings.
     * 
     *     @type    string  $chart_type     The type of chart (e.g., 'line', 'bar').
     *     @type    array   $data           The data to be displayed on the chart.
     *     @type    array   $labels         Additional labels for the chart.
     *     @type    string  $tooltip_format The format for tooltip ('currency' or 'number').
     *     @type    string  $canvas_id      The ID of the canvas element.
     *     @type    string  $title          The title fo the chart.
     * }
     */
    public function __construct( $args = [] ) {
        $this->chart_type = $args['type'] ?? 'line';
        $this->data = $args['data'] ?? [];
        $this->labels = $args['labels'] ?? [];
        $this->tooltip_format = $args['tooltip_format'] ?? 'number';
        $this->canvas_id = $args['canvas_id'] ?? 'chartCanvas';
        $this->title = $args['title'] ?? 'Chart';
    }

    /**
     * Renders the chart HTML and JavaScript.
     */
    public function render_chart() {
        ob_start();
        ?>

        <div class="buddyc-chart <?php echo esc_html( $this->chart_type ); ?>">
        <h3><?php echo esc_html( $this->title ); ?></h3>
        <canvas id="<?php echo esc_attr( $this->canvas_id ); ?>" class="buddyc-canvas"></canvas>
        </div>

        <?php

        $script = $this->chart_script();
        buddyclients_inline_script( $script, $admin = true, $direct = true );
        
        return ob_get_clean();
    }

    /**
     * Defines the chart JavaScript.
     */
    public function chart_script() {
        ob_start();
        ?>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('<?php echo esc_attr( $this->canvas_id ); ?>').getContext('2d');
            
            if (!ctx) {
                console.error('Canvas context not found!');
                return;
            }
            
            const chart = new Chart(ctx, {
                type: <?php echo wp_json_encode($this->chart_type); ?>,
                data: {
                    labels: <?php echo wp_json_encode($this->data['labels']); ?>,
                    datasets: <?php echo wp_json_encode($this->data['datasets']); ?>
                },
                options: {
                    responsive: true,
                    showTooltips: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: <?php echo wp_json_encode($this->labels['x_label'] ?? ''); ?>,
                                color: '#000',
                                font: {
                                    weight: 'bold' // Bold x-axis label
                                }
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: <?php echo wp_json_encode($this->labels['y_label'] ?? ''); ?>,
                                color: '#000',
                                font: {
                                    weight: 'bold' // Bold y-axis label
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.raw !== null) {
                                        if (<?php echo wp_json_encode($this->tooltip_format); ?> === 'currency') {
                                            label += '$' + context.raw.toFixed(2); // Currency format
                                        } else {
                                            label += context.raw.toFixed(0); // Whole number format
                                        }
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        });
        <?php
        return ob_get_clean();
    }
}