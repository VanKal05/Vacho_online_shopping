<?php

class SwiftXRDatabase {

    private $table_name;

    function __construct($name){

        global $wpdb;

        $this->table_name = $wpdb->prefix . $name;

        $this->create_table();
    }

    function create_table(){

        global $wpdb;

        $table_exists = $wpdb->get_var( "SHOW TABLES LIKE '$this->table_name'" ) == $this->table_name;

        if( !$table_exists){

            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE IF NOT EXISTS $this->table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                url varchar(255) NOT NULL,
                height varchar(255) NOT NULL,
                width varchar(255) NOT NULL,
                wc_product_id int(11),
                PRIMARY KEY  (id)
            ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
        }

    }

    /**
     * Add or update a shortcode entry in the database.
     *
     * @param int|null $id Shortcode ID (null for new entry).
     * @param string $url Shortcode URL.
     * @param int $width Shortcode width.
     * @param string $width_unit Shortcode width unit (% or px).
     * @param int $height Shortcode height.
     * @param string $height_unit Shortcode height unit (% or px).
     *
     * @return int|false Shortcode ID on success, false on failure.
     */
    public function add_update_shortcode_entry( $id, $url, $width, $height, $wc_product_id ) {
        
        global $wpdb;

        // Prepare data for insertion or update.
        $data = array(
            'url'         => $url,
            'width'       => $width,
            'height'      => $height,
            'wc_product_id'      => $wc_product_id,
        );

        // Insert new entry.
        if ( $id === null ) {
            $result = $wpdb->insert( $this->table_name, $data );

            if ( $result === false ) {
                return false;
            }

            return $wpdb->insert_id;
        }

        // Update existing entry.
        $result = $wpdb->update( $this->table_name, $data, array( 'id' => $id ) );

        if ( $result === false ) {
            return false;
        }

        return $id;
    }

    /**
     * Delete a shortcode entry from the database.
     *
     * @param int $id Shortcode ID.
     *
     * @return bool True on success, false on failure.
     */
    public function delete_shortcode_entry( $id ) {
        global $wpdb;

        $result = $wpdb->delete( $this->table_name, array( 'id' => $id ) );

        if ( $result === false ) {
            return false;
        }

        return true;
    }

    /**

    *Get a shortcode entry from the database by ID.

    * @param int $id Shortcode ID.

    * @return array|null Shortcode entry data if found, null if not found.
    */
    public function get_shortcode_entry_by_id( $id ) {
        global $wpdb;

        $entry = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE id = %d", $id ), ARRAY_A );

        return $entry ? $entry : null;
    }

    public function get_shortcode_entry_by_wc_id( $product_id ) {
        global $wpdb;

        try {
            $entry = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE wc_product_id = %d", $product_id ), ARRAY_A );

            return $entry ? $entry : null;
        } catch (Exception $e) {
            return  null;
        }
    }

    /**
     * Get all shortcode entries from the database.
     *
     * @return array Array of shortcode entries.
     */
    public function get_all_shortcode_entries() {
        global $wpdb;

        return $wpdb->get_results( "SELECT * FROM $this->table_name", ARRAY_A );
    }

}