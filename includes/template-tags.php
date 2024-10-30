<?php
/**
 * Wrapper to echo the buy button link.
 */
function bwec_the_buy_button()
{
    echo bwec_get_buy_button();
}

/**
 * Retrieves the html markup of the buy button link.
 *
 * @return string The link (<a></a>)
 */
function bwec_get_buy_button()
{
    global $post;

    $params = array(
        'bwec_action'   =>  'add',
        'bwec_prod_id'  =>  $post->ID
    );

    return sprintf( '<p class="btn"><a href="%s" title="Comprar %s"><span>%s</span></a></p>', add_query_arg( $params, bwec_get_cart_page_permalink() ), get_the_title( $post->ID ), bwec_get_text_to_buy() );
}

/**
 * Retrieves the html markup to delete a item in shop cart.
 *
 * @param int $prod_id The product id to delete.
 * @return string The tag <a>.
 */
function bwec_get_delete_link( $prod_id )
{
    $params = array(
        'bwec_action'   =>  'delete',
        'bwec_prod_id'  =>  $prod_id
    );

    return add_query_arg( $params, bwec_get_cart_page_permalink() );
}

/**
 * Retrieves the html markup to update the product quantity in shop cart.
 *
 * @param int $prod_id The product id to update.
 * @param int $quantity The new quantity for.
 * @return string The tag <a>.
 */
function bwec_get_update_quantity_link( $prod_id, $quantity )
{
    $params = array(
        'bwec_action'   =>  'update',
        'bwec_prod_id'  =>  $prod_id,
        'bwec_prod_quantity'    =>  $quantity
    );

    return add_query_arg( $params, bwec_get_cart_page_permalink() );
}

/**
 * Retrieves the text to buy button.
 *
 * @global object $obj_buscape_wp_ecommerce
 * @return string The text for the button.
 */
function bwec_get_text_to_buy()
{
    global $obj_bwec_options;

    return $obj_bwec_options->get_option( BWEC_OPTION_BUY_BUTTON_TEXT );
}

/**
 * Retrieves the text to checkout button.
 *
 * @global object $obj_buscape_wp_ecommerce
 * @return string The text for the button.
 */
function bwec_get_text_to_checkout()
{
    global $obj_bwec_options;

    return $obj_bwec_options->get_option( BWEC_OPTION_CHECKOUT_BUTTON_TEXT );
}

/**
 * Retrieves the permalink for the cart page.
 *
 * @global object $obj_buscape_wp_ecommerce
 * @return string
 */
function bwec_get_cart_page_permalink()
{
    return get_permalink( bwec_get_cart_page_id() );
}

/**
 * Retrieves the ID for the cart page.
 *
 * @global object $obj_bwec_options
 * @return int The ID of the page for shop cart.
 */
function bwec_get_cart_page_id()
{
    global $obj_bwec_options;

    return $obj_bwec_options->get_option( BWEC_OPTION_CART_PAGE );
}

/**
 * Retrieves the product price.
 *
 * @global object $post
 * @param <type> $post_id
 * @return <type>
 */
function bwec_get_product_price( $post_id = null )
{
    return _bwec_get_product_price_of_type( $post_id, BWEC_POSTMETA_PRICE );
}

/**
 * Retrieves the product price with discount.
 *
 * @global object $post
 * @param <type> $post_id
 * @return <type>
 */
function bwec_get_product_price_off( $post_id = null )
{
    return _bwec_get_product_price_of_type( $post_id, BWEC_POSTMETA_PRICE_OFF );
}

function _bwec_get_product_price_of_type( $post_id, $price_type )
{
    global $post;

    $post_id    = ( !empty( $post_id ) ) ? $post_id : $post->ID ;
    $price      = get_post_meta( $post_id, $price_type, true );
    $price      = ( !empty( $price ) ) ? $price : 0.00;

    return number_format( $price, 2, ',', '.' );
}

function _bwec_get_product_real_price( $post_id )
{
    $price_off = get_post_meta( $post_id, BWEC_POSTMETA_PRICE_OFF, true );

    if ( !empty( $price_off ) && $price_off > 0.00 )
        return $price_off;

    return get_post_meta( $post_id, BWEC_POSTMETA_PRICE, true );
}

/**
 * Retrieves the product weight.
 *
 * @global object $post
 * @param <type> $post_id
 * @return <type>
 */
function bwec_get_product_weight( $post_id = null )
{
    global $post;
    $post_id = ( isset( $post_id ) ) ? $post_id : $post->ID ;

    $weight = get_post_meta( $post_id, BWEC_POSTMETA_WEIGHT, true );

    if ( empty( $weight ) ) $weight = 0;

    return number_format( $weight, 3, ',', '.' );
}

/**
 * Retrieves the product cod.
 *
 * @global object $post
 * @param <type> $post_id
 * @return <type>
 */
function bwec_get_product_cod( $post_id = null )
{
    global $post;
    $post_id = ( isset( $post_id ) ) ? $post_id : $post->ID ;

    return get_post_meta( $post_id, BWEC_POSTMETA_CODE, true );
}

/**
 * Retrieves if product is available or unavailable.
 *
 * @global object $post
 * @param <type> $post_id
 * @return bool
 */
function bwec_product_display_portions( $post_id = null )
{
    global $post;
    $post_id = ( isset( $post_id ) ) ? $post_id : $post->ID ;

    return get_post_meta( $post_id, BWEC_POSTMETA_DISPLAY_PORTIONS, true );
}

/**
 * Retrieves how many products are in shop cart.
 *
 * @global object $obj_bwec_cart
 * @return int
 */
function bwec_get_quantity_products_in_cart()
{
    global $obj_bwec_cart;

    $quant_items = count( $obj_bwec_cart->get_products() );

    return $quant_items;
}

function bwec_get_quantity_products_in_cart_item_or_itens()
{
    global $obj_bwec_cart;

    $quant_items = count( $obj_bwec_cart->get_products() );

    if ( $quant_items == 0 )
        return 'Vazio';

    if ( $quant_items == 1 )
        return 'item';

    if ( $quant_items > 1 )
        return 'itens';
}

function bwec_get_cart_total_price()
{
    global $obj_bwec_cart;

    return number_format( $obj_bwec_cart->get_cart_total_price( false ), 2, ',', '.' );
}

function bwec_product_was_added( $post_id = null )
{
    global $obj_bwec_cart, $obj_bwec_wpdb;

    $post_id = ( !empty( $post_id ) ) ? $post_id : get_the_ID();

    return $obj_bwec_wpdb->product_was_added( $post_id, $obj_bwec_cart->get_cart_id() );
}