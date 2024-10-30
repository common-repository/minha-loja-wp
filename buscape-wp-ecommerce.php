<?php
/*
    Plugin Name: Minha Loja WP
    Plugin URI: http://wordpress.org/extend/plugins/minha-loja-wp/
    Description: O Minha Loja WP é um plugin para montar e gerenciar o seu e-commerce.
    Version: 2.3.1
    Author: Apiki WordPress
*/

/**
 * Buscape_WP_Ecommerce class, the major class for the plugin functionalities.
 */
class Buscape_WP_Ecommerce
{
    /**
     * Class construct.
     */
    public function __construct()
    {
        $this->_define_constants();

        $this->_load_files();

        add_action( 'activate_'.BWEC_PLUGIN_FOLDER_NAME.'/buscape-wp-ecommerce.php', array( &$this, 'install' ) );
        add_action( 'init', array( &$this, 'create_custom_types' ) );
        add_action( 'init' , array( &$this, 'load_textdomain' ), 1 );
        add_action( 'admin_menu', array( &$this, 'menu' ) );
        add_action( 'admin_print_styles', array( &$this, 'admin_styles' ) );
        add_action( 'admin_print_scripts', array( &$this, 'admin_scripts' ) );
        add_action( 'admin_notices', array( &$this, 'admin_notice_settings' ) );
        add_action( 'wp_enqueue_scripts', array( &$this, 'site_scripts' ) );

        add_shortcode( 'buscape-wp-ecommerce-button', array( $this, 'display_buy_button' ) );

        // Register a new area to content themes in WordPress
        register_theme_directory( WP_PLUGIN_DIR . '/'.BWEC_PLUGIN_FOLDER_NAME.'/content/themes' );
    }

    /**
     * Plugin initial setup.
     */
    public function install()
    {
        global $obj_bwec_wpdb;

        $this->_set_roles();

        $this->_set_defaults();

        $obj_bwec_wpdb->create_tables();

        // Set the new theme
        switch_theme( 'buscape-wp-ecommerce', 'buscape-wp-ecommerce' );

        // Number of posts per page default in this theme
        update_option( 'posts_per_page', 9 );
    }

    /**
     * Display the menu in wordpress admin dashboard.
     */
    public function menu()
    {
        global $obj_bwec_options, $obj_bwec_uninstall;

        add_menu_page( __( 'Minha Loja WP Dashboard', BWEC_TEXTDOMAIN ), __( 'Minha Loja WP', BWEC_TEXTDOMAIN ), BWEC_CAP_READ_DASHBOARD, 'bwec-settings-payment', array( &$obj_bwec_options, 'form' ) );
        add_submenu_page( 'bwec-settings-payment' , __( 'Minha Loja WP Pagamento Digital', BWEC_TEXTDOMAIN ), __( 'Pagamento Digital', BWEC_TEXTDOMAIN), BWEC_CAP_MANAGE_OPTIONS, 'bwec-settings-payment', array( &$obj_bwec_options, 'form' ) );
        add_submenu_page( 'bwec-settings-payment' , __( 'Minha Loja WP Buscapé integration', BWEC_TEXTDOMAIN ), __( 'Buscapé', BWEC_TEXTDOMAIN), BWEC_CAP_MANAGE_OPTIONS, 'bwec-settings-buscape', array( &$obj_bwec_options, 'form' ) );
        add_submenu_page( 'bwec-settings-payment' , __( 'Minha Loja WP e-bit', BWEC_TEXTDOMAIN ), __( 'e-bit', BWEC_TEXTDOMAIN ), BWEC_CAP_MANAGE_OPTIONS, 'bwec-settings-ebit', array( &$obj_bwec_options, 'form' ) );
        add_submenu_page( 'bwec-settings-payment' , __( 'Minha Loja WP eBehavior', BWEC_TEXTDOMAIN ), __( 'eBehavior', BWEC_TEXTDOMAIN ), BWEC_CAP_MANAGE_OPTIONS, 'bwec-settings-ebehavior', array( &$obj_bwec_options, 'form' ) );
        add_submenu_page( 'bwec-settings-payment' , __( 'Minha Loja WP QueBarato!', BWEC_TEXTDOMAIN ), __( 'QueBarato!', BWEC_TEXTDOMAIN ), BWEC_CAP_MANAGE_OPTIONS, 'bwec-settings-quebarato', array( &$obj_bwec_options, 'form' ) );
        add_submenu_page( 'bwec-settings-payment' , __( 'Minha Loja WP Store brand', BWEC_TEXTDOMAIN ), __( 'Store brand', BWEC_TEXTDOMAIN ), BWEC_CAP_MANAGE_OPTIONS, 'bwec-settings-brand', array( &$obj_bwec_options, 'form' ) );
        add_submenu_page( 'bwec-settings-payment' , __( 'Minha Loja WP Contact information', BWEC_TEXTDOMAIN ), __( 'Contact information', BWEC_TEXTDOMAIN ), BWEC_CAP_MANAGE_OPTIONS, 'bwec-settings-information', array( &$obj_bwec_options, 'form' ) );
        add_submenu_page( 'bwec-settings-payment' , __( 'Minha Loja WP Shopping Cart', BWEC_TEXTDOMAIN ), __( 'Shopping cart', BWEC_TEXTDOMAIN ), BWEC_CAP_MANAGE_OPTIONS, 'bwec-settings-cart', array( &$obj_bwec_options, 'form' ) );
        add_submenu_page( 'bwec-settings-payment' , __( 'Minha Loja WP Shipping', BWEC_TEXTDOMAIN ), __( 'Shipping', BWEC_TEXTDOMAIN ), BWEC_CAP_MANAGE_OPTIONS, 'bwec-settings-shipping', array( &$obj_bwec_options, 'form' ) );
        add_submenu_page( 'bwec-settings-payment' , __( 'Uninstall Minha Loja WP', BWEC_TEXTDOMAIN ), __( 'Uninstall', BWEC_TEXTDOMAIN ), BWEC_CAP_MANAGE_OPTIONS, 'bwec-settings-uninstall', array( &$obj_bwec_uninstall, 'form' ) );
    }

