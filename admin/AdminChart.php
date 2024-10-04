<?php
namespace BuddyClients\Admin;

use BuddyClients\Components\Booking\BookingIntent;

/**
 * Generates a chart for the admin area.
 * 
 * @since 1.0.2
 */
class AdminChart {
    
    private $chart_type;
    private $data;
    private $labels;
    private $canvas_id;
    private $title;

    /**
     * Constructor to initialize chart settings.
     *
     * @param array $args Associative array containing chart settings.
     *                    - 'chart_type': The type of chart (e.g., 'line', 'bar').
     *                    - 'data': The data to be displayed on the chart.
     *                    - 'labels': Additional labels for the chart.
     *                    - 'tooltip_format': Format for tooltip ('currency' or 'number').
     *                    - 'canvas_id': The ID of the canvas element.
     *                    - 'title': The title of the chart.
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
        ?>
        <div class="bc-chart <?php echo esc_html( $this->type ); ?>">
        <h3><?php echo esc_html( $this->title ); ?></h3>
        <canvas id="<?php echo esc_attr( $this->canvas_id ); ?>" class="bc-canvas"></canvas>
        </div>
        
        <script>
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
        </script>
        <?php
    }

    /**
     * Displays the chart by rendering it.
     */
    public function display_chart() {
        $this->render_chart();
    }
}
