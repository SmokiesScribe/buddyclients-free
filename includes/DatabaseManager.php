<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Interacts with custom databases.
 * 
 * Defines table structures. Handles table creation, updates, and deletion.
 * Retrieves, updates, and deletes data within tables.
 * 
 * @since 0.1.0
 * @since 1.0.17 Update the table structure when necessary.
 */
class DatabaseManager {

    /**
     * The table key.
     * 
     * For classes, the class name without namespace.
     *
     * @var string
     */
    private $table_key;
    
    /**
     * The name of the table.
     *
     * @var string
     */
    private $table_name;

    /**
     * The expected table structure.
     *
     * @var array
     */
    private $table_structure;
    
    /**
     * Defines the custom table structures.
     * 
     * @since 0.1.0
     * 
     * @return  array   An array of table data.
     */
    private static function define_tables() {
        $tables = [
            'File' => [
                'ID'            => 'INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
                'File'          => 'TEXT',
                'temporary'     => 'BOOLEAN',
                'file_path'     => 'VARCHAR(255)',
                'file_name'     => 'VARCHAR(255)',
                'user_id'       => 'INT(11) NOT NULL',
                'project_id'    => 'INT(11) NOT NULL',
                'created_at'    => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ],
            'Email' => [
                'ID'            => 'INT AUTO_INCREMENT PRIMARY KEY',
                'Email'         => 'TEXT',
                'to_user_id'    => 'INT(11)',
                'to_email'      => 'VARCHAR(255)',
                'subject'       => 'VARCHAR(255)',
                'content'       => 'TEXT',
                'created_at'    => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ],
            'Xprofile' => [
                'ID'             => 'INT PRIMARY KEY',
                'Xprofile'       => 'TEXT',
                'field_key'      => 'VARCHAR(255)',
                'post_id'        => 'INT',
                'filter_field'   => 'TINYINT(1)',
                'created_at'     => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ],
            'BookingIntent' => [
                'ID'             => 'INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
                'BookingIntent'  => 'TEXT',
                'client_id'      => 'VARCHAR(255)',
                'project_id'     => 'VARCHAR(255)',
                'created_at'     => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ],
            'BookedService' => [
                'ID'                => 'INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
                'BookedService'     => 'TEXT',
                'booking_intent_id' => 'INT',
                'service_id'        => 'INT',
                'team_id'           => 'VARCHAR(255)',
                'client_id'         => 'VARCHAR(255)',
                'created_at'        => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ],
            'Payment' => [
                'ID'                => 'INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
                'Payment'           => 'TEXT',
                'booked_service_id' => 'INT',
                'type'              => 'VARCHAR(255)',
                'status'            => 'VARCHAR(255)',
                'payee_id'          => 'VARCHAR(255)',
                'paid_date'         => 'DATETIME',
                'created_at'        => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ],
            'CancelRequest' => [
                'ID'                => 'INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
                'CancelRequest'     => 'TEXT',
                'booked_service_id' => 'INT',
                'booking_intent_id' => 'INT',
                'reason'            => 'VARCHAR(255)',
                'status'            => 'INT',
                'created_at'        => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ],
            'components' => [
                'ID'                => 'INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
                'components'        => 'TEXT',
                'created_at'        => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ],
            'License' => [
                'ID'                => 'INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
                'License'           => 'TEXT',
                'created_at'        => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ],
            'PDF' => [
                'ID'                => 'INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
                'PDF'               => 'TEXT',
                'user_id'           => 'VARCHAR(255)',
                'file_url'          => 'VARCHAR(255)',
                'created_at'        => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ],
            'UserAgreement' => [
                'ID'                => 'INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
                'UserAgreement'     => 'TEXT',
                'user_id'           => 'VARCHAR(255)',
                'created_at'        => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ],
            'hash' => [
                'ID'                => 'INT(11) AUTO_INCREMENT NOT NULL PRIMARY KEY',
                'key'               => 'TEXT',
                'version'           => 'TEXT',
                'hash'              => 'TEXT',
                'created_at'        => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ]
        ];
        
        /**
         * Filters the database table data.
         *
         * @since 0.3.4
         *
         * @param   array   $tables    The array of database table data.
         */
         $tables = apply_filters( 'buddyc_tables', $tables );

         return $tables;
    }

