<?php get_header(); ?>

<div id="content" class="grid_12 alpha">
    <h1 class="title-box">Carrinho de Compras</h1>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <div id="car-buy">
        <?php the_content(); ?>
    </div><!-- /.car-buy -->
    <?php endwhile; endif; ?>

</div><!-- /content -->
<?php get_footer(); ?>