    /**
     * This function create the custom post types and taxonomies uses by plugin.
     */
    public function create_custom_types()
    {
        register_post_type( BWEC_CPT_PRODUCTS, array(
            'labels'                =>  array(
                'name'                  =>  __( 'Products', BWEC_TEXTDOMAIN ),
                'singular_name'         =>  __( 'Product', BWEC_TEXTDOMAIN ),
                'add_new'               =>  __( 'Add new', BWEC_TEXTDOMAIN ),
                'add_new_item'          =>  __( 'Add new product', BWEC_TEXTDOMAIN ),
                'edit_item'             =>  __( 'Edit product', BWEC_TEXTDOMAIN ),
                'new_item'              =>  __( 'New product', BWEC_TEXTDOMAIN ),
                'view_item'             =>  __( 'See product', BWEC_TEXTDOMAIN ),
                'search_items'          =>  __( 'Search products', BWEC_TEXTDOMAIN ),
                'not_found'             =>  __( 'No product found', BWEC_TEXTDOMAIN ),
                'not_found_in_trash'    =>  __( 'No product found in the trash', BWEC_TEXTDOMAIN )
            ),
            'description'           =>  __( 'Products from your virtual store', BWEC_TEXTDOMAIN ),
            'public'                =>  true,
            'hierarchical'          =>  false,
            'menu_position'         =>  5,
            'register_meta_box_cb'  =>  'bwec_create_metaboxes',
            'supports'              =>  array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
            'rewrite'               =>  array( 'slug' => __( 'products', BWEC_TEXTDOMAIN ) )
        ));

        register_taxonomy( BWEC_TAX_CATEGORY, BWEC_CPT_PRODUCTS, array(
            'labels'        =>  array(
                'name'          =>  __( 'Categories', BWEC_TEXTDOMAIN ),
                'singular_name' =>  __( 'Category', BWEC_TEXTDOMAIN ),
            ),
            'public'        =>  true,
            'hierarchical'  =>  true,
            'rewrite'       =>  array( 'slug' => __( 'categories', BWEC_TEXTDOMAIN ) )
        ) );
    }

    /**
     * Register the styles used in admin page of plugin.
     */
    public function admin_styles()
    {
        wp_enqueue_style( 'bwec-admin-styles', BWEC_STYLES_URL . '/admin_styles.css', array(), filemtime( WP_PLUGIN_DIR . '/minha-loja-wp/assets/css/admin_styles.css' ) );

        $post_type = 'post';

        if ( isset( $_GET['post'] ) ) :
            $obj_post = get_post( $_GET['post'] );
            $post_type = $obj_post->post_type;
        elseif ( isset( $_GET['post_id'] ) ) :
            $obj_post = get_post( $_GET['post_id'] );
            $post_type = $obj_post->post_type;
        elseif ( isset( $_GET['post_type'] ) ) :
            $post_type = $_GET['post_type'];
        endif;

        if ( $post_type == BWEC_CPT_PRODUCTS ) :
?>
<style type="text/css">
    #gallery-settings {display: none !important}
</style>
<?php
        endif;
    }

