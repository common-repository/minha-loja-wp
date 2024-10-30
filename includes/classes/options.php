<?php
class BWEC_Options {

    public function  __construct()
    {
        add_action( 'admin_init', array( &$this, 'save_options' ) );
        add_action( 'admin_init', array( &$this, 'upload_brand' ) );
    }

    public function get_option( $option )
    {
        return get_option( $option );
    }

    public function save_options()
    {
        if ( !isset( $_POST['bwec_options_submit'] ) )
            return false;

        check_admin_referer( 'bwec-save-options' );

        extract( $_POST, EXTR_SKIP );

        if ( isset( $option_value ) ) :
            foreach ( (array)$option_value as $name => $value )
                update_option ( $name, $this->_sanitize( $name, $value ) );
        else :
            return false;
        endif;

        wp_redirect( $_SERVER['REQUEST_URI'].'&message=success' );
    }

    public function upload_brand()
    {
        if ( !isset( $_FILES['marca'] ) )
            return false;

        check_admin_referer( 'bwec-save-options' );

        if ( $_FILES['marca']['error'] == 0 ) :

            $brand_url = $this->_add_store_image();
            update_option( BWEC_OPTION_STORE_BRAND, $brand_url );

            wp_redirect( $_SERVER['REQUEST_URI'].'&message=success' );
            exit;

        endif;
    }

    public function the_store_brand( $image_src = null )
    {
        if ( is_null( $image_src ) ) :
            $brand = $this->get_option( BWEC_OPTION_STORE_BRAND );
            $image_src = $brand;
        endif;

        if ( empty( $image_src ) ) :
            bloginfo();
            return;
        endif;

        $image_path = str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $image_src );
        $image_size = getimagesize( $image_path );

