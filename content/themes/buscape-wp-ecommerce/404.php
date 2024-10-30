<?php get_header(); ?>
<div id="content" class="grid_12 alpha">
  <h3 class="title-box">Página não encontrada!</h3>

  <p class="page-not-found">A página não foi encontrada.<a href="<?php echo site_url(); ?>" title="Voltar">Volte para o site e continue procurando pelo produto desejado</a></p>

  <?php get_template_part( 'box' , 'other-products' ); ?>

</div><!-- /content -->
<?php get_footer(); ?>