    /**
     * Register the styles used in admin page of plugin.
     */
    public function admin_scripts()
    {
        wp_enqueue_script( 'bwec-admin-scripts', BWEC_SCRIPTS_URL . '/admin_scripts.js', array( 'jquery' ), filemtime( WP_PLUGIN_DIR . '/minha-loja-wp/assets/javascript/admin_scripts.js' ), true );
    }

    public function site_scripts()
    {
        if ( is_admin() ) return false;

        wp_enqueue_script( 'bwec-scripts', BWEC_SCRIPTS_URL . '/site_scripts.js', array( 'jquery' ), filemtime( WP_PLUGIN_DIR . '/minha-loja-wp/assets/javascript/site_scripts.js' ), true );
    }

    /**
     * Load the textdomain for plugin localization. I18n.
     */
    public function load_textdomain()
    {
        load_plugin_textdomain( BWEC_TEXTDOMAIN, false, BWEC_LANG_PATH );
    }

    /**
     * Function to display the buy button case use a shortcode.
     */
    public function display_buy_button()
    {
        return bwec_get_buy_button();
    }

    /**
     * Show admin notices when the settings are not defined
     */
    public function admin_notice_settings()
    {
        $setting        = get_option( BWEC_OPTION_SOURCE_ZIPCODE );
        $is_plugin_page = strpos( esc_url( $_SERVER['REQUEST_URI'] ), 'page=bwec-settings' );

        if ( $is_plugin_page || !empty( $setting ) )
            return;

        $notice  = '<p>';
        $notice .= sprintf( '<strong>O plugin Minha Loja WP necessita de configurações antes de ser utilizado. <a href="admin.php?page=bwec-settings-payment">Ir para as configurações do plugin</a>.</strong>' );
        $notice .= '</p>';

        echo '<div class="error fade">' . $notice . '</div>';
    }