    /**
     * Constructor method.
     * 
     * Builds the table key and name. Creates or updates
     * the table if necessary.
     *
     * @param   string    $table_key     The unique key for the custom table.
     */
    public function __construct( $table_key ) {
        global $wpdb;
        
        // Build table name
        $this->table_key = $this->clean_table_key( $table_key );
        $this->table_name = $wpdb->prefix . 'buddyc_' . $this->table_key;

        // Retrieve expected table structure
        $this->table_structure = $this->get_expected_structure();

        // Exit if table structure not defined
        if ( ! $this->table_structure ) {
            return;
        }

        // Create or update table if necessary
        $success = $this->check_table();
    }

    /**
     * Cleans the table key.
     * 
     * @since 1.0.4
     * 
     * @param   string  $key    The unique table key.
     */
    private function clean_table_key( $key ) {
        // Remove all characters that are not letters, numbers, or underscores
        return preg_replace( '/[^a-zA-Z0-9_]/', '', $key );
    }

    /**
     * Retrieves the expected table structure.
     * 
     * @since 1.0.17
     * 
     * @return  array   The expected table structure.
     */
    private function get_expected_structure() {
        $tables = self::define_tables();
        return $tables[$this->table_key] ?? null;
    }
    
    /**
     * Checks if the table needs to be created or updated.
     * 
     * @since 0.1.0
     * 
     * @return  bool    True on success, false on failure.
     */
    private function check_table() {
        // Initialize
        $success = false;
        
        // Check if the table exists
        if ( ! $this->table_exists() ) {

            // Table does not exist, create it
            if ( $this->table_structure ) {
                $success = $this->create_table();
            }
            
        } else {
            
            // Table exists, check for correct structure
            $differences = $this->compare_table_structures();

            // Check if differences exist
            if ( ! empty( $differences ) ) {
                $success = $this->update_table_structure( $differences );
            }
        }
        return $success;
    }

    /**
     * Compares the existing and expected table structures.
     * 
     * @since 1.0.17
     * 
     * @return  array   Associative array of differences.
     *                  Keyed by 'modify', 'add', and 'remove'.
     */
    public function compare_table_structures() {
        // Get structures to compare
        $current_structure = $this->get_table_structure();
        $expected_structure = $this->table_structure;

        // Initialize
        $differences = [];

        // Normalize field names
        $normalized_current_structure = array_change_key_case( $current_structure, CASE_LOWER );
        $normalized_expected_structure = array_change_key_case( $expected_structure, CASE_LOWER );
   
        // Compare fields from the expected structure with the current structure
        foreach ( $normalized_expected_structure as $field => $expected_type ) {
            $normalized_expected_type = $this->buddyc_normalize_column_type( $expected_type );
   
            if ( isset( $normalized_current_structure[$field] ) ) {
                $normalized_current_type = $this->buddyc_normalize_column_type( $normalized_current_structure[$field] );
   
                // Compare types
                if ( $normalized_current_type !== $normalized_expected_type ) {
                    $differences['modify'][$field] = $expected_type;
                }
            } else {
                // Field is missing in the current structure
                $differences['add'][$field] = $expected_type;
            }
        }
   
        // Check for extra fields in the current structure that are not in the expected structure
        foreach ( $normalized_current_structure as $field => $current_type ) {
            if ( ! isset( $normalized_expected_structure[$field] ) ) {
                $differences['remove'][$field] = $current_type;
            }
        }

        // Return array
        return $differences;
   }
   
