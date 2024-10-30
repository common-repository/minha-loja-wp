<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php apiki_tag_title(); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
        <?php wp_head(); ?>
        <!--[if IE 7]>
            <style type="text/css">
                #header #box-search input[type="submit"] { margin-top: 1px; right: 2px; }
                #car-buy .form-cep input[type="text"] { margin-right: 180px; }
                #car-buy .calcular-frete { margin-top: -37px }
                #car-buy .form-cep label { position: relative; top: -7px; }
                #car-buy .close-request .btn input[type="submit"] { width: 290px; padding: 0 22px 14px 23px; }
                #car-buy table tr td, #car-buy table tr th { border-bottom: 1px solid #cccccc !important; }
                #footer ul.nav-footer { position: relative; top: 15px; }
                #product-list .btn { height: 30px; }
                #product-list .btn a { position: relative; top: -1px; }
                .opcao-envio { margin-top: -25px !important; }
                .button-frete { position: relative !important: top: 30px !important; }
                .form-cep span { position: relative !important: top: -6px !important; }
            </style>
        <![endif]-->
        <!--[if IE 8]>
            <style type="text/css">
                #header #box-search input[type="submit"] { right: 2px; }
                #product-list .btn { height: 30px; }#product-list .btn a { position: relative; top: -1px; }
            </style>
        <![endif]-->
    </head>
    <body <?php body_class(); ?>>
        <div id="wrapper" class="container_12 clearfix">
            <div id="header" class="grid_12">
                <h1>
                    <a href="<?php echo BP_WP_ECOMMERCE_SITE_URL; ?>" title="<?php bloginfo(); ?> - <?php bloginfo( 'description' ); ?>">
                        <?php
                            global $obj_bwec_options;
                            $obj_bwec_options->the_store_brand();
                        ?>
                    </a>
                </h1>

                <form action="<?php echo BP_WP_ECOMMERCE_SITE_URL; ?>" id="box-search">
                    <input type="text" value="Faça sua pesquisa" id="s" name="s" />
                    <input type="submit" value="buscar" title="Buscar produtos" id="search-button" />
                </form>

                <div id="car-itens">
                    <?php $quant = bwec_get_quantity_products_in_cart(); ?>
                    <a href="<?php echo bwec_get_cart_page_permalink(); ?>" title="Ver carrinho" <?php echo ( $quant ) ? 'class="car-full"' : '' ; ?>><?php echo ( $quant > 0 ) ? $quant : ''; ?> <?php echo bwec_get_quantity_products_in_cart_item_or_itens(); ?> <span>R$ <?php echo bwec_get_cart_total_price(); ?></span></a>
                </div><!-- / car-itens  -->

                <?php
                wp_nav_menu( array(
                    'theme_location'    => 'header',
                    'container'         => '',
                    'menu_class'        => 'nav',
                    'fallback_cb'       => false
                ) );
                ?>
                
            </div><!-- / header -->

            <div id="wrapper-content" class="grid_12">