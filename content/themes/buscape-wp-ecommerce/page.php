<?php get_header(); ?>

<div id="content" class="grid_12 alpha">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
     <h1 class="title-box"><?php the_title(); ?></h1>
    <div id="post-<?php the_ID(); ?>" class="apiki-wp-content">
        <?php the_content(); ?>
    </div><!-- / post-<?php the_ID(); ?> -->
    <?php endwhile; endif; ?>

</div><!-- /content -->

<?php get_footer(); ?>