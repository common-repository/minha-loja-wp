<?php
class BWEC_wpdb
{
    /**
     * Class construct. Only set the $wpdb variables
     */
    public function  __construct()
    {
        $this->_set_variables();
    }

    /**
     * Create all tables used by plugin.
     * 
     * @global type $wpdb 
     */
    public function create_tables()
    {
        global $wpdb;

        $charset = $this->_get_charset();

        $sql_create_table[] = "
            CREATE TABLE IF NOT EXISTS $wpdb->bwec_table_cart (
                cart_id                 int(11)         NOT NULL    AUTO_INCREMENT,
                cart_post_id            bigint(20)      NOT NULL,
                cart_cookie_id          varchar(40)     NOT NULL,
                cart_quantity           tinyint(3)      NOT NULL,
                cart_registration_date  timestamp       NOT NULL    DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (cart_id)) $charset;";

        $sql_create_table[] = "
            CREATE TABLE IF NOT EXISTS $wpdb->bwec_table_meta (
                meta_id                 int(11)         NOT NULL    AUTO_INCREMENT,
                meta_type               varchar(24)     NOT NULL,
                meta_key                varchar(255)    NOT NULL,
                meta_value              longtext        NOT NULL,
                PRIMARY KEY (meta_id)) $charset;";

        include_once ABSPATH . '/wp-admin/includes/upgrade.php';
        foreach ( $sql_create_table as $query )
            dbDelta( $query );
    }

    
    /**
     * Retrieves all products in shopping cart. If $cart_id is set returns all
     * products of this sopping cart. Otherwise all products in cart will be returned.
     * 
     * @global type $wpdb
     * @param type $cart_id
     * @return type 
     */
    public function get_products_in_shop_cart( $cart_id = null )
    {
        global $wpdb;

        $where = ( isset( $cart_id ) ) ? " AND C.cart_cookie_id = '$cart_id'" : '' ;

        $query = "SELECT C.cart_id, C.cart_cookie_id, P.ID, P.post_title, C.cart_quantity
                FROM $wpdb->bwec_table_cart AS C
                INNER JOIN $wpdb->posts AS P
                ON C.cart_post_id = P.ID
                WHERE 1=1 $where";

        return $wpdb->get_results( $wpdb->prepare( $query ) ) ;
    }

    /**
     * Check if product already was added to shop cart.
     *
     * @param int $prod_id
     * @return bool True if product already was added to shop cart.
     */
    public function product_was_added( $cart_post_id, $cart_id )
    {
        global $wpdb;

        $query = sprintf( "
                SELECT COUNT(cart_id)
                FROM $wpdb->bwec_table_cart
                WHERE cart_cookie_id = '%s'
                AND cart_post_id = %d",
                $cart_id,
                $cart_post_id
        );

        $result = $wpdb->get_var( $wpdb->prepare( $query ) );

        return ( $result > 0 );
    }

    /**
     * Include a product to shopping cart.
     * 
     * @global type $wpdb
     * @param type $prod_id
     * @param type $quantity
     * @param type $cart_id 
     */
    public function add_product_in_shop_cart( $prod_id, $quantity, $cart_id )
    {
        global $wpdb;

        $query = sprintf(
                "INSERT INTO $wpdb->bwec_table_cart
                ( cart_post_id, cart_cookie_id, cart_quantity )
                VALUES
                ( %d, '%s', %d )",
                $prod_id, $cart_id, $quantity
        );

        $wpdb->query( $wpdb->prepare( $query ) );
    }

    /**
     * Remove a product from a specific shopping cart.
     * 
     * @global type $wpdb
     * @param type $prod_id
     * @param type $cart_id 
     */
    public function delete_product_in_shop_cart( $prod_id, $cart_id )
    {
        global $wpdb;

        $query = sprintf(
                "DELETE FROM $wpdb->bwec_table_cart
                WHERE cart_post_id = %d
                AND cart_cookie_id = '%s'",
                $prod_id, $cart_id
        );

        $wpdb->query( $wpdb->prepare( $query ) );
    }
    
