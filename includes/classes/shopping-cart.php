<?php
class BWEC_Shopping_Cart
{
    /** @var string The cookie name for save the session. */
    private $_cookie_name = 'bwec-shopping-cart';

    /** @var string The value of the cookie. It corresponds to cart_cookie_id,
     * that identifies each shop cart.
     */
    private $_cookie_value;

    /**
     * Class construct. Assign the hooks used in this class.
     */
    public function  __construct()
    {
        add_action( 'init', array( $this, 'set_session_cookie' ) );
        add_action( 'template_redirect', array( $this, 'execute_cart_operations' ) );

        add_shortcode( 'buscape-wp-ecommerce-cart', array( $this, 'display' ) );
    }

    /**
     * Retrieves the current shopping cart id.
     *
     * @return string
     */
    public function get_cart_id()
    {
        return $this->_cookie_value;
    }

    /**
     * Retrieves the products in current shop cart.
     *
     * @global object $obj_bwec_wpdb
     * @return array The products in shop cart.
     */
    public function get_products()
    {
        global $obj_bwec_wpdb;

        return $obj_bwec_wpdb->get_products_in_shop_cart( $this->_cookie_value );
    }

    /**
     * Insert a item in current shopping cart.
     *
     * @global object $obj_bwec_wpdb
     * @param type $prod_id
     * @param type $quantity
     * @return type
     */
    public function add_item( $prod_id, $quantity = 1 )
    {
        global $obj_bwec_wpdb;

        if ( !$this->_is_valid_post_id( $prod_id ) )
            return;

        if ( !$obj_bwec_wpdb->product_was_added( $prod_id, $this->_cookie_value ) ) {
            $obj_bwec_wpdb->add_product_in_shop_cart( $prod_id, $quantity, $this->_cookie_value );
            $_SESSION['bwec_added_id'] = $prod_id;
        }
    }

    /**
     * Remove a item from current shopping cart.
     *
     * @global object $obj_bwec_wpdb
     * @param type $prod_id
     */
    public function delete_item( $prod_id )
    {
        global $obj_bwec_wpdb;

        $obj_bwec_wpdb->delete_product_in_shop_cart( $prod_id, $this->_cookie_value );
    }

    /**
     * Update item quantity in current shopping cart.
     *
     * @global object $obj_bwec_wpdb
     * @param type $prod_id
     * @param type $quantity
     */
    public function update_item( $prod_id, $quantity )
    {
        global $obj_bwec_wpdb;

        if ( $quantity > 0 ){
            $obj_bwec_wpdb->update_product_quantity_in_shop_cart( $prod_id, $quantity, $this->_cookie_value );
            $_SESSION['bwec_added_id'] = $prod_id;
            $_SESSION['bwec_added_quantity'] = $quantity;
        }else{
            $this->delete_item ( $prod_id );
        }
    }

    /**
     * Hooked by shortcode to display the shopping cart. Show the screen according
     * with the step.
     *
     * @global type $errors
     * @return type
     */
    public function display()
    {
        global $errors;

        $step = intval( $_GET['step'] );
        switch ( $step ) {

            case 1 : // Only display shopping cart after any operation
                return $this->_display_shopping_cart();
                break;

            case 3: // Confirmation
                include BWEC_INCLUDES_PATH . '/views/confirmation.php';
                break;

            default:
                break;
        }
    }

    /**
     * Set the session cookie that validates the shopping cart. This function
     * needs to occur before the headers be sent, in init hook.
     */
    public function set_session_cookie()
    {
        if ( !isset( $_SESSION ) )
            session_start();

        // Set the cookie with the value of shopper_id query arg for recover cart by ebehavior
        if ( isset( $_GET['shopper_id'] ) ) :
            $shopper_id = $_GET['shopper_id'];
            setcookie( $this->_cookie_name, $shopper_id, $this->_cookie_lifetime( 30 ), '/' );
            wp_redirect( bwec_get_cart_page_permalink() );
            exit();
        endif;

        // Return the cookie value if exists
        if ( isset( $_COOKIE[$this->_cookie_name] ) )
            return $this->_cookie_value = $_COOKIE[$this->_cookie_name];

        // Sets the cookie to 30 days
        $this->_cookie_value = session_id();
        setcookie( $this->_cookie_name, $this->_cookie_value, $this->_cookie_lifetime( 30 ), '/' );
    }

