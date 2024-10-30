<?php 
    get_header();
    
    get_sidebar();
?>
    <div id="content" class="grid_9 omega">

        <div id="product-list" class="grid_9 alpha">
            <h1 class="title-box">Busca: <?php echo get_search_query(); ?></h1>
            
            <?php if ( function_exists( 'apiki_breadcrumbs' ) ) apiki_breadcrumbs(); ?>

            <?php get_template_part( 'content' , 'products-listing' ); ?>

        </div><!-- /product-list -->

    </div><!-- /content -->
<?php get_footer(); ?>