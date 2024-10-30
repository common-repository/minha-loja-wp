<?php 
    get_header();

    get_sidebar();
?>
<div id="content" class="grid_9 omega">
    
    <?php 
        if ( function_exists( 'apiki_breadcrumbs' ) ) apiki_breadcrumbs();
        
        if ( have_posts() ) : 
            the_post(); 
    ?>
    <div id="featured" class="featured-single">
        <?php
            $images = get_children( array(
                'post_parent'   => get_the_ID(),
                'post_type'     => 'attachment',
                'post_mime_type'    => 'image'
            ) );

            if ( !empty( $images ) ) :
                $first_thumb        = reset( $images );
                $img_src['feat']    = wp_get_attachment_image_src( $first_thumb->ID, 'featured' );
                $img_src['full']    = wp_get_attachment_image_src( $first_thumb->ID, 'full' );
        ?>
                <div class="featured-thumb">
                    <a href="<?php echo $img_src['full'][0]; ?>" class="jqzoom" rel='gal1'  title="<?php the_title_attribute(); ?>" >
                        <img src="<?php echo $img_src['feat'][0]; ?>"  title="<?php the_title_attribute(); ?>" />
                    </a>
                </div>
                <?php if ( count( $images ) > 3 ) : ?>
                    <button class="prev prev-detail disabled"><<</button>
                    <button class="next next-detail">>></button>
                <?php endif; //count( $images ) > 3 ?>
        <div class="slide-detail">
            <ul id="thumblist" class="clearfix" >
                <?php
                    foreach ( (array) $images as $img ) :
                        $img_src['thumb']   = wp_get_attachment_image_src( $img->ID );
                        $img_src['feat']    = wp_get_attachment_image_src( $img->ID , 'featured' );
                        $img_src['full']    = wp_get_attachment_image_src( $img->ID , 'full' );
                ?>
                <li>
                    <a href='javascript:void(0);' rel="{gallery: 'gal1', smallimage: '<?php echo $img_src['feat'][0]; ?>',largeimage: '<?php echo $img_src['full'][0]; ?>'}">
                        <img src="<?php echo $img_src['thumb'][0]; ?>" width="74" height="74" />
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div><!--  ./slide-home -->
        <?php else : // !empty( $images ) ?>
            <div class="featured-thumb">
                <?php apiki_default_product_thumbnail( 'large' ); ?>
            </div>
        <?php endif; ?>

        <div class="featured-description">

            <h1 class="title"><?php the_title(); ?></h1>
            <div class="text-excerpt"><?php the_excerpt(); ?></div>
            <p class="more-detail"><a href="#product-detail" title="Mais detalhes sobre <?php the_title_attribute(); ?>">Mais detalhes</a></p>

            <span class="price">
                <?php if ( get_post_meta( get_the_ID(), BWEC_POSTMETA_PRICE_OFF, true ) > 0.00 ) : ?>
                <span class="price-cut">de: R$ <?php echo bwec_get_product_price(); ?></span>
                POR: R$ <?php echo bwec_get_product_price_off(); ?>
                <?php else : ?>
                POR: R$ <?php echo bwec_get_product_price(); ?>
                <?php 
                    endif;
                    $preco = get_post_meta( get_the_ID(), BWEC_POSTMETA_PRICE, true );
                    $preco_off = get_post_meta( get_the_ID(), BWEC_POSTMETA_PRICE_OFF, true );
                    if ( !empty( $preco_off ) and $preco_off != '0.00' )
                        $preco = $preco_off;
                    $arr_parcelas = apiki_get_parcelas( $preco );
                    if ( bwec_product_display_portions() ) :
                ?>
                <span class="forms-payment">ou <strong>12x</strong>
                    de <strong>R$ <?php echo number_format( $arr_parcelas[12], 2, ',', '.' ); ?></strong>.
                    <a href="#subdivision">Ver formas de pagamento</a>
                </span>
                <?php endif; ?>
            </span>
            
            <?php 
                // Exibe o botão de compra ou uma imagem informativa caso o produto já tenha sido adicionado ao carrinho
                if ( !bwec_product_was_added() )
                    bwec_the_buy_button(); 
                else
                    printf( '<img id="product-added" src="%s" alt="produto já adicionado ao carrinho" />', BP_WP_ECOMMERCE_ASSETS_URL.'/images/btn-adicionado.png' );
            ?>

        </div><!-- ./ featured-description -->

    </div><!-- / featured -->

    <?php if ( bwec_product_display_portions() ) : ?>
    <div id="subdivision" class="apiki-wp-content">
        <h3 class="title-box">Formas de Pagamento</h3>
        <ul>
            <?php foreach ( (array)$arr_parcelas as $key => $value ) : ?>
            <li><?php echo $key; ?>x de <span>R$ <?php echo number_format( $value, 2, ',', '.' ); ?></span></li>
            <?php if ( $key == 4 || $key == 8 ) echo '</ul><ul>'; ?>
            <?php endforeach; ?>
        </ul>
        <span class="interest">*Juros de 1.99% a.m.. Parcelas fixas.</span>
    </div><!-- / product-detail -->
    <?php endif; ?>
    
    <div id="product-detail" class="apiki-wp-content">
        <h2 class="title-box">Detalhes do produto</h2>
        <?php the_content(); ?>
    </div><!-- / product-detail -->
    
    <?php endif; ?>

</div><!-- /content -->

<?php get_template_part( 'box' , 'other-products' ); ?>

<?php get_footer(); ?>