    /**
     * In each page load catch any operations of shopping cart.
     *
     * @global type $obj_bwec_shipping
     * @return type
     */
    public function execute_cart_operations()
    {
        global $obj_bwec_shipping;

        // Anything will be do if its not cart page.
        // Except in case where is the page register and the button register was hold.
        if ( !$this->_is_cart_page() )
            return;

        // If exists the step variable any operations in shopping cart will be do,
        // but redirects can be occour.
        if ( isset( $_GET['step'] ) ) {
            $step = intval( $_GET['step'] );

            // Save the CEP in meta table.
            $obj_bwec_shipping->update_meta_cep();

            // If is the step 2 and the button zipcode is hold. Calculate shipping value.
           if ( $step == 2 && isset( $_POST['bwec_zipcode'] ) ) {
               wp_redirect ( add_query_arg( array( 'step' => 1 ), bwec_get_cart_page_permalink() ) );
               exit();
           }

            // Close shop cart if is ste 3 and the button was pressed
            if ( $step == 3 )
                $this->clean_shop_cart();

            return;

        }

        if ( !isset( $_GET['bwec_action'] ) || !isset( $_GET['bwec_prod_id'] ) ) {
            wp_redirect ( add_query_arg( array( 'step' => 1 ), bwec_get_cart_page_permalink() ) );
            exit;
        }

        $operation = esc_attr( strip_tags( $_GET['bwec_action'] ) );
        $prod_id = intval( $_GET['bwec_prod_id'] );
        $quantity = ( isset( $_GET['bwec_prod_quantity'] ) ) ? $_GET['bwec_prod_quantity'] : 1;

        switch ( $operation )
        {
            case 'add':
                $this->add_item( $prod_id );
            break;

            case 'update':
                $this->update_item( $prod_id, $quantity );
            break;

            case 'delete':
                $this->delete_item( $prod_id );
            break;
        }

        wp_redirect ( add_query_arg( array( 'step' => 1 ), bwec_get_cart_page_permalink() ) );
        exit;
    }

    /**
     * Retrieves the total value of the shopping cart. Can be returned with or
     * withouth shipping value.
     *
     * @global type $obj_bwec_shipping
     * @param type $shipping
     * @return type
     */
    public function get_cart_total_price( $shipping = true )
    {
        global $obj_bwec_shipping;

        $products_in_cart   = $this->get_products();
        $checkout_value     = 0;

        foreach ( (array)$products_in_cart as $product )
            $checkout_value = $checkout_value + ( _bwec_get_product_real_price( $product->ID )*$product->cart_quantity );

        if ( $shipping ) {
            $shipping = $obj_bwec_shipping->calculate_shipping( $checkout_value );
            if ( !empty( $shipping ) )
                $checkout_value = $checkout_value + $shipping['valor'];
        }

        return $checkout_value;
    }

    /**
     * Remove all products in current shopping cart.
     *
     * @global object $obj_bwec_wpdb
     */
    public function clean_shop_cart()
    {
        global $obj_bwec_wpdb;

        $obj_bwec_wpdb->delete_products_in_cart( $this->get_cart_id() );
    }

