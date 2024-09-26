<?php
namespace BuddyClients\Components\Affiliate;

use BuddyClients\Components\Legal\LegalForm as LegalForm;
use BuddyClients\Includes\{
    Subnav as Subnav,
    CommissionList as CommissionList
};

/**
 * Affiliate profile content.
 * 
 * Generates content for the affiliate tab of a user's profile.
 * Content includes the signup form, affiliate agreement,
 * and click and commission data for current affiliates.
 * 
 * @since 0.1.0
 */
class AffiliateProfile {
    
    /**
     * Affiliate instance.
     * 
     * @var Affiliate
     */
    protected $affiliate;
    
    /**
     * User ID.
     * 
     * @var int
     */
    public $user_id;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct( $user_id = null ) {
        $this->user_id = $user_id ?? get_current_user_id();
        $this->affiliate = new Affiliate( $user_id );
    }
    
    /**
     * Builds the content.
     * 
     * @since 0.1.0
     */
    public function build() {
        
        // Initialize
        $content = '';
        
        // Get the curr view
        $view = $this->get_view();
        
        // Add subnav
        $content .= $this->subnav();
        
        // Affiliate link
        if ( $view === 'link' ) {
            $content .= $this->affiliate_link();
        }
        
        // Commission data
        if ( $view === 'commission' ) {
            $content .= $this->commission_data();
        }
        
        // Click data
        if ( $view === 'clicks' ) {
            $content .= $this->click_data();
        }
        
        // Affiliate form
        if ( ! $view || $view === 'form' ) {
            $content .= $this->form();
        }
        
        // Echo content
        echo $content;
    }
    
    /**
     * Checks the subnav param.
     * 
     * @since 0.1.0
     */
    private function get_view() {
        return $_GET['subnav'] ?? null;
    }
    
    /**
     * Creates the navigation.
     * 
     * @since 0.1.0
     */
    private function subnav() {
        $items = [
            'form' => [
                'label' => __( 'Join', 'buddyclients' ),
                'link'  => null
            ],
        ];
        
        $existing_affiliate_items = [
            'link' => [
                'label' => __( 'My Link', 'buddyclients' ),
                'link'  => null
            ],
            'commission' => [
                'label' => __( 'Commission', 'buddyclients' ),
                'link'  => null
            ],
            'clicks' => [
                'label' => __( 'Clicks', 'buddyclients' ),
                'link'  => null
            ],
        ];
        
        // Add links for existing affiliates
        if ( $this->affiliate->user_data['status'] ) {
            $items = array_merge( $items, $existing_affiliate_items );
        }
    
        return ( new Subnav )->build( $items );
    }
    
    /**
     * Creates the form content.
     * 
     * @since 0.1.0
     */
    private function form() {
        $content = ( new LegalForm( 'affiliate', ['email', 'payment'] ) )->build();
        return $content;
    }
    
    /**
     * Displays commission data.
     * 
     * @since 0.1.0
     */
    private function commission_data() {
        $content = '<h3>' . __( 'Commission Payments', 'buddyclients' ) . '</h3>';
        $content .= ( new CommissionList( 'affiliate' ) )->build( $this->affiliate->ID );
        return $content;
        
    }
    
    /**
     * Displays affiliate link
     * 
     * @since 0.1.0
     */
    private function affiliate_link() {
        
        // Add copy paste link for existing affiliates
        if ( $this->affiliate->user_data['status'] ) {
        
            $content = '<h3>' . __( 'Your Affiliate Link', 'buddyclients' ) . '</h3>';
            $content .= '<p>' . __( 'Share the link below to earn commission.', 'buddyclients' ) . '</p>';
            
            // Copy paste affiliate link
            $affiliate_url = $this->affiliate->affiliate_link();
            $content .= bc_copy_to_clipboard( $affiliate_url, 'bc_affiliate_link' );
            
            return $content;
        }
    }
    
    /**
     * Displays click data.
     * 
     * @since 0.1.0
     */
    private function click_data() {
        ob_start();
        
        $ref_users_count = get_user_meta($this->user_id, 'ref_users_count', true);
        $ref_users_count = $ref_users_count ? $ref_users_count : 0;
        $click_data = get_user_meta($this->user_id, 'bc_affiliate_clicks', true);
        
        $affiliate = new Affiliate( $this->user_id );
        $ref_users_count = $affiliate->ref_users_count();
        
        $current_date = date('Y-m-d');
        $one_year_ago = date('Y-m-d', strtotime('-1 year', strtotime($current_date)));
        
        echo '<h3>' . __( 'Your Data', 'buddyclients' ) . '</h3>';
        echo '<p>' . __( 'Total Clients Referred: ', 'buddyclients' ) . $ref_users_count . '</p>';
        
        if ($click_data) {
            echo '<table>';
            echo '<tr><th>' . __( 'Month', 'buddyclients' ) . '</th><th>' . __( 'Clicks', 'buddyclients' ) . '</th></tr>';
            
            // Initialize an array to store aggregated click counts per month
            $monthly_clicks = array();
            
            // Iterate over $click_data to aggregate clicks by month
            foreach ($click_data as $date => $clicks) {
                $month_year = date("Y-m", strtotime($date));
                if ($date < $one_year_ago) {
                    continue;
                }
                if (!isset($monthly_clicks[$month_year])) {
                    $monthly_clicks[$month_year] = 0;
                }
                $monthly_clicks[$month_year] += $clicks;
            }
            
            // Iterate over $monthly_clicks to generate table rows
            foreach ($monthly_clicks as $month_year => $clicks) {
                echo '<tr>';
                echo '<td>' . date("F Y", strtotime($month_year)) . '</td>'; // Format month and year
                echo '<td>' . $clicks . '</td>';
                echo '</tr>';
            }
            
            echo '</table>';
        } else {
            echo '<p>' . __( 'No clicks yet. Share your affiliate link to earn commission.', 'buddyclients' ) . '</p>';
        }
        return ob_get_clean();
    }
}