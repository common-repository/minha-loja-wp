<?php
/**
 * Buscapé WP E-Commerce functions and definitions
 *
 * This file sets up the WordPress theme and provides some helpers functions.
 * Some helpers functions are used in the theme as custom template tags. Others
 * are attached to action and filter hooks in WordPress to change core
 * funcionality.
 *
 * @package WordPress
 * @subpackage Buscapé_WP_E-Commerce
 */

// Loads and define some WordPress features throught our Framework
if ( !file_exists( dirname( __FILE__ ) . '/apiki-wp-dev/apiki-wp-dev.php' ) )
    exit( 'The Apiki WordPress Theme Development Framework is required' );

require_once dirname( __FILE__ ) . '/apiki-wp-dev/apiki-wp-dev.php';
require_once dirname( __FILE__ ) . '/includes/promocoes.php';
require_once dirname( __FILE__ ) . '/includes/breadcrumbs.php';
Apiki_Wp_Theme_Dev::define_excerpt_length( 25 );

// Constants definitions
if ( !defined( 'BP_WP_ECOMMERCE_ASSETS_URL' ) )
    define( 'BP_WP_ECOMMERCE_ASSETS_URL', get_bloginfo( 'template_url' ).'/assets' );
if ( !defined( 'BP_WP_ECOMMERCE_ASSETS_PATH' ) )
    define( 'BP_WP_ECOMMERCE_ASSETS_PATH', TEMPLATEPATH.'/assets' );
if ( !defined( 'BP_WP_ECOMMERCE_SITE_URL' ) )
    define( 'BP_WP_ECOMMERCE_SITE_URL', get_site_url() );

/** Tell WordPress to run apiki_theme_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'apiki_theme_setup' );

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as
 * indicating support post thumbnails.
 *
 * @uses add_theme_support() To add support for post thumbnails and automatic
 * feed links.
 * @uses register_nav_menus() To add support for navigation menus.
 */
function apiki_theme_setup()
{
    // Add post thumbnails support
    add_theme_support( 'post-thumbnails' );

    // Add default posts and comments RSS feed links to head
    add_theme_support( 'automatic-feed-links' );

    // Hooks actions and filters to WordPress core
    add_action( 'wp_enqueue_scripts', 'apiki_theme_enqueue_scripts' );
    add_action( 'admin_enqueue_scripts', 'apiki_theme_admin_scripts' );
    add_action( 'wp_head', 'apiki_meta_tags' );
    add_filter( 'show_admin_bar', '__return_false' ); // Disable the Admin Bar.
    add_filter( 'body_class', 'apiki_classes_on_body', 10, 2 );
//    add_filter( 'walker_nav_menu_start_el', 'apiki_set_menu_link_title', 11, 4 );
    add_filter( 'wp_nav_menu_objects', 'apiki_set_menu_link_title', 11, 2 );

     // Register the menu areas
    register_nav_menu( 'header', 'Navegação primária no cabeçalho' );
    register_nav_menu( 'footer', 'Navegação secundária no rodapé' );

    /**
     * Register and define the image's size.
     *
     * In this project we need the following image dimensions:
     * 320 x 310 for the featured images in the home and in the single
     * 170 x 165 for the listing products images (the medium size)
     * 83 x 83 for the thumbnail images. This is the smallest image
     */
    $media_args = array(
        'thumbnail_size_w'  => 74,
        'thumbnail_size_h'  => 74,
        'thumbnail_crop'    => true,
        'medium_size_w'     => 0,
        'medium_size_h'     => 0,
        'large_size_w'      => 0,
        'large_size_h'      => 0
    );
    Apiki_Wp_Theme_Dev::overwrite_default_images_size( $media_args );

    Apiki_Wp_Theme_Dev::define_security_environment();

    add_image_size( 'featured', 320, 310, true );
    add_image_size( 'listing', 170, 165, true );
}

/**
 * This functions loads the JavaScript files used by this theme
 */
