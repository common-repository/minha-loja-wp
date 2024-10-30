<?php

class BWEC_Buscape {
    
    private $_xml_file_name;
    
    public function __construct() 
    {
        add_action( 'admin_init', array( &$this, 'generate_xml' ) );
        $this->_xml_file_name = BWEC_INCLUDES_PATH . '/files/produtos.xml';
    }
    
    public function get_file_url()
    {
        return str_replace( WP_PLUGIN_DIR, WP_PLUGIN_URL, $this->_xml_file_name );
    }
    
    public function generate_xml()
    {
        if ( !isset( $_POST['bwec_buscape_xml'] ) )
            return;
        
        $products = get_posts( 'post_type='.BWEC_CPT_PRODUCTS.'&numberposts=-1' );
        $store_name = get_option( BWEC_OPTION_BUSCAPE_STORE );
        
        // Remove the numbers and sanitize the store name
        $store_name = preg_replace( '/[0-9]/', '', $store_name );
        $store_name = sanitize_title( $store_name );
        
        foreach ( (array)$products as $product ) :
            $product_markup .= $this->_get_product_markup( $product ) . "\n";
        endforeach;
        
        $xml .= '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        $xml .= sprintf( '<%1$s>'."\n".'%2$s</%1$s>', $store_name, $product_markup );
        
        file_put_contents( $this->_xml_file_name, $xml );
    }
    
    private function _get_product_markup( $product )
    {
        $template_file = BWEC_INCLUDES_PATH . '/views/buscape-xml-products.php';
            if ( !file_exists( $template_file ) )
                return;

        $template       = file_get_contents( $template_file );
        $placeholders   = $this->_get_placeholders();
        $replaces       = $this->_get_replaces( $product->ID );
        
        return str_replace( $placeholders, $replaces, $template );
    }
    
    private function _get_placeholders()
    {
        return array(
            '{product_id}',
            '{product_cod}',
            '{product_url}',
            '{product_title}',
            '{product_price}',
            '{product_portions}',
            '{product_image}',
            '{product_category}'
        );
    }
    
    private function _get_replaces( $id )
    {
        $installment_price  = apiki_get_parcelas( _bwec_get_product_real_price( $id ) );
        
        $product_id         = $id;
        $product_cod        = get_post_meta( $id, BWEC_POSTMETA_CODE, true );
        $product_url        = get_permalink( $id );
        $product_title      = get_the_title( $id );
        $product_price      = number_format( _bwec_get_product_real_price( $id ), 2, ',', '' );
        $product_portions   = '12x R$ ' . number_format( $installment_price[12] , 2, ',', '' );
        $product_image      = $this->_get_product_thumbnail_src( $id );
        $product_category   = $this->_get_product_category( $id );
        
        return array(
            $product_id,
            $product_cod,
            $product_url,
            $product_title,
            $product_price,
            $product_portions,
            $product_image,
            $product_category
        );
    }
    
    private function _get_product_thumbnail_src( $post_id )
    {
        if ( has_post_thumbnail( $post_id ) )
            $image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
        else 
            $image_src[0] = BWEC_IMAGES_URL . '/not-image.png';
        
        return $image_src[0];
    }
    
    private function _get_product_category( $post_id )
    {
        $categories = get_the_terms( $post_id, BWEC_TAX_CATEGORY );
        $category = '';
        
        if ( !is_array( $categories ) || is_wp_error( $categories ) ) 
            return '';
        
        array_multisort($categories);
        
        foreach ( $categories as $cat )
            $category[] = $cat->name;

        return implode( ' : ', $category );
    }
    
}

global $obj_bwec_buscape;
$obj_bwec_buscape = new BWEC_Buscape();