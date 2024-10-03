<?php
namespace BuddyClients\Includes;

/**
 * Interacts with the database.
 * 
 * Defines table structures. Handles table creation, updates, and deletion.
 * Retrieves, updates, and deletes data within tables.
 * 
 * @since 0.1.0
 */
class DatabaseManager {

    /**
     * Table key.
     * 
     * Class name without namespace.
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
     * Define tables.
     * 
     * @since 0.1.0
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
            'hash' => [
                'ID'                => 'INT AUTO_INCREMENT NOT NULL PRIMARY KEY',
                'key'               => 'TEXT',
                'version'           => 'TEXT',
                'hash'              => 'TEXT',
                'created_at'        => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ],
        ];
        
        /**
         * Filters the database table data.
         *
         * @since 0.3.4
         *
         * @param array  $tables    An array of database table data.
         */
         $tables = apply_filters( 'bc_tables', $tables );

         return $tables;
    }

    /**
     * Constructor.
     *
     * @param string $table_name The name of the custom table.
     */
    public function __construct( $table_key ) {
        global $wpdb;
        
        // Build table name
        $this->table_key = $this->clean_table_key($table_key);
        $this->table_name = $wpdb->prefix . 'bc_' . $table_key;
        
        // Create table if necessary
        $this->check_table( $table_key );
    }

    /**
     * Cleans the table key.
     * 
     * @since 1.0.4
     */
    private function clean_table_key( $key ) {
        // Remove all characters that are not letters, numbers, or underscores
        return preg_replace( '/[^a-zA-Z0-9_]/', '', $key );
    }
    
    /**
     * Checks if the table needs to be created or updated.
     * 
     * Calls the function to create or update the table if necessary.
     * 
     * @since 0.1.0
     */
    private function check_table( $table_key ) {
        
        // Check if the table exists
        if (!$this->table_exists()) {
            // If the table doesn't exist, create it
            $tables = self::define_tables();
            $table_structure = $tables[$table_key] ?? null;
            if ( $table_structure ) {
                $this->create_table($table_structure);
            }
        } else {
            // If the table exists, check if its structure matches the defined structure
            $tables = self::define_tables();
            $table_structure = $tables[$table_key] ?? null;
            if ( $table_structure ) {
                $current_structure = $this->get_table_structure();
            
                // Compare the structures
                if ($current_structure !== $table_structure) {
                    // If the structures don't match, update the table
                    // @todo This is deleting my table still
                  //  $this->update_table($table_structure);
                }
            }
        }
    }
    
    /**
     * Get the current table structure from the database.
     * 
     * @return array The current table structure.
     */
    private function get_table_structure() {
        global $wpdb;
        $table_name = $this->table_name;
        $current_structure = [];
        $columns = $wpdb->get_results("DESCRIBE $table_name");
        foreach ($columns as $column) {
            $current_structure[$column->Field] = $column->Type;
        }
        return $current_structure;
    }
    
    /**
     * Updates the table structure to match the defined structure.
     * 
     * @param array $new_structure The new table structure.
     */
    private function update_table($new_structure) {
        global $wpdb;
        $table_name = $this->table_name;
        $temp_table_name = $table_name . '_temp'; // Temporary table name
    
        // Create a temporary table with the new structure
        $this->create_table($new_structure, $temp_table_name);
    
        // Copy data from the existing table to the temporary table
        $wpdb->query("INSERT INTO $temp_table_name SELECT * FROM $table_name");
    
        // Drop the existing table
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    
        // Rename the temporary table to the original table name
        $wpdb->query("ALTER TABLE $temp_table_name RENAME TO $table_name");
    }

    /**
     * Creates the custom table if it doesn't exist.
     * 
     * @updated 0.4.3
     */
      public function create_table($table_structure) {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Ensure table structure is not empty
        if (empty($table_structure)) {
            error_log("Table structure is empty. Cannot create table.");
            return;
        }
    
        // Construct the CREATE TABLE SQL query
        $sql = "CREATE TABLE {$this->table_name} (";
        foreach ($table_structure as $column_name => $column_type) {
            $sql .= "`$column_name` $column_type, ";
        }
        $sql = rtrim($sql, ', '); // Remove the trailing comma and space
        $sql .= ") $charset_collate;";
    
        // Include WordPress upgrade.php for dbDelta()
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Execute the SQL query using dbDelta()
        dbDelta($sql);
        
        // Log the result of dbDelta() for debugging
        global $wpdb;
        $table_exists = $this->table_exists();
        
        if (! $table_exists ) {
            error_log("Failed to create table {$this->table_name}.");
        }
        
        // Log the list of existing tables for debugging
        $tables = $wpdb->get_col("SHOW TABLES LIKE '{$this->table_name}'");
    }

    /**
     * Insert a new record.
     *
     * @param   ?array      $data   An associative array of data to be inserted. Defaults to null.
     * @return  int|false   The inserted record ID or false on failure.
     */
    public function insert_record( $data = null ) {
        global $wpdb;
    
        if ($data === null) {
            // Insert a blank record
            $wpdb->query("INSERT INTO $this->table_name () VALUES ()");
            return $wpdb->insert_id;
        }
    
        // Insert the record with provided data
        $wpdb->insert($this->table_name, $data);
        return $wpdb->insert_id;
    }
    
    /**
     * Update an existing record.
     *
     * @param int   $record_id The ID of the record to update.
     * @param array $data      An associative array of data to be updated.
     * @return bool           True on success, false on failure.
     */
    public function update_record($record_id, $data) {
        global $wpdb;
        return $wpdb->update($this->table_name, $data, array('ID' => $record_id));
    }

    /**
     * Get all records from the custom table.
     *
     * @return array|null An array of records or null if no records found.
     */
    public function get_all_records( $order_key = null, $order = null ) {
        global $wpdb;

        // Default to newest first
        $order_key = $order_key ?? 'created_at';
        $order = $order ?? 'DESC';
        
        // Ensure order key and order are valid
        $order_key = in_array($order_key, ['created_at', 'another_column']) ? $order_key : 'created_at';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        
        // Prepare the query with placeholders
        $query = "SELECT * FROM {$this->table_name} ORDER BY $order_key $order";
        $prepared_query = $wpdb->prepare($query);
        
        return $wpdb->get_results($prepared_query);
    }
    
    /**
     * Get a single record from the custom table by its ID.
     *
     * @param int $record_id The ID of the record to retrieve.
     * @return array|null The retrieved record as an associative array, or null if not found.
     */
    public function get_record_by_id($record_id) {
        global $wpdb;
        $query = $wpdb->prepare("SELECT * FROM $this->table_name WHERE ID = %d", $record_id);
        return $wpdb->get_row($query);
    }
    
    /**
     * Get a record from the custom table by a specified column value.
     *
     * @param string $column_name The name of the column to search.
     * @param mixed $column_value The value of the column to search for.
     * @return array|null The record matching the column value or null if not found.
     */
    public function get_record_by_column($column_name, $column_value) {
        global $wpdb;
        $query = $wpdb->prepare(
            "SELECT * FROM $this->table_name WHERE $column_name = %s ORDER BY created_at DESC",
            $column_value
        );
        return $wpdb->get_row($query);
    }
    
    /**
     * Get all records from the custom table by a specified column value.
     * 
     * @since 0.1.0
     * @updated 0.3.4
     *
     * @param string $column_name The name of the column to search.
     * @param mixed $column_value The value of the column to search for.
     * @param bool $return_ids Optional. Set to true to return IDs only. Defaults to false.
     * @param bool $search_arrays Optional. Set to true to search serialized arrays for the provided value. Defaults to false.
     * @return array|null The records matching the column value or null if not found.
     */
    public function get_all_records_by_column($column_name, $column_value, $return_ids = false, $search_arrays = false) {
        global $wpdb;
    
        // Prepare the query based on whether we are searching within serialized arrays
        if ($search_arrays) {
            // Escape and serialize the column value for searching within serialized arrays
            $escaped_column_value = '%' . $wpdb->esc_like(serialize($column_value)) . '%';
            $query = $wpdb->prepare(
                "SELECT * FROM $this->table_name WHERE $column_name LIKE %s ORDER BY created_at DESC",
                $escaped_column_value
            );
        } else {
            // Prepare the query for exact match
            $query = $wpdb->prepare(
                "SELECT * FROM $this->table_name WHERE $column_name = %s ORDER BY created_at DESC",
                $column_value
            );
        }
    
        // Suppress errors and execute the query
        $results = @$wpdb->get_results($query);
    
        // Treat as no results if results are empty, null, or if a column error occurred
        if (empty($results) || $results === false) {
            return null;
        }
    
        // Return only IDs if $return_ids is true
        if ($return_ids) {
            return array_map(function($result) {
                return $result->ID;
            }, $results);
        }
    
        return $results;
    }

    /**
     * Delete a record from the custom table by ID.
     *
     * @param   int     $record_id The ID of the record to delete.
     * @return  bool    True on success, false on failure.
     */
    public function delete_record($record_id) {
        global $wpdb;
        
        // Perform the delete operation
        $rows_affected = $wpdb->delete($this->table_name, array('id' => $record_id));
        
        // Return true if a row was deleted, false otherwise
        return $rows_affected !== false && $rows_affected > 0;
    }
    
    /** Development Methods **/
    
    /**
     * Checks whether the database table exists.
     * 
     * @since 0.1.0
     */
    public function table_exists() {
        global $wpdb;
        $sql = "SHOW TABLES LIKE '{$this->table_name}'";
        $result = $wpdb->get_var($sql);
        return $result !== null;
    }
    
    /**
     * Delete the table from the database.
     * 
     * @since 0.1.0
     *
     * @return bool True on success, false on failure.
     */
    public function delete_table() {
        global $wpdb;
    
        // Construct the SQL query to drop the table
        $sql = "DROP TABLE IF EXISTS $this->table_name";
    
        // Execute the SQL query
        $result = $wpdb->query($sql);
    
        // Return true if the query was successful, false otherwise
        return $result !== false;
    }
    
    /**
     * Returns table columns.
     * 
     * @since 0.1.0
     * 
     * @return  array   Array of table columns.
     */
    public function table_columns() {
        
        // Initialize
        $columns = [];
        
        // Get the table structure
        $tables = self::define_tables();
        $table_structure = $tables[$this->table_key] ?? null;
        
        // Loop through the items
        foreach ( $table_structure as $key => $type ) {
            // Add the keys to the array
            $columns[] = $key;
        }
        return $columns;
    }

}
?>