function apiki_theme_enqueue_scripts()
{
    /* The slide javascript. Used on featured products of home and carousel of single page of an product. */
    $slideFile = BP_WP_ECOMMERCE_ASSETS_PATH . '/javascript/slide.js';
    if ( file_exists( $slideFile ) ) :
        wp_enqueue_script( 'jquery-slide', BP_WP_ECOMMERCE_ASSETS_URL . '/javascript/slide.js', array( 'jquery' ), filemtime( $slideFile ), true );
    endif;

    /* The mask javascript. Used on field CEP on cart page. */
    $maskFile = BP_WP_ECOMMERCE_ASSETS_PATH . '/javascript/meio-mask.js';
    if ( file_exists( $maskFile ) ) :
        wp_enqueue_script( 'jquery-mask', BP_WP_ECOMMERCE_ASSETS_URL . '/javascript/meio-mask.js', array( 'jquery' ), filemtime( $maskFile ), true );
    endif;

    /* The zoom javascript. Used in a product carousel. */
    $zoomFile = BP_WP_ECOMMERCE_ASSETS_PATH . '/javascript/jzoom.js';
    if ( file_exists( $zoomFile ) ) :
        wp_enqueue_script( 'jquery-zoom', BP_WP_ECOMMERCE_ASSETS_URL . '/javascript/jzoom.js', array( 'jquery' ), filemtime( $zoomFile ), true );
    endif;

    /* The general script used in the entire site. */
    $scriptFile = BP_WP_ECOMMERCE_ASSETS_PATH . '/javascript/script.js';
    if ( file_exists( $scriptFile ) ) :
        wp_enqueue_script( 'script', BP_WP_ECOMMERCE_ASSETS_URL . '/javascript/script.js', array( 'jquery', 'jquery-slide' ), filemtime( $scriptFile ), true );
    endif;

    /* Remove script I10n from site. */
    wp_deregister_script( 'l10n' );
}

/**
 * Loads javascript in admin area
 */
function apiki_theme_admin_scripts()
{
    /* Special script used only in admin area */
    $adminScriptFile = BP_WP_ECOMMERCE_ASSETS_PATH . '/javascript/admin-script.js';
    if ( file_exists( $adminScriptFile ) )
        wp_enqueue_script( 'bp-wp-admin-script', BP_WP_ECOMMERCE_ASSETS_URL . '/javascript/admin-script.js', array( 'jquery', 'suggest' ), filemtime( $adminScriptFile ), true );
}

/**
 * Include a class in body tag case it is cart page on step one.
 *
 * @param string $classes
 * @param type $class
 * @return string
 */
function apiki_classes_on_body( $classes, $class )
{
    if ( is_page( 'carrinho-de-compras' ) && !strpos( $_SERVER['REQUEST_URI'], 'step=3' ) )
        $classes[] = 'page-carrinho';

    return $classes;
}

function apiki_get_parcelas( $capital, $num_parcelas = 12, $taxa = 1.99 )
{
    $arr_parcelas[1] = $capital;
    
    for ( $i = 2; $i <= $num_parcelas; $i++ )
        $arr_parcelas[$i] = ( pow( 1 + ( $taxa/100 ), $i )*$capital )/$i;

    return $arr_parcelas;
}

/**
 * Para otimização de SEO configura o attr title das opções do menu igual ao title.
 *
 * @param type $sorted_menu_items
 * @param type $args
 * @return type
 */
function apiki_set_menu_link_title( $sorted_menu_items, $args )
{
    foreach ( $sorted_menu_items as $menu_obj )
        $menu_obj->attr_title = esc_attr( $menu_obj->title );

    return $sorted_menu_items;
}

/**
 * Recupera a marcação HTML para as categorias da sidebar.
 */
function apiki_get_sidebar_categories()
{
    $categories_list = wp_list_categories( 'taxonomy='.BWEC_TAX_CATEGORY.'&title_li=&show_option_none=&echo=0' );

    // Altera o link title das categorias para ficar SEO friendly
    $quant = preg_match_all( '/<a .*?title=[\'\"](.*)[\'"].*?>(.*?)<\/a>/i', $categories_list, $matches );

    $categories_list = preg_replace('/<a( .*?)title=[\'\"](.*)[\'"](.*?)>(.*?)<\/a>/i', '<a$1title="$4: confira todos os produtos"$3>$4</a>', $categories_list );

    return $categories_list;
}

/**
 * Meta Tags para otimização de SEO
 */