    /**
     * Display the shopping cart form.
     *
     * @return type
     */
    private function _display_shopping_cart()
    {
        $products_in_cart = $this->get_products();

        if ( count( $products_in_cart ) == 0 ) {
            include BWEC_INCLUDES_PATH . '/views/shopping-cart-empty.php';

            return;
        }

        $cart_items_markup = '';
        $checkout_value = 0;

        foreach ( $products_in_cart as $product )
        {
            $item_file_dir = BWEC_INCLUDES_PATH . '/views/shopping-cart-items.php';
            if ( !file_exists( $item_file_dir ) )
                return;

            $item_template      = file_get_contents( $item_file_dir );
            $item_placeholders  = $this->_get_placeholders( 'items' );
            $item_replaces      = $this->_get_item_replaces( $product );
            $cart_items_markup .= str_replace( $item_placeholders, $item_replaces, $item_template );

            $checkout_value = $checkout_value + ( _bwec_get_product_real_price( $product->ID )*$product->cart_quantity );
        }

        $cart_file_dir = BWEC_INCLUDES_PATH . '/views/shopping-cart.php';
        if ( !file_exists( $cart_file_dir ) )
            return;

        $cart_template      = file_get_contents( $cart_file_dir );
        $cart_placeholders  = $this->_get_placeholders( 'cart' );
        $cart_replaces      = $this->_get_cart_replaces( $cart_items_markup, $checkout_value );
        $cart_markup        = str_replace( $cart_placeholders, $cart_replaces, $cart_template );

        return $cart_markup;
    }

    /**
     * Calculates the time to cookie expires according with the $days necessary.
     *
     * @param int $days The number of days that the cookie is valid.
     * @return int The lifetime to cookie expires.
     */
    private function _cookie_lifetime( $days )
    {
        return time()+3600*24*$days;
    }

    /**
     * Check if the current page in exhibition is a correct cart page.
     *
     * @global object $post
     * @return bool True if is the cart page.
     */
    public function _is_cart_page()
    {
        global $post;

        if ( empty( $post ) ) {
            $pageinfo = get_page( bwec_get_cart_page_id() );
            return strpos( $_SERVER['REQUEST_URI'] , $pageinfo->post_name ) != 0;
        }

        return ( $post->ID == bwec_get_cart_page_id() );
    }

    /**
     * Check if the post_id informed is a valid bwec product post.
     *
     * @param int $post_id The post id to check
     * @return bool True if is a valid bwec product post or false in otherwise.
     */
    private function _is_valid_post_id( $postid )
    {
        $product_post = get_post( $postid );

        if ( !is_object( $product_post ) )
            return false;

        return ( $product_post->post_type == BWEC_CPT_PRODUCTS );
    }

    /**
     * Placeholders from cart page.
     *
     * @param type $local
     * @return string
     */
    private function _get_placeholders( $local )
    {
        switch ( $local ) {
            case 'items' :
                $placeholders = array(
                    '{cart.items.attribute.title}',
                    '{cart.items.product.link}',
                    '{cart.items.product.thumb}',
                    '{cart.items.product.title}',
                    '{cart.items.product.cod}',
                    '{cart.items.product.quantity}',
                    '{cart.items.update.less.link}',
                    '{cart.items.update.more.link}',
                    '{cart.items.product.unit.price}',
                    '{cart.items.product.total.price}',
                    '{cart.items.product.delete.link}'
                );
            break;

            case 'cart' :
                $placeholders = array(
                    '{shopping.cart.form.action}',
                    '{table.head.label.product}',
                    '{table.head.label.cod}',
                    '{table.head.label.quantity}',
                    '{table.head.label.unit.price}',
                    '{table.head.label.total.price}',
                    '{table.finish.label.subtotal.price}',
                    '{table.finish.label.checkout}',
                    '{table.body.items}',
                    '{table.finish.mode.pac.checked}',
                    '{table.finish.mode.sedex.checked}',
                    '{table.finish.mode.sedex10.checked}',
                    '{table.finish.label.continue}',
                    '{table.finish.label.total.price}',
                    '{table.finish.cep.value.markup}',
                    '{table.finish.zipcode.value}',
                    '{table.finish.zipcode.action.url}',
                    '{table.finish.pd.data.fields}'
                );
            break;
        }

        return $placeholders;
    }

