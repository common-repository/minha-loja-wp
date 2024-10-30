<?php
    global $query_string, $wp_query;

    query_posts( $query_string.'&post_type='.BWEC_CPT_PRODUCTS );
    
    if ( have_posts() ) :
        while( have_posts() ) : the_post();
            global $wp_query;
?>
            <div class="grid_3 <?php echo ( $wp_query->current_post%3 === 0 ) ? 'alpha' : ''; ?><?php echo ( ($wp_query->current_post+1)%3 === 0 ) ? 'omega' : ''; ?>">
                
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
            </div><!-- ./ grid_3 -->
            <?php echo ( ($wp_query->current_post+1)%3 === 0 ) ? '<div class="clear"></div>' : ''; ?>
            
<?php endwhile; 

        // Pagination
        global $wp_query;

        $big = 999999999; // need an unlikely integer

        echo '<div class="navigation">';
        echo paginate_links( array(
            'base' => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
            'format' => '?paged=%#%',
            'current' => max( 1, get_query_var('paged') ),
            'total' => $wp_query->max_num_pages,
            'mid_size'  => 3
        ) );
        echo '</div>';
        
    
    else : // have_posts()
        echo '<p>Nenhum produto encontrado.</p>';
    endif;
?>