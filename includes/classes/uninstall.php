<?php

class BWEC_Uninstall {

    public function __construct()
    {
        add_action( 'admin_init', array( &$this, 'uninstall' ) );
    }

    public function form()
    {
?>
<div class="wrap">
    <h2><?php _e( 'Uninstall your store', BWEC_TEXTDOMAIN ); ?></h2>

    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="bwec-form">
        <?php wp_nonce_field( 'bwec-uninstall' ); ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th>
                        <span class="description">
                            Por padrão ao desinstalar sua loja WP todas as opções do wordpress
                            salvas pelo plugin também serão excluídas, bem como todas as
                            tabelas adicionais criadas no banco de dados. Todos os
                            produtos cadastrados na loja também serão excluídos.
                        </span>
                    </th>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" id="bwec-uninstall-options" name="bwec_uninstall_options" checked="checked" />
                        <label for="bwec-uninstall-options">Excluir todas as options do WordPress geradas pelo plugin.</label>
                        <div class="uninstall-details">
                            <ol>
                            <?php
                                $options = $this->_get_options_names();
                                foreach ( (array)$options as $option )
                                    echo "<li>$option->option_name</li>";
                            ?>
                            </ol>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" id="bwec-uninstall-tables" name="bwec_uninstall_tables" checked="checked" />
                        <label for="bwec-uninstall-tables">Excluir do banco de dados todas as tabelas criadas pelo plugin.</label>
                        <div class="uninstall-details">
                            <ol>
                                <?php
                                $tables = $this->_get_tables_names();
                                foreach ( (array)$tables as $table_name )
                                    echo "<li>$table_name</li>";
                            ?>
                            </ol>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" id="bwec-uninstall-products" name="bwec_uninstall_products" checked="checked" />
                        <label for="bwec-uninstall-products">Excluir do WordPress todos os produtos cadastrados.</label>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" id="bwec-uninstall" class="button-primary" name="bwec_uninstall" value="<?php _e( 'Uninstall and deactivate', BWEC_TEXTDOMAIN ); ?>" />
        </p>
    </form>
</div>
<?php
    }

    public function uninstall()
    {
        if ( !isset( $_POST['bwec_uninstall'] ) )
            return;

        check_admin_referer( 'bwec-uninstall' );

        if ( isset( $_POST['bwec_uninstall_options'] ) )
            $this->_delete_options();

        if ( isset( $_POST['bwec_uninstall_tables'] ) )
            $this->_drop_tables();

        if ( isset( $_POST['bwec_uninstall_products'] ) )
            $this->_delete_products();

        $this->_plugin_deactive();

    }

    private function _delete_options()
    {
        $options = $this->_get_options_names();

        foreach ( (array) $options as $option )
            delete_option ( $option->option_name );
    }

    private function _drop_tables()
    {
        global $wpdb;

        $tables = $this->_get_tables_names();

        foreach ( (array) $tables as $table_name )
            $wpdb->query( $wpdb->prepare( "DROP TABLE IF EXISTS $table_name" ) );
    }

    private function _delete_products()
    {
        $all_products = get_posts( 'post_type='.BWEC_CPT_PRODUCTS.'&posts_per_page=-1' );

        foreach ( (array) $all_products as $product )
            wp_delete_post ( $product->ID );
    }

    private function _plugin_deactive()
    {
        $plugin_file    = BWEC_PLUGIN_FOLDER_NAME . '/buscape-wp-ecommerce.php';
//        $url            = wp_nonce_url( 'plugins.php?action=deactivate&plugin=' . $plugin_file, 'deactivate-plugin_' . $plugin_file );
        deactivate_plugins( $plugin_file );
        $url = admin_url( 'plugins.php?deactivate=true' );
        wp_redirect( $url );
    }

    private function _get_options_names()
    {
        global $wpdb;

        $query = "SELECT option_name
                    FROM $wpdb->options
                    WHERE option_name like 'bwec_%'";

        return $wpdb->get_results( $query );
    }

    private function _get_tables_names()
    {
        global $wpdb;

        return array( $wpdb->prefix.'bwec_meta', $wpdb->prefix.'bwec_shopping_cart' );
    }

}

global $obj_bwec_uninstall;
$obj_bwec_uninstall = new BWEC_Uninstall();