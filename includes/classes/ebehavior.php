<?php

class BWEC_eBehavior{

    public function __construct()
    {
        if ( is_admin() ) return;
        
        add_action( 'wp_footer', array( &$this, 'print_vitrines' ), 1, 11 );
        add_action( 'wp_footer', array( &$this, 'print_scripts' ), 1, 12 );
    }

    public function print_scripts()
    {
        $partner_id = get_option( BWEC_OPTION_EBEHAVIOR_PARTNER_ID );

        if ( empty( $partner_id ) )
            return;

        echo "\n \t<!-- Integração eBehavior -->";
        echo "\n \t" . '<script type="text/javascript" language="javascript">';
        echo "\n \t\t" . "try {
                    (function() {
                        var e7rTrack = document.createElement('script');
                        e7rTrack.type = 'text/javascript';
                        e7rTrack.setAttribute('async', 'true');
                        e7rTrack.src = ((\"https:\" == document.location.protocol) ?\"https://ssltrack.\" : \"http://track.\") + \"e7r.com.br/aspx/tracking.ashx?parceiro=$partner_id\";
                        document.documentElement.firstChild.appendChild(e7rTrack);
                    })();
                } catch(err) {} ";
        echo $this->_get_e7r_attributes();
        echo "\n \t" . '</script>';
        echo "\n \t <!-- Fim do script eBehavior --> \n \t";
    }
    
    public function print_vitrines()
    {
        echo "\n \t<!-- Script de vitrines do eBehavior -->";
        
        if ( is_home() ) :
        
            echo "\n \t" . '<script type="text/javascript">';
            echo "\n \t\t" . 'jQuery(document).ready(function(){
            jQuery(\'<div id="dive7rCenterMiddle"></div>\').insertAfter(\'.featured-content\');
            jQuery(\'<div id="dive7rCenterBottom"></div>\').insertAfter(\'#product-list\');
            jQuery(\'<div id="dive7rCenterTop"></div>\').insertBefore(\'.featured-content\');
        } );';
            echo "\n \t" . '</script>';
        
        elseif ( is_singular( BWEC_CPT_PRODUCTS ) ) :
            
            echo "\n \t" . '<script type="text/javascript">';
            echo "\n \t\t" . 'jQuery(document).ready(function(){
            jQuery(\'<div id="dive7rCenterBottom"></div>\').insertAfter(\'#content\');
            jQuery(\'<div id="dive7rCenterMiddle"></div>\').insertAfter(\'.breadcrumbs\');
            jQuery(\'<div id="dive7rCenterTop"></div>\').insertBefore(\'#product-detail\');
        } );';
            echo "\n \t" . '</script>';
        
        elseif ( is_page( bwec_get_cart_page_id() ) ) :
            
            echo "\n \t" . '<script type="text/javascript">';
            echo "\n \t\t" . 'jQuery(document).ready(function(){
            jQuery(\'<div id="dive7rCenterTop"></div>\').insertBefore(\'.grid_12 .alpha\');
        } );';
            echo "\n \t" . '</script>';
            
        else :
            
            echo "\n \t" . '<script type="text/javascript">';
            echo "\n \t\t" . 'jQuery(document).ready(function(){
            jQuery(\'<div id="dive7rCenterTop"></div>\').insertBefore(\'.grid_9 .alpha\');
            jQuery(\'<div id="dive7rCenterBottom"></div>\').insertAfter(\'.grid_9 .alpha\');
        } );';
            echo "\n \t" . '</script>';
            
        endif;
        
        echo "\n \t<!-- Fim do script de vitrines do eBehavior -->";
        
    }

    private function _get_e7r_attributes()
    {
        global $term, $obj_bwec_cart;

        if ( is_home() ) :

            $attr_name['pageName'] = "Home";
            $attr_name['funcName'] = "home";

        elseif ( is_tax( BWEC_TAX_CATEGORY ) ) :

            $term_data = get_term_by( 'slug', $term, BWEC_TAX_CATEGORY );
            if ( $term_data->parent == 0 ) :
                $attr_name['pageName'] = "Categoria";
                $attr_name['funcName'] = "department";
            else :
                $attr_name['pageName'] = "Categoria";
                $attr_name['funcName'] = "category";
            endif;

        elseif ( is_singular( BWEC_CPT_PRODUCTS ) ) :

            $attr_name['pageName'] = "Produto";
            $attr_name['funcName'] = "product";

        elseif ( is_search() ) :

            $attr_name['pageName'] = "Busca";
            $attr_name['funcName'] = "search";

        elseif ( is_page( bwec_get_cart_page_id() ) ) :

            $step = isset( $_GET['step'] ) ? intval( $_GET['step'] ) : 1 ;

            if ( $step == 1 ) :
                if ( !$obj_bwec_cart->_is_cart_page() ) return;
                $attr_name['pageName'] = "Carrinho";
                $attr_name['funcName'] = "basket";
            elseif ( $step == 3 ) :
                if ( !isset( $_POST['id_transacao'] ) ) return;
                $attr_name['pageName'] = "Confirmacao de Pedido";
                $attr_name['funcName'] = "order";
            endif;

        else :
            return;
        endif;

        printf( "\n \t\t" . 'e7rNavigation = {};' .
                "\n \t\t" . 'e7rNavigation.PageName = "%s";' .
                "\n \t\t" . 'e7rNavigation.ShopperID = "%s";' .
                "\n \t\t" . 'e7rNavigation.StoreType = "1";',
                $attr_name['pageName'], $obj_bwec_cart->get_cart_id() );

        $func_name = '_get_e7r_' . $attr_name['funcName'];
        $this->$func_name();
    }

    private function _get_e7r_home()
    {
        // Na home entram apenas os atributos padrão.
    }

    private function _get_e7r_department()
    {
        global $term;

        $term_data = get_term_by( 'slug', $term, BWEC_TAX_CATEGORY );

        printf( "\n \t\t" . 'e7rNavigation.CodParentCategory = "0";' .
                "\n \t\t" . 'e7rNavigation.CodCategory = "%d";' .
                "\n \t\t" . 'e7rNavigation.CategoryName = "%s";',
                $term_data->term_id, $term_data->name );
    }

    private function _get_e7r_category()
    {
        global $term;

        $term_data = get_term_by( 'slug', $term, BWEC_TAX_CATEGORY );

        printf( "\n \t\t" . 'e7rNavigation.CodParentCategory = "%d";' .
                "\n \t\t" . 'e7rNavigation.CodCategory = "%d";' .
                "\n \t\t" . 'e7rNavigation.CategoryName = "%s";',
                $term_data->parent, $term_data->term_id, $term_data->name );
    }

    private function _get_e7r_product()
    {
        global $post;

        $categories = get_the_terms( $post->ID, BWEC_TAX_CATEGORY );
        foreach ( (array)$categories as $term ) :
            if ( $term->parent == 0 ) :
                $cod_parent_category = $term->term_id;
            else :
                $cod_category = $term->term_id;
                $category_name = $term->name;
            endif;
        endforeach;

        $cod_product        = $post->ID;
        $price_product      = get_post_meta( $post->ID, BWEC_POSTMETA_PRICE, true );
        $price_promotion    = get_post_meta( $post->ID, BWEC_POSTMETA_PRICE_OFF, true );
        $promotion          = ( !empty( $price_promotion ) and $price_promotion != '0.00' ) ? true : false;
        $price_promotion    = ( empty( $price_promotion ) || $price_promotion == '0.00' ) ? $price_product : $price_promotion;
        $thumb_id           = get_post_thumbnail_id( $post->ID );
        $image_url          = wp_get_attachment_image_src( $thumb_id, 'featured' );
        $installment_price  = apiki_get_parcelas( _bwec_get_product_real_price( $post->ID ) );

        printf( "\n \t\t" . 'e7rNavigation.CodParentCategory = "%1$d";' .
                "\n \t\t" . 'e7rNavigation.CodCategory = "%2$d";' .
                "\n \t\t" . 'e7rNavigation.CategoryName = "%3$s";' .
                "\n \t\t" . 'e7rNavigation.CodProduct = "%4$d";' .
                "\n \t\t" . 'e7rNavigation.ProductName = "%5$s";' .
                "\n \t\t" . 'e7rNavigation.ProductShortDescription = "%6$s";' .
//                "\n \t\t" . 'e7rNavigation.KeyWords = "Livros, Ciência, Livro Aquicultura na Prática";' .
                "\n \t\t" . 'e7rNavigation.PriceProduct = "%7$s";' .
                "\n \t\t" . 'e7rNavigation.Promotion = "%8$s";' .
                "\n \t\t" . 'e7rNavigation.PricePromotion = "%9$s";' .
//                "\n \t\t" . 'e7rNavigation.StockProduct = "1";' .
                "\n \t\t" . 'e7rNavigation.ImageURL = "%10$s";' .
                "\n \t\t" . 'e7rNavigation.AvailableProduct = "true";' .
                "\n \t\t" . 'e7rNavigation.Installment = "12";' .
                "\n \t\t" . 'e7rNavigation.InstallmentPrice = "%11$s";' .
                "\n \t\t" . 'e7rNavigation.InterestRate = "1,99";',
                $cod_parent_category,
                $cod_category,
                $category_name,
                $cod_product,
                $post->post_title,
                preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", " ", $post->post_excerpt ), // Remove as quebras de linhas
                number_format( $price_product, 2, ',', '' ),
                ( $promotion ) ? 'true' :'false',
                number_format( $price_promotion, 2, ',', '' ),
                $image_url[0],
                number_format( $installment_price[12], 2, ',', '' )
            );
    }

    private function _get_e7r_search()
    {
        printf( "\n \t\t" . 'e7rSearch = {};' .
                "\n \t\t" . 'e7rSearch.Keyword = "%s";' .
                "\n \t\t" . 'if(window.tracking!=undefined) window.tracking.sendSearch(null,null,e7rSearch);',
                get_search_query() );
    }

    private function _get_e7r_basket()
    {
        if ( !isset( $_SESSION['bwec_added_id'] ) )
            return printf( "\n \t\t" . 'if(window.tracking!=undefined)
                            window.tracking.sendBasket(null, null, e7rBasketItem);' );

        $product_added = intval( $_SESSION['bwec_added_id'] );

        printf( "\n \t\t" . 'e7rBasketItem = {};' .
                "\n \t\t" . 'e7rBasketItem.CodProduct = "%1$d";' .
                "\n \t\t" . 'e7rBasketItem.Name = "%2$s";' .
                "\n \t\t" . 'e7rBasketItem.Price = "%3$s";' .
                "\n \t\t" . 'e7rBasketItem.Quantity = "%4$d";' .
                "\n \t\t" . 'if(window.tracking!=undefined)
                            window.tracking.sendBasket(null, null, e7rBasketItem);',
                $_SESSION['bwec_added_id'],
                get_post_field( 'post_title', $_SESSION['bwec_added_id'] ),
                number_format( _bwec_get_product_real_price( $_SESSION['bwec_added_id'] ), 2, ',', '' ),
                ( isset( $_SESSION['bwec_added_quantity'] ) ) ? intval( $_SESSION['bwec_added_quantity'] ) : 1
        );

        unset( $_SESSION['bwec_added_id'] );
        unset( $_SESSION['bwec_added_quantity'] );
    }

    private function _get_e7r_order()
    {
        extract( $_POST, EXTR_SKIP );
        
        for ( $i = 1; $i <= $qtde_produtos; $i++ ) :
            $product_list .= sprintf(
                    '{
                        "CodProduct": "%1$d",
                        "Price": "%2$s",
                        "Quantity": "%3$d"
                     },', $_POST['produto_codigo_'.$i],
                    number_format( $_POST['produto_valor_'.$i], 2, ',', '' ),
                    $_POST['produto_qtde_'.$i]
            );
        endfor;

        printf( "\n \t\t" . 'e7rOrder = {};' .
                "\n \t\t" . 'e7rOrder.CodOrder = "%1$d";' .
                "\n \t\t" . 'e7rOrder.Date = "%2$s";' .
                "\n \t\t" . 'e7rOrder.Freight = "%3$s";' .
                "\n \t\t" . 'e7rOrder.Installment = "%4$d";' .
                "\n \t\t" . 'e7rOrder.ST = "%5$s";' .
                "\n \t\t" . 'e7rOrder.City = "%6$s";' .
                "\n \t\t" . 'e7rOrder.Neighborhood = "%7$s";' .
                "\n \t\t" . 'e7rOrder.PaymentMethod = "%8$s";' .

                "\n \t\t" . 'e7rOrder.ProductList = [%9$s];' .

                "\n \t\t" . 'if(window.tracking!=undefined)
                            window.tracking.sendConfirmedOrder(e7rOrder);',
                intval( $id_pedido ), $data_transacao, number_format( $frete, 2, ',', '.' ),
                $parcelas, utf8_encode( $cliente_estado ), utf8_encode( $cliente_cidade ), utf8_encode( $cliente_bairro ), utf8_encode( $tipo_pagamento ),
                $product_list
            );
    }


}

global $obj_bwec_ebehavior;
$obj_bwec_ebehavior = new BWEC_eBehavior();