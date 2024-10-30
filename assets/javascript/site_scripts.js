jQuery(function() {
    bwec_scripts.init();
});

var bwec_scripts = {
    
    init : function() {
        bwec_scripts.confirmExcludeProductInCart();
    },
    
    confirmExcludeProductInCart : function() {
        jQuery( '.closed' ).click( function() {
            if ( confirm( 'Este produto será retirado do carrinho de compras. Confirmar?' ) )
                return true;
            else return false;
        } );
    }
}