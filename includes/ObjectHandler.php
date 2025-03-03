<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Includes\DatabaseManager;

/**
 * Manages objects in the database.
 * 
 * Retrieves, updates, and deletes objects stored in the database.
 * 
 * @since 0.1.0
 */
class ObjectHandler {
    
    /**
     * Fully qualified class name.
     * 
     * @since 0.1.0
     */
    private $full_class_name;
    
    /**
     * Class name without namespace.
     * 
     * @since 0.1.0
     */
    private $class_name;
    
    /**
     * Database instance.
     * 
     * @var DatabaseManager
     */
    protected $database;
    
    /**
     * Constructor method.
     * 
     * Formats the class name and initializes DatabaseManager.
     * 
     * @since 0.1.0
     * 
     * @param   string  $class_name     The fully qualified class name.
     */
    public function __construct( $full_class_name ) {
        
        // Extract fully qualified class name
        $this->full_class_name = $full_class_name;
        
        // Format class name without namespace
        $this->class_name = basename( str_replace( '\\', '/', $full_class_name ) );
        
        // Initialize database
        $this->database = new DatabaseManager( $this->class_name );
    }
    
    /**
     * Creates a new row in the database.
     * 
     * @since 0.1.0
     * 
     * @param   object  $object     The object to add to the database.
     * @return  int                 The new object ID.
     */
    public function new_object( $object ) {
        // Insert blank record to get ID
        $object->ID = $this->database->insert_record();

        // Set created_at only if it exists and is not already set
        if ( property_exists( $object, 'created_at' ) && empty( $object->created_at ) ) {
            $object->created_at = current_time( 'mysql' );
        }
        
        // Update the database record
        $this->update_object( $object->ID, $object );
        
        // Return ID
        return $object->ID;
    }
    
    /**
     * Retrieves an object from the database by ID.
     * 
     * @since 0.1.0
     * 
     * @param int $ID The ID of the object to retrieve.
     * @return object|bool The retrieved object on success, false on failure.
     */
    public function get_object( $ID ) {
        // Get record by ID
        $record = $this->database->get_record_by_id( strval($ID) ) ;

        // Extract object
        $object = $this->extract_objects( $record );

        return $object[0] ?? false;
    }
    
    /**
     * Retrieves all objects of a specified class.
     * 
     * @since 0.1.0
     * 
     * @return array An array of retrieved objects on success, empty array on failure.
     */
    public function get_all_objects() {
        
        // Initialize array
        $objects = [];
        
        // Get all records
        $records = $this->database->get_all_records();

        // Get objects
        $objects = $this->extract_objects( $records );

        return $objects;
    }

    /**
     * Retrieves objects from an array of records.
     * 
     * @since 1.0.17
     * 
     * @param   array   $records    An array of records.
     * @return  array   An array of objects.
     */
    private function extract_objects( $records ) {
        // Initialize
        $objects = [];
        $first_record = true;

        // Cast to array
        $records = is_array( $records ) ? $records : [$records];
        
        // Make sure it's an array
        if ( ! empty( $records ) ) {

            // Define lowercase class name
            $class_lower = strtolower( $this->class_name );
            
            // Loop through the records
            foreach ( $records as $record ) {

                // Check first record
                if ( $first_record ) {

                    // Update flag
                    $first_record = false;

                    // Standard cap class name
                    if ( isset( $record->{$this->class_name} ) ) {
                        $column_name = $this->class_name;

                    // Lowercase class name
                    } else if ( isset( $record->{$class_lower} ) ) {
                        $column_name = $class_lower;

                    // Skip if no object
                    } else {
                        continue;
                    }
                }
                
                // Retrieve serialized object
                $serialized_object = $record->{$column_name};
                
                // Check if the serialized object is empty
                if ( ! empty( $serialized_object )) {
                    // Unserialize the object
                    $unserialized_object = unserialize( $serialized_object );
                    
                    // Check if unserialization was successful
                    if ( $unserialized_object !== false ) {
                        
                        // Add to array
                        $objects[] = $unserialized_object;
                    }
                }
            }
        }
        return $objects;
    }
    