   /**
    * Normalize column types to ensure comparison is case-insensitive and ignores certain formatting differences.
    * Modify this function based on the specific formatting rules for your database.
    */
   private function buddyc_normalize_column_type( $type ) {
        // Lowercase
        $type = strtolower( trim( $type ) );

        // If key found, replace with value
        $replace = [
            'datetime'      => 'datetime',
            'int(11)'       => 'int(11)',
            'boolean'       => 'tinyint(1)',
            'varchar(255)'  => 'varchar(255)',
        ];

        // Replace values
        foreach ( $replace as $key => $value ) {
            if ( strpos( $type, $key ) !== false ) {
                $type = $value;
                break;
            }
        }

        // Return modified string
        return $type;
   }
    
    /**
     * Retrieves the current table structure from the database.
     * 
     * @return  array   The current table structure.
     */
    private function get_table_structure() {
        global $wpdb;

        // Initialize
        $current_structure = [];

        // Create cache key
        $cache_key = $this->create_cache_key( 'columns' );

        // Try to get data from cache
        $cached_records = wp_cache_get( $cache_key );

        // Return cached data if available
        if ( $cached_records !== false ) {
            $columns = $cached_records;

        // Otherwise query database
        } else {

            // Get columns
            $columns = $wpdb->get_results( "DESCRIBE " . esc_sql( $this->table_name ) );

            // Set cache for the results
            wp_cache_set( $cache_key, $columns );
        }

        // Loop through columns and build array
        foreach ( $columns as $column ) {
            $current_structure[$column->Field] = $column->Type;
        }

        return $current_structure;
    }
    
    /**
     * Updates the table structure to match the defined structure.
     * 
     * @since 1.0.17
     * 
     * @param   array   $differences    The array of differences between expected and current structures.
     * @return  bool    True if the table was updated successfully, false on failure or no changes.
     */
    private function update_table_structure( $differences ) {
        global $wpdb;
        
        // Retrieve existing records
        $existing_records = $this->get_all_records();

        // Ensure differences are not empty
        if ( empty( $differences ) ) {
            // No differences, successful
            return true;
        }

        // Prepare to track changes
        $alter_queries = [];

        // Loop through differences and prepare ALTER statements
        foreach ( $differences as $action => $columns ) {
            foreach ( $columns as $column => $type ) {
                $column = sanitize_key( $column ); // Sanitize column name
                if ( $action === 'add' ) {
                    // Column is missing, prepare to add it
                    $alter_queries[] = "ADD `$column` $type";
                } else if ( $action === 'modify' ) {
                    // Column type has changed, prepare to modify it
                    $alter_queries[] = "MODIFY `$column` $type";
                } else if ( $action === 'remove' ) {
                    // Ignore
                }
            }
        }

        // Execute ALTER TABLE queries if there are changes
        if ( ! empty( $alter_queries ) ) {

            $queries_string = esc_sql( implode( ", ", $alter_queries ) );
            
            $result = $wpdb->query( $wpdb->prepare(
                "ALTER TABLE %i {$queries_string}",
                $this->table_name
                ) );

            // Check if the query was successful
            if ( $result === false ) {
                // Log error for debugging
                error_log("Failed to alter table $this->table_name: " . $wpdb->last_error);
                return false; // Failure to update the table
            }
        }

        // Invalidate cache if successful
        $this->invalidate_cache( true );

        // Return true if no issues were encountered
        return true;
    }

    /**
     * Creates the custom table.
     * 
     * @updated 0.4.3
     */
      public function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Ensure table structure is not empty
        if ( empty( $this->table_structure ) ) {
            return;
        }
        
        // Construct the CREATE TABLE SQL query
        $sql = "CREATE TABLE {$this->table_name} (";
        foreach ( $this->table_structure as $column_name => $column_type ) {
            // Prepare the column names to prevent SQL injection
            $column_name = sanitize_key( $column_name );
            $sql .= "`$column_name` $column_type, ";
        }
        $sql = rtrim( $sql, ', ' ); // Remove the trailing comma and space
        $sql .= ") $charset_collate;";
        
