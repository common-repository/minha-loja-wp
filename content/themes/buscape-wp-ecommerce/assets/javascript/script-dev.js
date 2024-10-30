jQuery (function() {
    jQuery(".slide-home").jCarouselLite({
        btnNext: ".next",
        btnPrev: ".prev"
    });
    jQuery(".slide-detail").jCarouselLite({
        btnNext: ".next",
        btnPrev: ".prev",
        visible: 3
    });
    
    // Insere a classe last no último item do menu de categorias na sidebar.
    jQuery( '#sidebar-category-list ul, #sidebar-category-list > ul > li:last > ul > li:last' ).addClass( 'last' );
    
    // Troca o action do form para calcular o frete
    jQuery( '#bwec_zipcode' ).click( function() {
        jQuery( '#form-shopping-cart' ).attr( 'action', jQuery( '#frete_action' ).val() );
    } );
    
    // Mascara no campo de cep no carrinho de compras
    jQuery( '#cep' ).setMask( {mask : '99999-999'} );
    
    
    // Exibir formas de pagamento
    jQuery( '.forms-payment a' ).click( function() {
        var href = jQuery( this ).attr( 'href' );
        jQuery( href ).fadeIn();
        jQuery( 'html,body' ).animate( {scrollTop: jQuery( href ).offset().top}, 500 );
        return false;
    } );
    
    // Rolagem com suavidade até os detalhes do produto
    jQuery( '.more-detail a' ).click( function() {
        var href = jQuery( this ).attr( 'href' );
        jQuery( href ).fadeIn();
        jQuery( 'html,body' ).animate( {scrollTop: jQuery( href ).offset().top}, 500 );
        return false;
    } );
    
});
jQuery(document).ready(function() {
    jQuery('.jqzoom').jqzoom({
        zoomType: 'innerzoom',
        preloadImages: false,
        alwaysOn:false
    });
});

/**
 * jQuery resetDefaultValue plugin
 * @version 0.9.1
 * @author Leandro Vieira Pinho
 *
 *  Limpar input quando clicado
 */
jQuery.fn.resetDefaultValue = function() {
	function _clearDefaultValue() {
		var _$ = jQuery(this);
		if ( _$.val() == this.defaultValue ) {_$.val('');}
	};
	function _resetDefaultValue() {
		var _$ = jQuery(this);
		if ( _$.val() == '' ) {_$.val(this.defaultValue);}
	};
	return this.click(_clearDefaultValue).focus(_clearDefaultValue).blur(_resetDefaultValue);
}
jQuery(function() {
	jQuery('.field').resetDefaultValue(); // for all input elements
	jQuery('input.field').resetDefaultValue(); // for some elements
	jQuery('#s').resetDefaultValue(); // for a especific element
	jQuery('textarea').resetDefaultValue(); // work with textarea too
});