    /**
     * Retrieves objects of a certain age.
     * 
     * @since 0.1.0
     * 
     * @param   int     $days       The number of days that must have passed for the record to be expired.
     * @param   array   $objects    Optional. An array of objects to check.
     *                              Defaults to all objects.
     */
    public function get_expired_objects( $days, $objects = null ) {
        
        // Initialize array
        $expired_objects = [];
        
        // Use objects if provided
        $objects = $objects ?? $this->get_all_objects();
        
        // Check if the records exist
        if ( ! empty( $objects ) ) {
            
            // Get the current time as a Unix timestamp
            $current_timestamp = time();
            
            // Loop through the records
            foreach ( $objects as $object ) {
                
                // Calculate the difference in seconds
                $time_difference = $current_timestamp - strtotime( $object->created_at );
                
                // Check if the difference is greater than the number of days
                if ($time_difference > (int) $days * 24 * 60 * 60) {
                    
                    // Add to array
                    $expired_objects[] = $object;
                }
            }
        }
        return $expired_objects;
    }
    
    /**
     * Retrieves all objects of a specified class by a property.
     * 
     * @since 0.1.0
     * 
     * @param   string      $property       The property name to filter by.
     * @param   mixed       $value          The property value to filter by.
     * @param   bool        $search_arrays  Whether to search serialized arrays for the value.
     * @return array        An array of retrieved objects on success, empty array on failure.
     */
    public function get_objects_by_property( $property, $value, $search_arrays = false ) {
        
        // Initialize array
        $objects = [];
        
        // Try to get all records by the property
        $records = $this->database->get_all_records_by_column( $property, $value, false, $search_arrays );
        
        // Check if records were found
        if ( $records ) {

            // Get objects from records
            $objects = $this->extract_objects( $records );

        // No records found
        } else {
            // Filter all objects
            $objects = $this->filter_objects( $property, $value );
        }
        return $objects;
    }

    /**
     * Retrieves multiple objects by ID.
     * 
     * @since 1.0.27
     * 
     * @param   array   $object_ids       An array of IDs of the objects to retrieve.
     * @return  array   An array of retrieved objects on success, empty array on failure.
     */
    public function get_objects_by_ids( $object_ids ) {
        
        // Initialize array
        $objects = [];
        
        // Try to get all records by the property
        $records = $this->database->get_records_by_ids( $object_ids );
        
        // Check if records were found
        if ( $records ) {
            // Get objects from records
            $objects = $this->extract_objects( $records );
        }
        return $objects;
    }
    
    /**
     * Filters all objects by property.
     * 
     * @since 0.1.0
     * @updated 0.3.4
     * 
     * @param   array       $objects        An array of objects to filter.
     * @param   string      $property       The property name to filter by.
     * @param   ?array      $objects        Optional. An array of objects to filter.
     *                                      Defaults to all objects of the class.
     * @param   mixed       $value          The property value to filter by.
     * @return  array       Array of objects on success, empty array on failure.
     */
    public function filter_objects( $property, $value, $objects = null ) {
        
        // Initialize array
        $filtered_objects = [];
        
        // Make sure the property exists for the class
        if ( property_exists( $this->full_class_name, $property ) ) {
            
            // Get all objects if not defined
            $all_objects = $objects ?? $this->get_all_objects();
            
            // Loop through objects
            foreach ( $all_objects as $object ) {
                
                // Check if the property is an array
                if ( is_array( $object->{$property} ) ) {
                    // Check if the value exists in the array property
                    if ( in_array($value, $object->{$property} ) ) {
                        $filtered_objects[] = $object;
                    }
                } else {
                    // Check if the property matches the value
                    if ( $object->{$property} == $value ) {
                        $filtered_objects[] = $object;
                    }
                }
            }
        }
        return $filtered_objects;
    }
    
    /**
     * Sorts objects by a specified property.
     * 
     * @since 0.1.0
     * 
     * @param   array       $objects        An array of objects to sort.
     * @param   string      $property       The property name to sort by.
     * @param   string      $order          The sort order ('asc' or 'desc').
     * @return  array       Array of sorted objects.
     */
    public function sort_objects( $objects, $property, $order = 'asc' ) {
        // Sort the objects based on the property
        usort($objects, function($a, $b) use ($property, $order) {
            if ($order === 'desc') {
                return $b->{$property} <=> $a->{$property};
            } else {
                return $a->{$property} <=> $b->{$property};
            }
        });
        return $objects;
    }