    /**
     * Define the constants used in plugin
     */
    private function _define_constants()
    {
        if ( !defined( 'BWEC_PLUGIN_FOLDER_NAME' ) )
            define( 'BWEC_PLUGIN_FOLDER_NAME', 'minha-loja-wp' );

        if ( !defined( 'BWEC_CLASSES_PATH' ) )
            define( 'BWEC_CLASSES_PATH', WP_PLUGIN_DIR . '/'.BWEC_PLUGIN_FOLDER_NAME.'/includes/classes' );

        if ( !defined( 'BWEC_INCLUDES_PATH' ) )
            define( 'BWEC_INCLUDES_PATH', WP_PLUGIN_DIR . '/'.BWEC_PLUGIN_FOLDER_NAME.'/includes' );

        if ( !defined( 'BWEC_LANG_PATH' ) )
            define( 'BWEC_LANG_PATH', '/'.BWEC_PLUGIN_FOLDER_NAME.'/content/languages' );

        if ( !defined( 'BWEC_STYLES_URL' ) )
            define( 'BWEC_STYLES_URL', WP_PLUGIN_URL . '/'.BWEC_PLUGIN_FOLDER_NAME.'/assets/css' );

        if ( !defined( 'BWEC_SCRIPTS_URL' ) )
            define( 'BWEC_SCRIPTS_URL', WP_PLUGIN_URL . '/'.BWEC_PLUGIN_FOLDER_NAME.'/assets/javascript' );

        if ( !defined( 'BWEC_IMAGES_URL' ) )
            define( 'BWEC_IMAGES_URL', WP_PLUGIN_URL . '/'.BWEC_PLUGIN_FOLDER_NAME.'/assets/images' );

        if ( !defined( 'BWEC_TEXTDOMAIN' ) )
            define( 'BWEC_TEXTDOMAIN', 'buscape-wp-ecommerce' );

        if ( !defined( 'BWEC_POSTMETA_CODE' ) )
            define( 'BWEC_POSTMETA_CODE', '_bwec_product_code' );

        if ( !defined( 'BWEC_POSTMETA_PRICE' ) )
            define( 'BWEC_POSTMETA_PRICE', '_bwec_product_price' );

        if ( !defined( 'BWEC_POSTMETA_PRICE_OFF' ) )
            define( 'BWEC_POSTMETA_PRICE_OFF', '_bwec_product_price_off' );

        if ( !defined( 'BWEC_POSTMETA_WEIGHT' ) )
            define( 'BWEC_POSTMETA_WEIGHT', '_bwec_product_weight' );

        if ( !defined( 'BWEC_POSTMETA_DISPLAY_PORTIONS' ) )
            define( 'BWEC_POSTMETA_DISPLAY_PORTIONS', '_bwec_product_display_portions' );

        if ( !defined( 'BWEC_POSTMETA_QUEBARATO_PUBLISH' ) )
            define( 'BWEC_POSTMETA_QUEBARATO_PUBLISH', '_bwec_product_quebarato_publish' );

        if ( !defined( 'BWEC_POSTMETA_QUEBARATO_URL' ) )
            define( 'BWEC_POSTMETA_QUEBARATO_URL', '_bwec_product_quebarato_url' );

        if ( !defined( 'BWEC_CPT_PRODUCTS' ) )
            define( 'BWEC_CPT_PRODUCTS', 'bwec-products' );

        if ( !defined( 'BWEC_TAX_CATEGORY' ) )
            define( 'BWEC_TAX_CATEGORY', 'bwec-categories' );

        if ( !defined( 'BWEC_CAP_MANAGE_OPTIONS' ) )
            define( 'BWEC_CAP_MANAGE_OPTIONS', 'bwec_manage_options' );

        if ( !defined( 'BWEC_CAP_READ_DASHBOARD' ) )
            define( 'BWEC_CAP_READ_DASHBOARD', 'bwec_read_dashboard' );

        if ( !defined( 'BWEC_OPTION_BUY_BUTTON_TEXT' ) )
            define( 'BWEC_OPTION_BUY_BUTTON_TEXT', 'bwec_buy_button_text' );

        if ( !defined( 'BWEC_OPTION_CHECKOUT_BUTTON_TEXT' ) )
            define( 'BWEC_OPTION_CHECKOUT_BUTTON_TEXT', 'bwec_checkout_button_text' );

        if ( !defined( 'BWEC_OPTION_CART_PAGE' ) )
            define( 'BWEC_OPTION_CART_PAGE', 'bwec_cart_page' );

        if ( !defined( 'BWEC_OPTION_SOURCE_ZIPCODE' ) )
            define( 'BWEC_OPTION_SOURCE_ZIPCODE', 'bwec_source_zipcode' );

        if ( !defined( 'BWEC_OPTION_CORREIO_FORMAT' ) )
            define( 'BWEC_OPTION_CORREIO_FORMAT', 'bwec_correio_format' );

        if ( !defined( 'BWEC_OPTION_CORREIO_LENGTH' ) )
            define( 'BWEC_OPTION_CORREIO_LENGTH', 'bwec_correio_length' );

        if ( !defined( 'BWEC_OPTION_CORREIO_HEIGHT' ) )
            define( 'BWEC_OPTION_CORREIO_HEIGHT', 'bwec_correio_height' );

        if ( !defined( 'BWEC_OPTION_CORREIO_WIDTH' ) )
            define( 'BWEC_OPTION_CORREIO_WIDTH', 'bwec_correio_width' );

        if ( !defined( 'BWEC_OPTION_CORREIO_DIAMETER' ) )
            define( 'BWEC_OPTION_CORREIO_DIAMETER', 'bwec_correio_diameter' );

        if ( !defined( 'BWEC_OPTION_PAGDIGITAL_EMAIL' ) )
            define( 'BWEC_OPTION_PAGDIGITAL_EMAIL', 'bwec_pagdigital_email' );

        if ( !defined( 'BWEC_OPTION_EBEHAVIOR_PARTNER_ID' ) )
            define( 'BWEC_OPTION_EBEHAVIOR_PARTNER_ID', 'bwec_ebehavior_id' );

        if ( !defined( 'BWEC_OPTION_EBIT_ID' ) )
            define( 'BWEC_OPTION_EBIT_ID', 'bwec_ebit_id' );

        if ( !defined( 'BWEC_OPTION_BUSCAPE_STORE' ) )
            define( 'BWEC_OPTION_BUSCAPE_STORE', 'bwec_buscape_store_name' );

        if ( !defined( 'BWEC_OPTION_QUEBARATO_USER' ) )
            define( 'BWEC_OPTION_QUEBARATO_USER', 'bwec_quebarato_user' );

        if ( !defined( 'BWEC_OPTION_QUEBARATO_PASS' ) )
            define( 'BWEC_OPTION_QUEBARATO_PASS', 'bwec_quebarato_pass' );

        if ( !defined( 'BWEC_OPTION_QUEBARATO_PAYMENT' ) )
            define( 'BWEC_OPTION_QUEBARATO_PAYMENT', 'bwec_quebarato_payment' );

        if ( !defined( 'BWEC_OPTION_BUSCAPE_STORE' ) )
            define( 'BWEC_OPTION_BUSCAPE_STORE', 'bwec_buscape_store_name' );

        if ( !defined( 'BWEC_OPTION_CONTACT_INFORMATION' ) )
            define( 'BWEC_OPTION_CONTACT_INFORMATION', 'bwec_contact_information' );

        if ( !defined( 'BWEC_OPTION_STORE_BRAND' ) )
            define( 'BWEC_OPTION_STORE_BRAND', 'bwec_store_brand' );
    }