function apiki_meta_tags()
{
    if ( is_home() ) :
        $description    = sprintf( 'Compre com segunça no %s, através do Pagamento Digital. Acesse e confira preço, especificações ténicas e muito mais.', get_bloginfo() );
        $keywords       = 'compre, segunça, pagamento, digital, detalhes, produto, preço, especificações, ténicas';
    endif;

    if ( is_tax() ) :
        $description    = sprintf( '%s no %s. Confira preços, detalhes dos produtos e compre com segurança.', single_cat_title( '', false ), get_bloginfo() );
        $keywords       = sprintf( 'comprar, barato, segurança, oferta, %s, %s, preço, detalhes, produto, compre', single_cat_title( '', false ), get_bloginfo() );
    endif;

    if ( is_singular( BWEC_CPT_PRODUCTS ) ) :
        $description    = sprintf( 'Preço de %s no %s. Saiba mais sobre o produto antes de comprar. Confira as especificações técnicas e muito mais.', get_the_title(), get_bloginfo() );
        $keywords       = sprintf( 'preço, %s, %s, produto, comprar, especificações, técnicas, informações', get_the_title(), get_bloginfo() );
    endif;

    if ( is_search() ) :
        $description    = sprintf( 'Procurando por %1$s? Encontre preços e informações técnicas sobre %1$s no %2$s. Confira!', get_search_query(), get_bloginfo() );
        $keywords       = sprintf( 'procurando, %s, encontre, preços, informações, técnicas, %s', get_search_query(), get_bloginfo() );
    endif;

    if ( is_page( bwec_get_cart_page_id() ) ) :

        $step = isset( $_GET['step'] ) ? $_GET['step'] : '';

        if ( $step == 1 ) :
            $description    = sprintf( 'Compre com segunça no %s, através do Pagamento Digital. Confirme agora o seu pedido!', get_bloginfo() );
            $keywords       = sprintf( 'compre, segunça, %s, pagamento, digital, pedido, carrinho, compras', get_bloginfo() );
        endif;

        if ( $step == 3 ) :
            $description    = sprintf( 'O seu pedido foi concluído com sucesso no %s.', get_bloginfo() );
            $keywords       = sprintf( 'pedido, %s', get_bloginfo() );
        endif;
    endif;

    printf(
            "\t" .
            '<meta name="description" content="%s" />' . "\n\t" .
            '<meta name="keywords" content="%s" />' . "\n\t",
            $description, $keywords
    );
}

/**
 * Escreve a tag title das páginas otimizadas para SEO
 */
function apiki_tag_title()
{
    if ( is_home() ) :
        $title = sprintf( '%s - Compre com Segurança', get_bloginfo() );
    endif;

    if ( is_tax() ) :
        $title = sprintf( '%s - Comprar Barato com Segurança | %s', single_cat_title( '', false ), get_bloginfo() );
    endif;

    if ( is_singular( BWEC_CPT_PRODUCTS ) ) :
        $title = sprintf( 'Preço de %s e Informações Técnicas | %s', get_the_title(), get_bloginfo() );
    endif;

    if ( is_search() ) :
        $title = sprintf( 'Resultado da Pesquisa por %s  | %s', get_search_query(), get_bloginfo() );
    endif;

    if ( is_page( bwec_get_cart_page_id() ) ) :

        $step = isset( $_GET['step'] ) ? $_GET['step'] : '';

        if ( $step == 1 ) :
            $title = sprintf( 'Carrinho de Compras | %s', get_bloginfo() );
        endif;

        if ( $step == 3 ) :
            $title = sprintf( 'Pedido Confirmado | %s', get_bloginfo() );
        endif;

        elseif ( is_page() ) :
            $title = sprintf( '%s | %s', get_the_title(), get_bloginfo() );
        endif;

    echo $title;
}

/**
 * Limita a quantidade de caracteres no título de um post.
 *
 * @param type $id
 * @param type $limit
 * @param type $display
 * @return string
 */
function apiki_limit_the_title( $id = 0, $limit = 35, $display = true )
{
    $id     = ( $id > 0 ) ? $id : get_the_ID();
    $title  = get_the_title( $id );

    if( strlen( $title ) > $limit )
        $title = mb_substr( $title, 0, $limit ) . '...';

    if ( $display )
        echo $title;
    else
        return $title;
}

/**
 * Gera o HTML de uma imagem padrão para os produtos. Deve ser chamado sempre
 * que um produto não tiver imagem destacada.
 *
 * @param string $size Tamanho que a imagem deve ser gerada.
 * @param bool $display Se para retornar ou exibir
 * @return string HTML da tag img.
 */
function apiki_default_product_thumbnail( $size = 'large', $display = true )
{
    $arr_size   = array(
        'large'     => '320x310',
        'medium'    => '170x165',
        'small'     => '74x74'
    );

    $image_src  = BP_WP_ECOMMERCE_ASSETS_URL.'/images/not-image-'.$arr_size[$size].'.jpg';
    $w_h        = explode( 'x', $arr_size[$size] );

    $output = sprintf( '<img src="%s" width="%d" height="%d" alt="Produto não possui imagem de destaque" />',
            $image_src, $w_h[0], $w_h[1] );

    if ( $display )
        echo $output;
    else
        return $output;
}