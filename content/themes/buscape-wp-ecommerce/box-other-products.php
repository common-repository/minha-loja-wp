    <?php
        global $exclude;
        
        $posts_per_page = ( is_home() ) ? 3 : 4 ;
        $exclude        = ( is_singular( BWEC_CPT_PRODUCTS ) ) ? get_the_ID() : $exclude;

        $products_query = new WP_Query( array(
            'posts_per_page'    => $posts_per_page,
            'post_type'         => BWEC_CPT_PRODUCTS,
            'post__not_in'      => (array)$exclude
        ) );
        if ( $products_query->have_posts() ) :
    ?>
    <div id="product-list" class="<?php echo ( is_home() ) ? 'grid_9' : 'grid_12'; ?> alpha">
        <?php if ( is_home() ) : ?>
        <h1 class="title-box">Novidades</h1>
        <?php else : ?>
        <h3 class="title-box">Novidades</h3>
        <?php endif; ?>
        <ul>
            <?php while( $products_query->have_posts() ) : $products_query->the_post(); ?>
            <li class="grid_3 <?php echo ( $products_query->current_post == 0 ) ? 'alpha' : ''; ?><?php echo ( $products_query->current_post == ( $posts_per_page-1 ) ) ? 'omega' : ''; ?>">
                
                <div class="thumbnail">
                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                        <?php 
                            if ( has_post_thumbnail() ) :
                                the_post_thumbnail( 'listing', array( 'alt' => get_the_title().' | '.get_bloginfo() ) ); 
                            else :
                                apiki_default_product_thumbnail( 'medium' );
                            endif;
                        ?>
                    </a>
                </div><!-- / thumbnail -->
                
                <h4 class="title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php apiki_limit_the_title(); ?></a></h4>
                <span class="price">
                    <?php if ( get_post_meta( get_the_ID(), BWEC_POSTMETA_PRICE_OFF, true ) > 0.00 ) : ?>
                    <span class="price-cut">de: R$ <?php echo bwec_get_product_price(); ?></span>
                    POR: R$ <?php echo bwec_get_product_price_off(); ?></span>
                    <?php else : ?>
                    POR: R$ <?php echo bwec_get_product_price(); ?></span>
                    <?php endif; ?>
                <?php if ( !bwec_product_was_added() ) : ?>
                <p class="btn">
                    <a href="<?php the_permalink(); ?>" title="Comprar <?php the_title_attribute(); ?>"><span>Comprar</span></a>
                </p>
                <?php else : printf( '<img id="product-added" src="%s" alt="produto já adicionado ao carrinho" />', BP_WP_ECOMMERCE_ASSETS_URL.'/images/btn-adicionado-pequeno.png' ); endif; ?>
            </li>
            <?php endwhile; ?>
        </ul>

    </div><!-- /product-list -->
    <?php endif; //have_posts() ?>