        printf( '<img src="%s" %s alt="%s" />', $image_src, $image_size[3], get_bloginfo() );
    }

    public function form()
    {
        $current_form = str_replace( 'bwec-settings-', '', $_GET['page'] );
        $default_forms = array(
            'payment'       => array(
                1,
                __( 'Pagamento Digital', BWEC_TEXTDOMAIN),
                'Configuração do Pagamento Digital salva com sucesso. <a href="admin.php?page=bwec-settings-buscape">Continue para configurar o xml de produtos do Buscapé.</a>'
            ),
            'buscape'       => array(
                2,
                __( 'Buscapé', BWEC_TEXTDOMAIN ),
                'Configurações de conta e xml do Buscapé salvas com sucesso. <a href="admin.php?page=bwec-settings-ebit">Continue para configurar o e-bit em sua loja.</a>'
            ),
            'ebit'          => array(
                3,
                __( 'e-bit', BWEC_TEXTDOMAIN ),
                'Configurações do e-bit salvas com sucesso. <a href="admin.php?page=bwec-settings-ebehavior">Continue para configurar o eBehavior.</a>'
            ),
            'ebehavior'      => array(
                4,
                __( 'eBehavior', BWEC_TEXTDOMAIN ),
                'Configurações do eBehavior salvas com sucesso. <a href="admin.php?page=bwec-settings-quebarato">Continue para configurar o QueBarato! em sua loja.</a>'
            ),
            'quebarato'     => array(
                5,
                __( 'QueBarato!', BWEC_TEXTDOMAIN ),
                'Configurações do QueBarato! salvas com sucesso.  <a href="admin.php?page=bwec-settings-brand">Continue para configurar a marca da loja.</a>'
            ),
            'brand'         => array(
                6,
                __( 'Store brand', BWEC_TEXTDOMAIN ),
                'Marca da loja configurada com sucesso. <a href="admin.php?page=bwec-settings-information">Continue para configurar as informações de contato.</a>'
            ),
            'information'   => array(
                7,
                __( 'Contact information', BWEC_TEXTDOMAIN ),
                'Informações de contato salvas com sucesso. <a href="admin.php?page=bwec-settings-cart">Continue para configurar o carrinho de compras.</a>'
            ),
            'cart'          => array(
                8,
                __( 'Shopping cart', BWEC_TEXTDOMAIN ),
                'O carrinho de compras foi configurado com sucesso. <a href="admin.php?page=bwec-settings-shipping">Continue para configurar o envio.</a>'
            ),
            'shipping'      => array(
                9,
                __( 'Shipping', BWEC_TEXTDOMAIN ),
                'Os dados de envio foram configurados com sucesso. <a href="edit.php?post_type=bwec-products">Comece agora mesmo a incluir os seus produtos na loja.</a>'
            )
        );
    ?>
<div class="wrap">
    <h2><?php _e( 'Setup your store', BWEC_TEXTDOMAIN ); ?></h2>

    <?php
        $message_type = ( isset( $_GET['message'] ) ) ? $_GET['message'] : '' ;
        if ( 'success' == $message_type )
            printf( '<div id="message" class="updated"><p>%s</p></div>', $default_forms[$current_form][2] );
    ?>

    <div id="steps">
        <ul>
            <?php foreach ( $default_forms as $form => $options ) : ?>
            <li<?php echo ( $form == $current_form ) ? ' class="current-step"' : ''; ?>><span class="step"><?php echo $options[0]; ?></span><a href="admin.php?page=bwec-settings-<?php echo $form; ?>"><?php echo $options[1]; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data" id="bwec-form">
        <?php wp_nonce_field( 'bwec-save-options' ); ?>
        <table class="form-table">
            <tbody>
                <tr><th colspan="2"><h3><?php printf( '%d. %s', $default_forms[$current_form][0], $default_forms[$current_form][1] ); ?></h3></th></tr>
                <?php
                    $form_func = '_form_'.$current_form;
                    $this->$form_func();
                ?>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" class="button-primary" name="bwec_options_submit" value="<?php _e( 'Save', BWEC_TEXTDOMAIN ); ?>" id="save-<?php echo $current_form; ?>" />
        </p>
    </form>
</div>
<?php }

    private function _form_payment()
    {
?>
                <tr>
                    <th colspan="2">
                        <span class="description">
                            O Pagamento Digital é mais que um facilitador de pagamentos, é a solução completa para sua loja vender mais, com segurança e eficiência.
                            Configure abaixo o endereço de e-mail com o qual você se cadastrou no Pagamento Digital.
                        </span>
                    </th>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <strong>Já tem sua conta no Pagamento Digital? Digite seu e-mail cadastrado:</strong>
                    </th>
                    <td>
                        <input type="text" class="regular-text" id="paypal-email" name="option_value[<?php echo BWEC_OPTION_PAGDIGITAL_EMAIL; ?>]" value="<?php echo $this->get_option( BWEC_OPTION_PAGDIGITAL_EMAIL ); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <strong>Ainda não é cliente? Crie já a sua conta!</strong>
                    </th>
                    <td><a class="button" href="https://www.pagamentodigital.com.br/site/PagamentoDigital/CriarConta/" title="Cria sua conta no Pagamento Digital" target="_blank">Criar conta</a></td>
                </tr>
<?php }

    private function _form_buscape()
    {
        global $obj_bwec_buscape;
?>
                <tr>
                    <th colspan="2">
                        <span class="description">
                            O Buscapé é mais do que um comparador de preços, é uma ferramenta essencial para se consultar antes de todas as compras, pois além de comparar preços, lojas e produtos, usando o Buscapé você paga um preço justo pelo produto e pratica a Compra Consciente.
                            Configure abaixo o nome da loja no Buscapé e gere o XML de produtos.
                        </span>
                    </th>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <strong>Nome da loja:</strong>
                    </th>
                    <td>
                        <input type="text" class="regular-text" id="buscape-store" name="option_value[<?php echo BWEC_OPTION_BUSCAPE_STORE; ?>]" value="<?php echo $this->get_option( BWEC_OPTION_BUSCAPE_STORE ); ?>" />
                        <span class="description"><strong>Nome da sua loja cadastrada no Buscapé. Não pode conter apenas números.</strong></span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <strong>Ainda não é cliente? Crie já a sua conta!</strong>
                    </th>
                    <td><a class="button" href="http://negocios.buscapecompany.com.br/" title="Cria sua conta no Buscapé" target="_blank">Anuncie no Buscapé</a></td>
                </tr>
                <?php
                $store_name = get_option( BWEC_OPTION_BUSCAPE_STORE );
                if ( !empty( $store_name ) ) :
                ?>
                <tr valign="top">
                    <th scope="row"><strong>Arquivo XML para a captura dos produtos pelo Buscapé</strong></th>
                    <td>
                        <?php if ( !isset( $_POST['bwec_buscape_xml'] ) ) : ?>
                        <input type="submit" class="button-primary" name="bwec_buscape_xml" value="<?php _e( 'Generate XML', BWEC_TEXTDOMAIN ); ?>" />
                        <?php else : ?>
                        Informe a seguinte url para o Buscapé: <strong><?php echo $obj_bwec_buscape->get_file_url(); ?></strong>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endif; ?>
<?php }

    private function _form_ebit()
    {
?>
                <tr>
                    <th colspan="2">
                        <span class="description">
                            Presente no mercado brasileiro desde janeiro de 2000, a e-bit é referência no fornecimento de informações sobre e-commerce nacional.
                            Configure abaixo o ID que foi gerado para a sua loja pela e-bit.
                        </span>
                    </th>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <strong>Já tem seu cadastro e-bit? Digite seu ID:</strong>
                    </th>
                    <td>
                        <input type="text" class="regular-text" id="ebit-id" name="option_value[<?php echo BWEC_OPTION_EBIT_ID; ?>]" value="<?php echo $this->get_option( BWEC_OPTION_EBIT_ID ); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <strong>Ainda não é cliente? Associe-se já!</strong>
                    </th>
                    <td><a class="button" href="http://www.ebit.com.br/convenio_lojas/html/convenio_lojas.asp" title="Associe-se já ao e-bit" target="_blank">Associe-se</a></td>
                </tr>
<?php }

    private function _form_ebehavior()
    {
?>
                <tr>
                    <th colspan="2">
                        <span class="description">
                            Com o eBehavior sua loja virtual ganha vida e se torna uma experiência única para cada cliente.
                            Configure abaixo o seu código de parceiro do eBehavior.
                        </span>
                    </th>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <strong><label for="ebhavior-id"><?php _e( 'Já é parceiro do eBehavior? Digite seu código:', BWEC_TEXTDOMAIN ); ?></label></strong>
                    </th>
                    <td>
                        <input type="text" class="regular-text" id="ebhavior-id" name="option_value[<?php echo BWEC_OPTION_EBEHAVIOR_PARTNER_ID; ?>]" value="<?php echo $this->get_option( BWEC_OPTION_EBEHAVIOR_PARTNER_ID ); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <strong><?php _e( 'Ainda não é parceiro? Contrate agora mesmo!', BWEC_TEXTDOMAIN ); ?></strong>
                    </th>
                    <td><a class="button" href="http://www.ebehavior.com.br/ebcommerce.htm" title="Contrate agora mesmo o eBehavior" target="_blank">Contrate</a></td>
                </tr>
<?php }

    private function _form_quebarato()
    {
        $quebarato = get_option( BWEC_OPTION_QUEBARATO_USER );
        $payment_methods = (array)get_option( BWEC_OPTION_QUEBARATO_PAYMENT );
?>
                <tr>
                    <th colspan="2">
                        <span class="description">
                            O QueBarato! é o melhor lugar para comprar e vender na internet.
                            Anuncie grátis e compre com mais segurança!
                            Configure abaixo os dados de sua conta no QueBarato!
                        </span>
                    </th>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <strong>Já tem cadastro no QueBarato? Digite seu nome de usuário:</strong>
                    </th>
                    <td>
                        <input type="text" class="regular-text" id="quebarato-user" name="option_value[<?php echo BWEC_OPTION_QUEBARATO_USER; ?>]" value="<?php echo $quebarato; ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <strong>Digite a senha do usuário:</strong>
                    </th>
                    <td>
                        <input type="password" class="regular-text" id="quebarato-pass" name="option_value[<?php echo BWEC_OPTION_QUEBARATO_PASS; ?>]" value="" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <strong>Ainda não é cliente? Crie já a sua conta!</strong>
                    </th>
                    <td><a class="button" href="http://www.quebarato.com.br/cadastre-se.html" title="Cria sua conta no QueBarato!" target="_blank">Criar conta</a></td>
                </tr>
                <?php if ( !empty( $quebarato ) ) : ?>
                <tr valign="top">
                    <th scope="row">
                        <strong>Escolha as formas de pagamento aceitas nos seus anúncios no QueBarato!</strong>
                    </th>
                    <td>
                        <ul>
                            <li>
                                <input id="quebarato-payment-money" type="checkbox"
                                       name="option_value[<?php echo BWEC_OPTION_QUEBARATO_PAYMENT; ?>][]"
                                       value="/payment-method/offline/1"
                                       <?php echo ( in_array( '/payment-method/offline/1', $payment_methods ) ) ? 'checked="checked"' : '' ; ?> />
                                <label for="quebarato-payment-money">Dinheiro</label>
                            </li>
                            <li>
                                <input id="quebarato-payment-deposit" type="checkbox"
                                       name="option_value[<?php echo BWEC_OPTION_QUEBARATO_PAYMENT; ?>][]"
                                       value="/payment-method/offline/2"
                                       <?php echo ( in_array( '/payment-method/offline/2', $payment_methods ) ) ? 'checked="checked"' : '' ; ?> />
                                <label for="quebarato-payment-deposit">Depósito Bancário</label>
                            </li>
                            <li>
                                <input id="quebarato-payment-check" type="checkbox"
                                       name="option_value[<?php echo BWEC_OPTION_QUEBARATO_PAYMENT; ?>][]"
                                       value="/payment-method/offline/3"
                                       <?php echo ( in_array( '/payment-method/offline/3', $payment_methods ) ) ? 'checked="checked"' : '' ; ?> />
                                <label for="quebarato-payment-check">Cheque</label>
                            </li>
                            <li>
                                <input id="quebarato-payment-credit" type="checkbox"
                                       name="option_value[<?php echo BWEC_OPTION_QUEBARATO_PAYMENT; ?>][]"
                                       value="/payment-method/offline/4"
                                       <?php echo ( in_array( '/payment-method/offline/4', $payment_methods ) ) ? 'checked="checked"' : '' ; ?> />
                                <label for="quebarato-payment-credit">Cartão de Crédito</label>
                            </li>
                            <li>
                                <input id="quebarato-payment-sedex" type="checkbox"
                                       name="option_value[<?php echo BWEC_OPTION_QUEBARATO_PAYMENT; ?>][]"
                                       value="/payment-method/offline/5"
                                       <?php echo ( in_array( '/payment-method/offline/5', $payment_methods ) ) ? 'checked="checked"' : '' ; ?> />
                                <label for="quebarato-payment-sedex">Sedex a Cobrar</label>
                            </li>
                            <li>
                                <input id="quebarato-payment-other" type="checkbox"
                                       name="option_value[<?php echo BWEC_OPTION_QUEBARATO_PAYMENT; ?>][]"
                                       value="/payment-method/offline/6"
                                       <?php echo ( in_array( '/payment-method/offline/6', $payment_methods ) ) ? 'checked="checked"' : '' ; ?> />
                                <label for="quebarato-payment-other">A Combinar</label>
                            </li>
                        </ul>
                    </td>
                </tr>
                <?php endif; ?>
<?php }

    private function _form_brand()
    {
?>
                <tr>
                    <th colspan="2">
                        <span class="description">
                            Configure a marca da sua loja que será exibida no site.
                            A imagem que for configurada será utilizada no cabeçalho e rodapé.
                            <strong>Recomendamos é que a imagem tenha as dimensões 160 x 60.</strong>
                        </span>
                    </th>
                </tr>

                <tr>
                    <th scope="row">Imagem atual:</th>
                    <td><?php $this->the_store_brand(); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="marca">Selecione uma nova marca:</label></th>
                    <td>
                        <input type="file" id="marca" name="marca" />
                        <input type="hidden" name="action" value="bwec_store_image_upload" />
                    </td>
                </tr>
<?php

    }

    private function _form_information()
    {
        $store = $this->get_option( BWEC_OPTION_CONTACT_INFORMATION );
?>
                <tr>
                    <th colspan="2">
                        <span class="description">
                            Configure os dados de endereço da loja abaixo.
                            Os dados serão utilizados como informações no rodapé do site.
                        </span>
                    </th>
                </tr>

                <tr>
                    <th scope="row"><label for="logadouro">Logradouro:</label></th>
                    <td><input type="text" id="logadouro" name="option_value[<?php echo BWEC_OPTION_CONTACT_INFORMATION; ?>][logradouro]" class="regular-text" value="<?php echo ( isset( $store['logradouro'] ) ) ? $store['logradouro'] : '' ; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="numero">Número:</label></th>
                    <td><input type="text" id="numero" name="option_value[<?php echo BWEC_OPTION_CONTACT_INFORMATION; ?>][numero]" class="regular-text" value="<?php echo ( isset( $store['numero'] ) ) ? $store['numero'] : '' ; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="complemento">Complemento:</label></th>
                    <td><input type="text" id="complemento" name="option_value[<?php echo BWEC_OPTION_CONTACT_INFORMATION; ?>][complemento]" class="regular-text" value="<?php echo ( isset( $store['complemento'] ) ) ? $store['complemento'] : '' ; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="bairro">Bairro:</label></th>
                    <td><input type="text" id="bairro" name="option_value[<?php echo BWEC_OPTION_CONTACT_INFORMATION; ?>][bairro]" class="regular-text" value="<?php echo ( isset( $store['bairro'] ) ) ? $store['bairro'] : '' ; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="cidade">Cidade:</label></th>
                    <td><input type="text" id="cidade" name="option_value[<?php echo BWEC_OPTION_CONTACT_INFORMATION; ?>][cidade]" class="regular-text" value="<?php echo ( isset( $store['cidade'] ) ) ? $store['cidade'] : '' ; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="uf">UF:</label></th>
                    <td><input type="text" id="uf" maxlength="2" name="option_value[<?php echo BWEC_OPTION_CONTACT_INFORMATION; ?>][uf]" class="small-text" value="<?php echo ( isset( $store['uf'] ) ) ? $store['uf'] : '' ; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="fone">Fone:</label></th>
                    <td><input type="text" id="fone" name="option_value[<?php echo BWEC_OPTION_CONTACT_INFORMATION; ?>][fone]" value="<?php echo ( isset( $store['fone'] ) ) ? $store['fone'] : '' ; ?>" /></td>
                </tr>
<?php
    }

    private function _form_cart()
    {
?>
                <tr>
                    <th colspan="2">
                        <span class="description">
                            Configure abaixo as informações para o carrinho de compras da sua loja.
                        </span>
                    </th>
                </tr>

                <tr valign="top">
                    <th scope="row">
                        <label for="text-to-buy"><?php _e( 'Text to add to cart', BWEC_TEXTDOMAIN ); ?></label>
                    </th>
                    <td>
                        <input type="text" id="text-to-buy" name="option_value[<?php echo BWEC_OPTION_BUY_BUTTON_TEXT; ?>]" class="regular-text" value="<?php echo $this->get_option( BWEC_OPTION_BUY_BUTTON_TEXT ); ?>" />
                        <span class="description"><?php _e( 'Text to add to cart button. Used to add products to the cart.', BWEC_TEXTDOMAIN ); ?></span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="text-to-checkout"><?php _e( 'Text to checkout button', BWEC_TEXTDOMAIN ); ?></label>
                    </th>
                    <td>
                        <input type="text" id="text-to-checkout" name="option_value[<?php echo BWEC_OPTION_CHECKOUT_BUTTON_TEXT; ?>]" class="regular-text" value="<?php echo $this->get_option( BWEC_OPTION_CHECKOUT_BUTTON_TEXT ); ?>" />
                        <span class="description"><?php _e( 'Text to checkout button. Used to close shopping cart.', BWEC_TEXTDOMAIN ); ?></span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="cart-page"><?php _e( 'Page to cart', BWEC_TEXTDOMAIN ); ?></label>
                    </th>
                    <td>
                        <?php wp_dropdown_pages( 'name=option_value['.BWEC_OPTION_CART_PAGE.']&selected='. $this->get_option( BWEC_OPTION_CART_PAGE ).'&show_option_none='.__( 'Select it...', BWEC_TEXTDOMAIN ) ); ?>
                        <span class="description"><?php _e( 'Choose the page to display the shop cart. Use the follow shortcode in this page: [buscape-wp-ecommerce-cart]', BWEC_TEXTDOMAIN ); ?></span>
                    </td>
                </tr>
<?php }

    private function _form_shipping()
    {
?>
                <tr>
                    <th colspan="2">
                        <span class="description">
                            Configure abaixo as informações específicas para o cálculo do frete dos pedidos.
                            Esses dados são de extrema importância para o correto cálculo do frete.
                            Se algum dos dados não for informado ou estiver inconsistente, o cálculo do frete poderá não ser realizado.
                        </span>
                    </th>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="cep"><?php _e( 'Source Zip Code', BWEC_TEXTDOMAIN ); ?></label>
                    </th>
                    <td>
                        <input type="text" id="cep" name="option_value[<?php echo BWEC_OPTION_SOURCE_ZIPCODE; ?>]" value="<?php echo $this->get_option( BWEC_OPTION_SOURCE_ZIPCODE ); ?>" />
                        <span class="description"><?php _e( 'Store zipcode.', BWEC_TEXTDOMAIN ); ?></span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="package-type"><?php _e( 'Package type', BWEC_TEXTDOMAIN ); ?></label>
                    </th>
                    <td>
                        <select name="option_value[<?php echo BWEC_OPTION_CORREIO_FORMAT ?>]" id="package-type">
                            <?php $selected = $this->get_option( BWEC_OPTION_CORREIO_FORMAT ); ?>
                            <option value="1" <?php selected( $selected, 1 ); ?>>Formato caixa/pacote</option>
                            <option value="2" <?php selected( $selected, 2 ); ?>>Formato rolo/prisma</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="package-length"><?php _e( 'Package Length', BWEC_TEXTDOMAIN ); ?></label>
                    </th>
                    <td>
                        <input type="text" id="package-length" name="option_value[<?php echo BWEC_OPTION_CORREIO_LENGTH; ?>]" class="small-text" value="<?php echo $this->get_option( BWEC_OPTION_CORREIO_LENGTH ); ?>" />
                        <span class="description"><strong>Comprimento da encomenda em centímetros. Deve ter no mínimo 18 e no máximo 90 centímetros.</strong></span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="package-height"><?php _e( 'Package Height', BWEC_TEXTDOMAIN ); ?></label>
                    </th>
                    <td>
                        <input type="text" id="package-height" name="option_value[<?php echo BWEC_OPTION_CORREIO_HEIGHT; ?>]" class="small-text" value="<?php echo $this->get_option( BWEC_OPTION_CORREIO_HEIGHT ); ?>" />
                        <span class="description"><strong>Altura da encomenda em centímetros. Deve ter no mínimo 2 e no máximo 90 centímetros. Não pode ser maior que o comprimento.</strong></span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="package-width"><?php _e( 'Package Width', BWEC_TEXTDOMAIN ); ?></label>
                    </th>
                    <td>
                        <input type="text" id="package-width" name="option_value[<?php echo BWEC_OPTION_CORREIO_WIDTH; ?>]" class="small-text" value="<?php echo $this->get_option( BWEC_OPTION_CORREIO_WIDTH ); ?>" />
                        <span class="description"><strong>Largura da encomenda em centímetros. Deve ter no mínimo 5 e no máximo 90 centímetros. Caso o comprimento seja menor que 25 centímetros a largura deve ser maior que 11 centímetros.</strong></span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="package-diameter"><?php _e( 'Package Diameter', BWEC_TEXTDOMAIN ); ?></label>
                    </th>
                    <td>
                        <input type="text" id="package-diameter" name="option_value[<?php echo BWEC_OPTION_CORREIO_DIAMETER; ?>]" class="small-text" value="<?php echo $this->get_option( BWEC_OPTION_CORREIO_DIAMETER ); ?>" />
                        <span class="description"><strong>Diâmetro da encomenda em centímetros. Deve ter no mínimo 5 e no máximo 90 centímetros.</strong></span>
                    </td>
                </tr>
<?php }

    private function _add_store_image()
    {
        if ( !function_exists( 'wp_handle_upload' ) )
            require_once ABSPATH . '/wp-admin/includes/file.php';

        $upload_path = wp_upload_dir();

        // action deve ser um campo oculto no formulário
        $new_file = wp_handle_upload( $_FILES['marca'], array( 'action' => 'bwec_store_image_upload' ) );

        // Creates the new image sizes
        $brand_url = image_resize( $new_file['file'], 160,  60 );

        if ( is_wp_error( $brand_url ) )
            $brand_url = $new_file['file'];

        return str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, $brand_url );
    }

    private function _sanitize( $name, $value )
    {
        switch ( $name ) {
            case BWEC_OPTION_SOURCE_ZIPCODE :
                $value = preg_replace( '/[^\d]/', '', $value );
                break;

            case BWEC_OPTION_QUEBARATO_PASS :
                if ( empty( $value ) )
                    $value = get_option( BWEC_OPTION_QUEBARATO_PASS );
                else
                    $value = base64_encode( $value );
                break;

            default:
                break;
        }

        return $value;
    }
}

global $obj_bwec_options;
$obj_bwec_options = new BWEC_Options();
