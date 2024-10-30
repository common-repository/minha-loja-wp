jQuery(function() {

    wptitlehint = function(id) {
		id = id || 'title';

		var title = jQuery('#' + id), titleprompt = jQuery('#' + id + '-prompt-text');

		if ( title.val() == '' )
			titleprompt.css('visibility', '');

		titleprompt.click(function(){
			jQuery(this).css('visibility', 'hidden');
			title.focus();
		});

		title.blur(function(){
			if ( this.value == '' )
				titleprompt.css('visibility', '');
		}).focus(function(){
			titleprompt.css('visibility', 'hidden');
		}).keydown(function(e){
			titleprompt.css('visibility', 'hidden');
			jQuery(this).unbind(e);
		});
	}

    wptitlehint();

    jQuery("#bp-wp-ecommerce-promocoes #title").suggest("../wp-content/plugins/minha-loja-wp/content/themes/buscape-wp-ecommerce/includes/promocoes.php",{

        onSelect: function() {
            var post            = new String( this.value );
            var post_content    = post.split('|');
            var post_title      = post_content[1];
            var markup          = jQuery("#produtos-em-promocao");
            var post_entity     = post.replace( /"/g, '&quot;' );
            var html            = '<li><input type="checkbox" name="promocoes_itens[]" checked="checked" value="' + post_entity + '" /> ' + post_title + '</li>';

            markup.append( html );

            jQuery("#bp-wp-ecommerce-promocoes #title").val('');
        }

    });

});