    /**
     * Replaces from each item in shopping cart.
     *
     * @param type $item
     * @return type
     */
    private function _get_item_replaces( $item )
    {
        $cart_items_attribute_title     = esc_attr( strip_tags( $item->post_title ) );
        $cart_items_product_link        = get_permalink( $item->ID );
        $cart_items_product_thumb       = ( has_post_thumbnail( $item->ID ) )
                                          ? get_the_post_thumbnail( $item->ID, 'thumbnail', array( 'alt' => get_the_title().' | '.get_bloginfo() ) )
                                          : apiki_default_product_thumbnail( 'small', false );
        $cart_items_product_title       = trim( esc_html( $item->post_title ) );
        $cart_items_product_cod         = intval( $item->ID );
        $cart_items_product_quantity    = intval( $item->cart_quantity );
        $cart_items_update_less_link    = bwec_get_update_quantity_link( $item->ID, $item->cart_quantity-1 );
        $cart_items_update_more_link    = bwec_get_update_quantity_link( $item->ID, $item->cart_quantity+1 );
        $cart_items_product_unit_price  = sprintf( 'R$ %s', number_format( _bwec_get_product_real_price( $item->ID ), 2, ',', '.' ) );
        $cart_items_product_total_price = sprintf( 'R$ %s', number_format( _bwec_get_product_real_price( $item->ID )*$item->cart_quantity , 2, ',', '.' ) );
        $cart_items_product_delete_link = bwec_get_delete_link( $item->ID );

        return array(
            $cart_items_attribute_title,
            $cart_items_product_link,
            ( !empty($cart_items_product_thumb) ) ? $cart_items_product_thumb : '<span class="product-no-thumb">&nbsp;</span>' ,
            $cart_items_product_title,
            $cart_items_product_cod,
            $cart_items_product_quantity,
            $cart_items_update_less_link,
            $cart_items_update_more_link,
            $cart_items_product_unit_price,
            $cart_items_product_total_price,
            $cart_items_product_delete_link
        );
    }

