<?php 
get_header();

get_sidebar();
?>
<div id="content" class="grid_9 omega">
    
    <?php $promocoes = get_option( BP_WP_ECOMMERCE_OPTION_PROMOCOES ); ?>

    <?php if ( !empty( $promocoes ) ) : ?>
    
    <div id="featured">
        <div class="featured-content">
            <h3 class="title-box">Promoções</h3>

            <button class="prev disabled"><<</button>
            <button class="next">>></button>
            <div class="slide-home">
                <ul>
                    <?php foreach( (array)$promocoes as $product_id => $product_title ) : ?>
                    <li>
                        <div class="featured-thumb">
                            <a href="<?php echo get_permalink( $product_id ) ?>" title="<?php echo esc_attr( stripslashes( $product_title ) ); ?>">
                                <?php 
                                    if ( has_post_thumbnail( $product_id ) ) : 
                                        echo get_the_post_thumbnail( $product_id, 'featured', array( 'alt' => get_the_title( $product_id ).' | '.get_bloginfo() ) );
                                    else : 
                                        apiki_default_product_thumbnail( 'large' );
                                    endif;
                                ?>
                            </a>
                        </div>   <!-- /featured-thumb -->
                        

                        <div class="featured-description">
                            <h2 class="title">
                                <a href="<?php echo get_permalink( $product_id ); ?>" title="<?php echo esc_attr( stripslashes( $product_title ) ); ?>">
                                    <?php apiki_limit_the_title( $product_id, 30 ); ?>
                                </a>
                            </h2>

                            <span class="price">
                                <?php if ( get_post_meta( $product_id, BWEC_POSTMETA_PRICE_OFF, true ) > 0.00 ) : ?>
                                <span class="price-cut">de: R$ <?php echo bwec_get_product_price( $product_id ); ?></span>
                                POR: R$ <?php echo bwec_get_product_price_off( $product_id ); ?></span>
                                <?php else : ?>
                                POR: R$ <?php echo bwec_get_product_price( $product_id ); ?></span>
                                <?php endif; ?>
                            <?php if ( !bwec_product_was_added( $product_id ) ) : ?>
                            <p class="btn">
                                <a href="<?php echo get_permalink( $product_id ); ?>" title="Comprar <?php echo esc_attr( stripslashes( $product_title ) ); ?>"><span>Comprar</span></a>
                            </p>
                            <?php else : printf( '<img id="product-added" src="%s" alt="produto já adicionado ao carrinho" />', BP_WP_ECOMMERCE_ASSETS_URL.'/images/btn-adicionado.png' ); endif; ?>
                        </div>  <!-- /featured-description -->
                    </li>
                    <?php 
                            $exclude[] = $product_id; 
                        endforeach; 
                    ?>
                </ul>
            </div><!--  ./slide-home -->
        </div>
    </div><!-- / featured -->
    <?php endif; ?>
        
    <?php get_template_part( 'box' , 'other-products' ); ?>

</div><!-- /content -->
<?php get_footer(); ?>