    /**
     * Updates an object.
     * 
     * @since 0.1.0
     * @since 1.0.27 Use prepare_update_data to format data.
     *
     * @param int $ID The ID of the object to update.
     * @param object $object The updated object.
     * @return bool True on success, false on failure.
     */
    public function update_object( $ID, $object ) {
        $data = $this->prepare_update_data( $object );
        if ( ! $data ) {
            return false;
        }

        return $this->database->update_record( strval( $ID ), $data );
    }

    /**
     * Updates multiple objects in the database.
     *
     * @param array $objects An array of objects to be updated.
     * @return bool True on success, false on failure of any record.
     */
    public function update_objects( $objects ) {
        if ( empty( $objects ) || ! is_array( $objects ) ) {
            return false;
        }

        $records_data = [];
        foreach ( $objects as $object ) {
            $data = $this->prepare_update_data( $object );
            if ( $data ) {
                $records_data[$object->ID] = $data;
            }
        }
        // Update multiple records
        return $this->database->update_records( $records_data );
    }

    /**
     * Prepares object data to send to the DatabaseManager.
     * Removes the ID.
     * 
     * @since 1.0.27
     * 
     * @param   object  $object The object to prepare.
     * @return  array   An array of prepared data.
     */
    private function prepare_update_data( $object ) {
        if ( ! isset( $object->ID ) ) {
            return false; // Ensure the object has an ID
        }
    
        // Get the existing record
        $record = $this->database->get_record_by_id( $object->ID );
        $columns = $record ? get_object_vars( $record ) : [];
    
        // Initialize data with serialized object stored in the class name column
        $data = [
            $this->class_name => serialize( $object ),
        ];
    
        // Convert object properties
        foreach ( (array) $object as $property_name => $property_value ) {
            if ( in_array( $property_name, array_keys( $columns ) ) ) {
                $data[$property_name] = is_array( $property_value ) ? serialize( $property_value ) : $property_value;
            }
        }
    
        return $data; // ID is removed here
    }    
    
    /**
     * Updates an object property.
     * 
     * @since 0.1.0
     * 
     * @param   int     $ID         The ID of the object to update.
     * @param   string  $data       Array of new property values keyed by property name.
     * @return  object|bool The updated object on success, false on failure.
     */
    public function update_object_properties( $ID, $data ) {
        // Get object by ID
        $object = $this->get_object( $ID );
    
        if ( ! $object ) {
            return;
        }
    
        // Loop through properties to update
        foreach ( $data as $property => $value ) {
            $object->{$property} = $value;
        }
    
        // Update record in database
        $updated = $this->update_object( $ID, $object );
    
        // Return updated object
        return $updated ? $object : false;
    }

    /**
     * Updates properties for multiple objects.
     * 
     * @since 1.0.27
     * 
     * @param   array   $IDs        The IDs of the objects to update.
     * @param   string  $data       Array of new property values keyed by property name.
     * @return  array|bool An array of updated objects on success, false on failure of any object.
     */
    public function update_objects_properties( $IDs, $data ) {
        // Initialize
        $objects = [];
        $success = true;

        // Loop through ids and update
        foreach ( $IDs as $ID ) {
            $object = $this->update_object_properties( $ID, $data );
            if ( $object ) {
                $objects[$ID] = $object;
            } else {
                $success = false;
            }
        }
        return $success ? $objects : false;
    }    
    
    /**
     * Removes an array of objects from the database.
     * 
     * @since 0.1.0
     * 
     * @param   array   $objects     An array of objects to delete.
     */
    public function delete_objects( $objects ) {
        foreach ( $objects as $object ) {
            $this->delete_object( $object->ID );
        }
    }
    
    /**
     * Removes an object from the database.
     * 
     * @since 0.1.0
     * 
     * @param   int     $ID     The ID of the object to remove.
     * @return  bool    True on success, false on failure.
     */
    public function delete_object( $ID ) {
        $deleted = $this->database->delete_record( $ID );
        return $deleted;
    }
    
    /**
     * Removes all objects from the database.
     * 
     * @since 0.1.0
     */
    public function delete_all_objects() {
        $objects = $this->get_all_objects();
        foreach ( $objects as $object ) {
            $this->delete_object( $object->ID );
        }
    }   
}