        // Include WordPress upgrade.php for dbDelta()
        $file = get_home_path() . 'wp-admin/includes/upgrade.php';
        if ( file_exists( $file ) ) {
            require_once( $file );
        } else {
            return;
        }
        
        // Execute the SQL query using dbDelta()
        dbDelta( $sql );
        
        // Check if table was created
        $table_exists = $this->table_exists();

        // Invalidate cache if successful
        if ( $table_exists ) {
            $this->invalidate_cache( true );
        }
        
        return $table_exists;
    }

    /**
     * Inserts a new record.
     *
     * @param   ?array      $data   An associative array of data to be inserted. Defaults to null.
     * @return  int|false   The inserted record ID or false on failure.
     */
    public function insert_record( $data = null ) {
        global $wpdb;

        // Initialize
        $new_id = null;

        // No data provided
        if ( $data === null ) {
            // Insert a blank record
            $wpdb->query( $wpdb->prepare( "INSERT INTO %i () VALUES ()", $this->table_name ) );
            $new_id = $wpdb->insert_id;

        // Data provided
        } else {    
            // Insert the record with provided data
            $wpdb->insert( $this->table_name, $data );
            $new_id = $wpdb->insert_id;
        }

        // Invalidate cache if successful
        if ( $new_id ) {
            $this->invalidate_cache();
        }

        // Return the id of the new row
        return $new_id;
    }   
    
    /**
     * Updates an existing record.
     *
     * @param int   $record_id The ID of the record to update.
     * @param array $data      An associative array of data to be updated.
     * @return bool            True on success, false on failure.
     */
    public function update_record( $record_id, $data ) {
        global $wpdb;
        $updated = $wpdb->update( $this->table_name, $data, array( 'ID' => $record_id ) );
        
        // Invalidate cache if update was successful
        if ( $updated !== false ) {
            $this->invalidate_cache();
        }
        
        return $updated;
    }

    /**
     * Retrieves all records from the custom table.
     * 
     * @param   string  $order_key  Optional. The column to order by.
     * @param   string  $order      Optional. How to order the items.
     *                              Accepts 'DESC' and 'ASC'. Defaults to 'DESC'.
     *
     * @return  array|null  An array of records or null if no records found.
     */
    public function get_all_records( $order_key = null, $order = null ) {
        global $wpdb;

        // Default to newest first
        $order_key = $order_key ?? 'created_at';
        $order = $order ?? 'DESC';

        // Create a unique cache key based on order key and order
        $cache_key = $this->create_cache_key( 'all_records', [$order_key, $order] );

        // Try to get data from cache
        $cached_records = wp_cache_get( $cache_key );
    
        // Return cached data if available
        if ( $cached_records !== false ) {
            return $cached_records;
        }
        
        // Prepare and process the query
        $table_name = esc_sql($this->table_name);
        
        // Build orderby
        if ( $this->column_exists( $order_key ) ) {
            $orderby = $order_key && $order ? 'ORDER BY ' . $order_key . ' ' . $order : '';
        } else {
            $orderby = '';
        }

        // Query database
        $records = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM %i {$orderby}",
            $this->table_name
            ));
    
        // Set cache for the results
        wp_cache_set( $cache_key, $records );
    
        return $records;
    }

    /**
     * Checks whether a column exists in the table.
     * 
     * @since 1.0.17
     * 
     * @param   string  $column_name    The name of the column to check.
     */
    public function column_exists( $column_name ) {
        global $wpdb;

        // Sanitize column name
        $column_name = sanitize_key( $column_name );

        // Create cache key
        $cache_key = $this->create_cache_key( 'column_names' );

        // Try to get data from cache
        $cached_columns = wp_cache_get( $cache_key );

        // Return cached data if available
        if ( $cached_columns !== false ) {
            $columns = $cached_columns;
        
        // Otherwiser query database
        } else {        
            // Get all columns
            $columns = $wpdb->get_results( $wpdb->prepare(
                "SHOW COLUMNS FROM %i",
                $this->table_name
            ));
            
        }

        // Set cache for the results
        wp_cache_set( $cache_key, $columns );

        // Loop through and check columns
        foreach ( $columns as $column ) {
            if ( $column->Field === $column_name ) {
                // Return if found
                return true;
            }
        }

        // Column not found
        return false;
    }
    
    
    /**
     * Retrieves a single record from the custom table by its ID.
     *
     * @param   int     $record_id      The ID of the record to retrieve.
     * 
     * @return  array|null  The retrieved record as an associative array, or null if not found.
     */
    public function get_record_by_id( $record_id ) {
        global $wpdb;

        // Create cache key
        $cache_key = $this->create_cache_key( 'record', [$record_id] );

        // Try to get data from cache
        $cached_record = wp_cache_get( $cache_key );

        // Return cached data if available
        if ( $cached_record !== false ) {
            return $cached_record;
        }

        $record = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM %i WHERE ID = %d",
            $this->table_name,
            $record_id
            ) );
    
        // Set cache for the results
        wp_cache_set( $cache_key, $record );

        // Return record
        return $record;
    }
    
    /**
     * Retrieves a record from the custom table by a specified column value.
     *
     * @param   string      $column_name The name of the column to search.
     * @param   mixed       $column_value The value of the column to search for.
     * 
     * @return  array|null  The record matching the column value or null if not found.
     */
    public function get_record_by_column( $column_name, $column_value ) {
        global $wpdb;

        // Create cache key
        $cache_key = $this->create_cache_key( 'record', [$column_name, $column_value] );

        // Try to get data from cache
        $cached_record = wp_cache_get( $cache_key );

        // Return cached data if available
        if ( $cached_record !== false ) {
            return $cached_record;
        }

        // Query database
        $record = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM %i WHERE %i = %s ORDER BY created_at DESC",
                $this->table_name,
                $column_name,
                $column_value
            ));

        // Set cache for the results
        wp_cache_set( $cache_key, $record );

        return $record;
    }
    
    /**
     * Retrieves all records from the custom table by a specified column value.
     * 
     * @since 0.1.0
     *
     * @param   string  $column_name    The name of the column to search.
     * @param   mixed   $column_value   The value of the column to search for.
     * @param   bool    $return_ids     Optional. Set to true to return IDs only. Defaults to false.
     * @param   bool    $search_arrays  Optional. Set to true to search serialized arrays for the provided value. Defaults to false.
     * 
     * @return  array|null  The records matching the column value or null if not found.
     */
    public function get_all_records_by_column( $column_name, $column_value, $return_ids = false, $search_arrays = false ) {
        global $wpdb;

        // Check if the column exists in the table
        if ( ! $this->column_exists( $column_name ) ) {
            return false;
        }

        // Create cache key
        $cache_key = $this->create_cache_key( 'all_records', [$column_name, $column_value, $return_ids, $search_arrays] );

        // Try to get data from cache
        $cached_records = wp_cache_get( $cache_key );

        // Return cached data if available
        if ( $cached_records !== false ) {
            return $cached_records;
        }
    
        // Prepare the query based on whether we are searching within serialized arrays
        if ( $search_arrays ) {
            // Escape and serialize the column value for searching within serialized arrays
            $escaped_column_value = '%' . $wpdb->esc_like( serialize( $column_value ) ) . '%';
            $results = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM %i WHERE %i LIKE %s ORDER BY created_at DESC",
                $this->table_name,
                $column_name,
                $escaped_column_value
            ));
        } else {
            // Prepare the query for exact match
            $results = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM %i WHERE %i = %s ORDER BY created_at DESC",
                $this->table_name,
                $column_name,
                $column_value
            ));
        }
    
        // Treat as no results if results are empty, null, or if a column error occurred
        if ( empty( $results ) || $results === false ) {
            return null;
        }
    
        // Return only IDs if $return_ids is true
        if ( $return_ids ) {
            return array_map( function( $result ) {
                return $result->ID;
            }, $results );
        }

        // Set cache for the results
        wp_cache_set( $cache_key, $results );

        // Return results    
        return $results;
    }

    /**
     * Deletes a record from the custom table by ID.
     *
     * @param   int     $record_id The ID of the record to delete.
     * 
     * @return  bool    True on success, false on failure.
     */
    public function delete_record( $record_id ) {
        global $wpdb;
        
        // Perform the delete operation
        $rows_affected = $wpdb->delete( $this->table_name, array('id' => $record_id ) );

        // Check if successful
        $deleted = $rows_affected !== false && $rows_affected > 0;

        // Invalidate cache
        if ( $deleted ) {
            $this->invalidate_cache();
        }
        
        // Return true if a row was deleted, false otherwise
        return $deleted;
    }
    
    /**
     * Checks whether the database table exists.
     * 
     * @since 0.1.0
     */
    public function table_exists() {
        global $wpdb;

        // Create cache key
        $cache_key = $this->create_cache_key( 'exists' );

        // Try to get data from cache
        $cached_result = wp_cache_get( $cache_key );

        // Return cached data if available
        if ( $cached_result !== false ) {
            return $cached_result;
        }
    
        // Prepare the SQL query with a placeholder for the table name
        $result = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $this->table_name ) );

        // Set cache for the results
        wp_cache_set( $cache_key, $result );
    
        // Check if the result is not false or null
        return $result !== null;
    }    
    
    /**
     * Deletes the table from the database.
     * 
     * @since 0.1.0
     *
     * @return bool True on success, false on failure.
     */
    public function delete_table() {
        global $wpdb;
    
        // Construct the SQL query to drop the table
        $result = $wpdb->query( $wpdb->prepare( "DROP TABLE IF EXISTS %i", $this->table_name ) );

        // Check if successful
        if ( $result !== false ) {
            // Invalidate cache
            $this->invalidate_cache( true );
        }
    
        // Return true if the query was successful, false otherwise
        return $result !== false;
    }

    /**
     * Invalidates the cache.
     * 
     * @since 1.0.16
     * 
     * @param   bool  $table   Optional. Whether to invalidate actions applying to the
     *                        entire table and its structure. Defaults to false.
     */
    private function invalidate_cache( $table = false ) {
        $actions = [
            'all_records',
        ];

        $table_actions = [
            'columns',
            'column_names',
            'exists'
        ];

        // Check if invalidating table structure
        if ( $table ) {
            $actions = array_merge( $actions, $table_actions );
        }

        // Loop through and invalidate all actions
        foreach ( $actions as $action ) {

            // Build cache key
            $key = $this->create_cache_key( $action );

            // Append wildcard
            $key = $key . '*';

            // Delete cache
            wp_cache_delete( $key );
        }
    }

    /**
     * Generates a cache key.
     * 
     * @since 1.0.17
     * 
     * @param   string  $action The action key.
     * @param   array   $params Optional. An array of paramaters to attach (e.g. orderby).
     */
    private function create_cache_key( $action, $params = [] ) {
        // Format params
        if ( ! is_array( $params ) ) {
            $params = [$params];
        }

        // Remove empty params
        $params = array_filter( $params );

        // Build prefix and suffix
        $prefix = 'buddyc_' . $action . '_';
        $suffix = ! empty( $params ) ? '_' . implode( '_', $params ) : '';

        // Build and return key
        $cache_key = $prefix . $this->table_key . $suffix;
        return $cache_key;
    }
}
?>
