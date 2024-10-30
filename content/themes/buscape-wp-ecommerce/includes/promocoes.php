<?php
define( 'BP_WP_ECOMMERCE_OPTION_PROMOCOES', 'bp_wp_ecommerce_promocoes' );

if ( isset( $_GET['q'] ) ) {
    if ( !function_exists( 'add_action' ) ) {
        $wp_root = '../../../../../../..';
        if (file_exists($wp_root.'/wp-load.php')) {
                require_once($wp_root.'/wp-load.php');
        } else {
                require_once($wp_root.'/wp-config.php');
        }
    }
    
    // Encodes < > & " '. Will never double encode entities and convert string to lower
    $q = strtolower( esc_attr( $_GET["q"] ) );

    if ( !$q ) return;

    $posts = get_posts( "numberposts=1000&post_type=bwec-products" );

    foreach ( (array)$posts as $post ) :
        if ( strpos( strtolower( $post->post_title ), $q ) !== false )
            printf( "<span style='display:none'>%d|</span>%s\n", intval( $post->ID ), esc_html( $post->post_title ) );
    endforeach;
    
    exit;
}

class BP_WP_Ecommerce_Promocoes
{
    private $_base_page = 'edit.php?post_type=bwec-products&page=bp-wp-ecommerce-promocoes';
    
    public function __construct() 
    {
        add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
        add_action( 'admin_init', array( &$this, 'save_options' ) );
    }
    
    /**
     * Cria todas as opções no menu de administração do WordPress.
     */
    public function admin_menu()
    {
        add_submenu_page( 'edit.php?post_type=bwec-products', 'Produtos em promoção', 'Promoções', 'edit_posts', 'bp-wp-ecommerce-promocoes', array( &$this, 'admin_page' ) );
    }

    /**
     * Formulário de opções para netmotocar na administração.
     */
    public function admin_page()
    {
        $promocoes = get_option( BP_WP_ECOMMERCE_OPTION_PROMOCOES );
        
        $message = '';
        if ( isset( $_GET['message'] ) ) :
            switch ( $_GET['message'] ) {
                case 'success':
                    $message = '<div id="message" class="updated"><p>Produtos em promoção atualizados.</p></div>';
                break;

                default:
                    break;
            }
        endif;
?>
<div class="wrap">
    <h2>Produtos em promoção</h2>
    <?php echo $message; ?>
    <form action="<?php echo $this->_base_page; ?>" method="post">
        <?php wp_nonce_field( 'bp-wp-ecommerce-promocoes' ); ?>
        <div id="bp-wp-ecommerce-promocoes" class="metabox-holder">
            <div id="titlediv">
                <div id="titlewrap">
                    <label for="title" id="title-prompt-text" style="" class="hide-if-no-js">Digite o título do produto</label>
                    <input type="text" autocomplete="off" id="title" value="" tabindex="1" size="30" name="post_title">
                </div>
            </div>
        </div>
        
        <div>
            <ul id="produtos-em-promocao">
                <?php foreach( (array)$promocoes as $post_id => $post_title ) : ?>
                    <?php if( !empty( $post_title ) ) : ?>
                    <li>
                        <label>
                            <input type="checkbox" name="promocoes_itens[]" checked="checked" value="<?php echo esc_attr( $post_id . '|' . $post_title ); ?>" />
                            <?php echo esc_html( $post_title ); ?>
                        </label>
                    </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <input type="submit" class="button-primary" name="bp_wp_ecommerce_save_promocoes" value="Salvar" />
    </form>
</div> <!-- / .wrap -->
<?php
        
    }
    
    /**
     * Salva as options de netmotocar. Essa função é cahamada no hook admin_init.
     * 
     * @return type 
     */
    public function save_options()
    {
        if ( !isset( $_POST['bp_wp_ecommerce_save_promocoes'] ) )
            return;
        
        check_admin_referer( 'bp-wp-ecommerce-promocoes' );

        extract( $_POST, EXTR_SKIP );

        if ( isset( $promocoes_itens ) ) {
            foreach ( (array)$promocoes_itens as $post ) {
                $array_post         = explode('|', $post);
                $promocao_post_id    = $array_post[0];
                $promocao_post_title = $array_post[1];

                $arr_promocoes_posts[$promocao_post_id] = $promocao_post_title;
            }
            
            update_option( BP_WP_ECOMMERCE_OPTION_PROMOCOES, $arr_promocoes_posts );
            
        } else
            update_option( BP_WP_ECOMMERCE_OPTION_PROMOCOES, null );
        
        wp_redirect( $this->_base_page.'&message=success' );
    }
}

global $obj_bp_promocoes;
$obj_bp_promocoes = new BP_WP_Ecommerce_Promocoes();