<?php 
    $categories_list = apiki_get_sidebar_categories();
    
    if ( !empty( $categories_list ) ) :
?>
<div id="sidebar" class="grid_3 alpha">
    <div id="sidebar-category-list">
        <ul><?php echo $categories_list; ?></ul>
    </div>
</div><!-- / sidebar -->
<?php else : ?>
<div class="grid_3 alpha">&nbsp;</div><!-- / sidebar -->
<?php endif; ?>