    /**
     * General replaces from shopping cart.
     *
     * @global type $obj_bwec_shipping
     * @global object $obj_bwec_wpdb
     * @param type $items
     * @param type $checkout_value
     * @return type
     */
    private function _get_cart_replaces( $items, $checkout_value )
    {
        global $obj_bwec_shipping, $obj_bwec_wpdb;

        $shipping = $obj_bwec_shipping->calculate_shipping( $checkout_value );
        $mode     = $obj_bwec_wpdb->get_meta_value( 'CEP_MODE', $this->get_cart_id() );
        if ( empty( $mode ) )
            $mode = 'sedex';

        $shopping_cart_form_action          = 'https://www.pagamentodigital.com.br/checkout/pay/';
        $table_head_label_product           = __( 'Product', BWEC_TEXTDOMAIN );
        $table_head_label_cod               = __( 'Reference', BWEC_TEXTDOMAIN );
        $table_head_label_quantity          = __( 'Quantity', BWEC_TEXTDOMAIN );
        $table_head_label_unit_price        = __( 'Unit price', BWEC_TEXTDOMAIN );
        $table_head_label_total_price       = __( 'Total price', BWEC_TEXTDOMAIN );
        $table_finish_label_subtotal_price  = sprintf( 'R$ %s', number_format( $checkout_value, 2, ',', '.' ) );
        $table_finish_label_checkout        = bwec_get_text_to_checkout();
        $table_body_items                   = $items;
        $table_finish_mode_pac_checked      = ( $mode == 'pac' ) ? 'checked="checked"' : '' ;
        $table_finish_mode_sedex_checked    = ( $mode == 'sedex' ) ? 'checked="checked"' : '' ;
        $table_finish_mode_sedex10_checked  = ( $mode == 'sedex10' ) ? 'checked="checked"' : '' ;
        $shipping_value                     = ( $shipping['valor'] > 0 ) ? 'R$ ' . number_format( $shipping['valor'], 2, ',', '.' ) : 'Frete grátis' ;
        $table_finish_cep_value_markup      = ( !empty( $shipping )
                                                ? sprintf( '<span class="frete">Frete: R$ %s</span>
                                                            <input type="hidden" id="frete-calculado" value="1" />', number_format( $shipping['valor'], 2, ',', '.' ) )
                                                : sprintf( '<span class="frete">Frete: R$ %s</span>', number_format( 0, 2, ',', '.' ) ) );
        $table_finish_label_continue        = get_site_url();
        $table_finish_label_total_price     = ( !empty( $shipping )
                                                ? sprintf( 'R$ %s', number_format( $checkout_value + $shipping['valor'], 2, ',', '.' ) )
                                                : sprintf( 'R$ %s', number_format( $checkout_value, 2, ',', '.' ) ) );
        $table_finish_zipcode_value         = $shipping['cep'];
        $table_finish_zipcode_action_url    = add_query_arg( array( 'step' => 2 ), bwec_get_cart_page_permalink() );
        $table_finish_pd_data_fields        = $this->_get_pd_form_data( $shipping['valor'] );

        return array(
            $shopping_cart_form_action,
            $table_head_label_product,
            $table_head_label_cod,
            $table_head_label_quantity,
            $table_head_label_unit_price,
            $table_head_label_total_price,
            $table_finish_label_subtotal_price,
            $table_finish_label_checkout,
            $table_body_items,
            $table_finish_mode_pac_checked,
            $table_finish_mode_sedex_checked,
            $table_finish_mode_sedex10_checked,
            $table_finish_label_continue,
            $table_finish_label_total_price,
            $table_finish_cep_value_markup,
            $table_finish_zipcode_value,
            $table_finish_zipcode_action_url,
            $table_finish_pd_data_fields
        );
    }

    /**
     * Form with data to send to Pagamento Digital.
     *
     * @global object $obj_bwec_wpdb
     * @param type $shipping_value
     * @return type
     */
    private function _get_pd_form_data( $shipping_value )
    {
        global $obj_bwec_wpdb;

        $products = $this->get_products();

        $products_markup = '';
        foreach ( (array)$products as $key => $prod )
        {
            $products_markup .= sprintf( '<input name="produto_codigo_%d" type="hidden" value="%d" />', $key+1, $prod->ID );
            $products_markup .= sprintf( '<input name="produto_descricao_%d" type="hidden" value="%s" />', $key+1, $prod->post_title );
            $products_markup .= sprintf( '<input name="produto_qtde_%d" type="hidden" value="%d" />', $key+1, $prod->cart_quantity );
            $products_markup .= sprintf( '<input name="produto_valor_%d" type="hidden" value="%f" />', $key+1, _bwec_get_product_real_price( $prod->ID ) );
        }

        return '
            <input name="email_loja" type="hidden" value="'.get_option( BWEC_OPTION_PAGDIGITAL_EMAIL ).'" />
            '.$products_markup.'
            <input name="tipo_integracao" type="hidden" value="PAD" />
            <input name="frete" type="hidden" value="'.$shipping_value.'" />
            <input name="tipo_frete" type="hidden" value="'.$obj_bwec_wpdb->get_meta_value( 'CEP_MODE' , $this->get_cart_id() ).'" />

            <input name="redirect" type="hidden" value="true" />
            <input name="redirect_time" type="hidden" value="0" />
            <input name="url_retorno" type="hidden" value="'.add_query_arg( array( 'step' => 3 ), bwec_get_cart_page_permalink() ).'" />';
    }
}

global $obj_bwec_cart;
$obj_bwec_cart = new BWEC_Shopping_Cart();