    /**
     * Load files necessary to plugin run
     */
    private function _load_files()
    {
        if ( file_exists( BWEC_CLASSES_PATH . '/metaboxes.php' ) )
            require  BWEC_CLASSES_PATH . '/metaboxes.php';

        if ( file_exists( BWEC_CLASSES_PATH . '/wpdb.php' ) )
            require  BWEC_CLASSES_PATH . '/wpdb.php';

        if ( file_exists( BWEC_CLASSES_PATH . '/shopping-cart.php' ) )
            require  BWEC_CLASSES_PATH . '/shopping-cart.php';

        if ( file_exists( BWEC_CLASSES_PATH . '/shipping.php' ) )
            require  BWEC_CLASSES_PATH . '/shipping.php';

        if ( file_exists( BWEC_CLASSES_PATH . '/options.php' ) )
            require  BWEC_CLASSES_PATH . '/options.php';

        if ( file_exists( BWEC_CLASSES_PATH . '/ebehavior.php' ) )
            require  BWEC_CLASSES_PATH . '/ebehavior.php';

        if ( file_exists( BWEC_CLASSES_PATH . '/buscape.php' ) )
            require  BWEC_CLASSES_PATH . '/buscape.php';

        if ( file_exists( BWEC_CLASSES_PATH . '/quebarato.php' ) )
            require  BWEC_CLASSES_PATH . '/quebarato.php';

        if ( file_exists( BWEC_CLASSES_PATH . '/uninstall.php' ) )
            require  BWEC_CLASSES_PATH . '/uninstall.php';

        if ( file_exists( BWEC_INCLUDES_PATH . '/template-tags.php' ) )
            require  BWEC_INCLUDES_PATH . '/template-tags.php';
    }

    /**
     * Creates the plugin capabilities.
     */
    private function _set_roles()
    {
        $role = get_role( 'administrator' );
        if ( !$role->has_cap( BWEC_CAP_MANAGE_OPTIONS ) )
            $role->add_cap( BWEC_CAP_MANAGE_OPTIONS );
        if ( !$role->has_cap( BWEC_CAP_READ_DASHBOARD ) )
            $role->add_cap( BWEC_CAP_READ_DASHBOARD );
    }

    /**
     * Set the defaults values of options and create pages necessary.
     */
    private function _set_defaults()
    {
        $cart_page = get_page_by_path( 'carrinho-de-compras' );

        if ( is_object( $cart_page ) ) :
            $cart_page_id = $cart_page->ID;
        else :
            // Insert a default page to products listing
            $cart_page_id = wp_insert_post( array(
                                'post_type'     => 'page',
                                'post_title'    => 'Carrinho de compras',
                                'post_content'  => '[buscape-wp-ecommerce-cart]',
                                'post_status'   => 'publish'
                            ));
        endif;

        // Set default options values
        update_option( BWEC_OPTION_BUY_BUTTON_TEXT, 'Comprar' );
        update_option( BWEC_OPTION_CHECKOUT_BUTTON_TEXT, 'Finalizar Pedido' );
        update_option( BWEC_OPTION_CART_PAGE, $cart_page_id );
    }
}

$obj_buscape_wp_ecommerce = new Buscape_WP_Ecommerce();