    /**
     * Remove all products from a specific shopping cart.
     * 
     * @global type $wpdb
     * @param type $cart_id 
     */
    public function delete_products_in_cart( $cart_id )
    {
        global $wpdb;

        $query = "DELETE FROM $wpdb->bwec_table_cart
                  WHERE cart_cookie_id = '$cart_id'";

        $wpdb->query( $wpdb->prepare( $query ) );
    }

    /**
     * Update the quantity of a product in shopping cart.
     * 
     * @global type $wpdb
     * @param type $prod_id
     * @param type $quantity
     * @param type $cart_id 
     */
    public function update_product_quantity_in_shop_cart( $prod_id, $quantity, $cart_id )
    {
        global $wpdb;

        $query = sprintf(
                "UPDATE $wpdb->bwec_table_cart
                SET cart_quantity = %d
                WHERE cart_post_id = %d
                AND cart_cookie_id = '%s'",
                $quantity, $prod_id, $cart_id
        );

        $wpdb->query( $wpdb->prepare( $query ) );
    }


    /**
     * Insert a meta value.
     * 
     * @global type $wpdb
     * @param type $type
     * @param type $key
     * @param type $value 
     */
    public function add_meta( $type, $key, $value )
    {
        global $wpdb;

        $query = sprintf(
                "INSERT INTO $wpdb->bwec_table_meta
                ( meta_type, meta_key, meta_value )
                VALUES
                ( '%s', '%s', '%s' )",
                $type, $key, $value
        );

        $wpdb->query( $wpdb->prepare( $query ) );
    }

    /**
     * Update the meta value.
     * 
     * @global type $wpdb
     * @param type $type
     * @param type $key
     * @param type $value
     * @return type 
     */
    public function update_meta_value( $type, $key, $value )
    {
        global $wpdb;

        $current_value = $this->get_meta_value( $type, $key );
        if ( empty( $current_value ) )
            return $this->add_meta( $type, $key, $value );

        $query = sprintf(
                "UPDATE $wpdb->bwec_table_meta
                SET meta_value = '%s'
                WHERE meta_type = '%s' AND meta_key = '%s'",
                $value, $type, $key
        );

        $wpdb->query( $wpdb->prepare( $query ) );
    }

    /**
     * Retrieve a meta value.
     * 
     * @global type $wpdb
     * @param type $type
     * @param type $key
     * @return type 
     */
    public function get_meta_value( $type, $key )
    {
        global $wpdb;

        $query = sprintf(
                "SELECT meta_value FROM $wpdb->bwec_table_meta
                 WHERE meta_type = '%s' AND meta_key = '%s'",
                $type, $key
        );

        return $wpdb->get_var( $wpdb->prepare( $query ) );
    }

    
    
    /**
     * Set in WPDB variables with table names.
     * 
     * @global type $wpdb 
     */
    private function _set_variables()
    {
        global $wpdb;

        $wpdb->bwec_table_cart              = $wpdb->prefix . 'bwec_shopping_cart';
        $wpdb->bwec_table_meta              = $wpdb->prefix . 'bwec_meta';
    }

    /**
     * Retrieves the database charset do create new tables.
     * 
     * @global type $wpdb
     * @return type 
     */
    private function _get_charset()
    {
        global $wpdb;

        $charset_collate = '';
        
        if( $wpdb->supports_collation() ) :
            if( !empty( $wpdb->charset ) ) :
                    $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            endif;
            if( !empty( $wpdb->collate ) ) :
                    $charset_collate .= " COLLATE $wpdb->collate";
            endif;
        endif;

        return $charset_collate;
    }
}

global $obj_bwec_wpdb;
$obj_bwec_wpdb = new BWEC_wpdb();