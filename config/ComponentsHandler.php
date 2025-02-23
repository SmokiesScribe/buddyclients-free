<?php 

namespace BuddyClients\Config;

use BuddyClients\Includes\DatabaseManager;
use BuddyClients\Admin\AdminNotice;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Class ComponentsHandler
 * Handles component management, encryption, and dependencies.
 */
class ComponentsHandler {
    private static $database = null;
    private static $encryption_key = 'mellons';
    private $components;

    /**
     * Initialize the database connection if not already set.
     */
    private static function init_database() {
        if (!self::$database) {
            self::$database = new DatabaseManager('components');
        }
    }

    /**
     * ComponentsHandler constructor.
     */
    public function __construct() {
        self::init_database();
    }

    /**
     * Retrieve and decrypt stored components.
     *
     * @return array Decrypted components list.
     */
    public static function get_components() {
        $decrypted_components = [];
        self::init_database();

        $record = self::$database->get_record_by_id(1);
        if ($record) {
            $encrypted_components = $record->components;
            $decrypted_components = self::decrypt($encrypted_components);
        }

        return is_array($decrypted_components) ? $decrypted_components : [];
    }

    /**
     * List of required components.
     *
     * @return array
     */
    public static function required_components() {
        return ['Booking', 'Checkout', 'Service'];
    }

    /**
     * List of components with dependencies.
     *
     * @return array
     */
    public static function dependent_components() {
        return ['Affiliate' => 'Legal'];
    }

    /**
     * Update and store encrypted components in the database.
     *
     * @param array $components List of components.
     */
    public static function update_components($components) {
        self::init_database();

        if (!$components) {
            $components = [];
        }

        // Ensure required components are included
        $required_components = self::required_components();
        foreach ($required_components as $required_component) {
            if (!in_array($required_component, $components)) {
                $components[] = $required_component;
            }
        }

        // Encrypt and store in the database
        $record = self::$database->get_record_by_id(1);
        $encrypted_components = self::encrypt($components);

        if ($record) {
            self::$database->update_record(1, ['components' => $encrypted_components]);
        } else {
            self::$database->insert_record(['components' => $encrypted_components]);
        }
    }

    /**
     * Check if a component is active and meets dependency requirements.
     *
     * @param string $component Component name.
     * @return bool True if active, false otherwise.
     */
    public static function in_components($component) {
        $components = self::get_components();

        if (!in_array($component, $components)) {
            return false;
        }

        $enabled_components = buddyc_get_setting('components', 'components');
        if (!is_array($enabled_components) || !in_array($component, $enabled_components)) {
            return false;
        }

        // Check dependencies
        $dependent_components = self::dependent_components();
        if (isset($dependent_components[$component])) {
            $necessary_component = $dependent_components[$component];
            if (!in_array($necessary_component, $enabled_components)) {
                $notice_args = [
                    'repair_link' => 'admin.php?page=buddyc-components-settings',
                    'message' => "The $component component requires the $necessary_component component to be enabled.",
                    'color' => 'orange'
                ];
                new AdminNotice($notice_args);
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a component exists in the database.
     *
     * @param string $component Component name.
     * @return bool True if exists, false otherwise.
     */
    public static function component_exists($component) {
        return in_array($component, self::get_components());
    }

    /**
     * Encrypt data using AES-256-CBC.
     *
     * @param mixed $data Data to encrypt.
     * @return string Encrypted data in base64 format.
     */
    private static function encrypt($data) {
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted_data = openssl_encrypt(serialize($data), 'AES-256-CBC', self::$encryption_key, 0, $iv);
        return base64_encode($iv . $encrypted_data);
    }

    /**
     * Decrypt data using AES-256-CBC.
     *
     * @param string $data Base64 encoded encrypted data.
     * @return mixed Decrypted data.
     */
    private static function decrypt($data) {
        $data = base64_decode($data);
        $iv = substr($data, 0, 16);
        $encrypted_data = substr($data, 16);
        return unserialize(openssl_decrypt($encrypted_data, 'AES-256-CBC', self::$encryption_key, 0, $iv));
    }
}
