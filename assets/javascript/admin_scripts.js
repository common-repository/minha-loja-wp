// Meio Mask
(function(D){var C=(window.orientation!=undefined),A=((D.browser.opera||(D.browser.mozilla&&parseFloat(D.browser.version.substr(0,3))<1.9))?"input":"paste");D.event.special.paste={setup:function(){if(this.addEventListener){this.addEventListener(A,B,false)}else{if(this.attachEvent){this.attachEvent(A,B)}}},teardown:function(){if(this.removeEventListener){this.removeEventListener(A,B,false)}else{if(this.detachEvent){this.detachEvent(A,B)}}}};function B(F){var E=this;F=D.event.fix(F||window.e);F.type="paste";setTimeout(function(){D.event.handle.call(E,F)},1)}D.extend({mask:{rules:{"z":/[a-z]/,"Z":/[A-Z]/,"a":/[a-zA-Z]/,"*":/[0-9a-zA-Z]/,"@":/[0-9a-zA-ZçÇáàãâéèêíìóòôõúùü]/},keyRepresentation:{8:"backspace",9:"tab",13:"enter",16:"shift",17:"control",18:"alt",27:"esc",33:"page up",34:"page down",35:"end",36:"home",37:"left",38:"up",39:"right",40:"down",45:"insert",46:"delete",116:"f5",123:"f12",224:"command"},iphoneKeyRepresentation:{10:"go",127:"delete"},signals:{"+":"","-":"-"},options:{attr:"alt",mask:null,type:"fixed",maxLength:-1,defaultValue:"",signal:false,textAlign:true,selectCharsOnFocus:true,autoTab:true,setSize:false,fixedChars:"[(),.:/ -]",onInvalid:function(){},onValid:function(){},onOverflow:function(){}},masks:{"phone":{mask:"(99) 9999-9999"},"phone-us":{mask:"(999) 999-9999"},"cpf":{mask:"999.999.999-99"},"cnpj":{mask:"99.999.999/9999-99"},"date":{mask:"39/19/9999"},"date-us":{mask:"19/39/9999"},"cep":{mask:"99999-999"},"time":{mask:"29:59"},"cc":{mask:"9999 9999 9999 9999"},"integer":{mask:"999.999.999.999",type:"reverse"},"decimal":{mask:"99,999.999.999.999",type:"reverse",defaultValue:"000"},"decimal-us":{mask:"99.999,999,999,999",type:"reverse",defaultValue:"000"},"signed-decimal":{mask:"99,999.999.999.999",type:"reverse",defaultValue:"+000"},"signed-decimal-us":{mask:"99,999.999.999.999",type:"reverse",defaultValue:"+000"}},init:function(){if(!this.hasInit){var E=this,F,G=(C)?this.iphoneKeyRepresentation:this.keyRepresentation;this.ignore=false;for(F=0;F<=9;F++){this.rules[F]=new RegExp("[0-"+F+"]")}this.keyRep=G;this.ignoreKeys=[];D.each(G,function(H){E.ignoreKeys.push(parseInt(H))});this.hasInit=true}},set:function(I,F){var E=this,G=D(I),H="maxLength";F=F||{};this.init();return G.each(function(){if(F.attr){E.options.attr=F.attr}var O=D(this),Q=D.extend({},E.options),N=O.attr(Q.attr),J="";J=(typeof F=="string")?F:(N!="")?N:null;if(J){Q.mask=J}if(E.masks[J]){Q=D.extend(Q,E.masks[J])}if(typeof F=="object"&&F.constructor!=Array){Q=D.extend(Q,F)}if(D.metadata){Q=D.extend(Q,O.metadata())}if(Q.mask!=null){if(O.data("mask")){E.unset(O)}var K=Q.defaultValue,L=(Q.type=="reverse"),M=new RegExp(Q.fixedChars,"g");if(Q.maxLength==-1){Q.maxLength=O.attr(H)}Q=D.extend({},Q,{fixedCharsReg:new RegExp(Q.fixedChars),fixedCharsRegG:M,maskArray:Q.mask.split(""),maskNonFixedCharsArray:Q.mask.replace(M,"").split("")});if((Q.type=="fixed"||L)&&Q.setSize&&!O.attr("size")){O.attr("size",Q.mask.length)}if(L&&Q.textAlign){O.css("text-align","right")}if(this.value!=""||K!=""){var P=E.string((this.value!="")?this.value:K,Q);this.defaultValue=P;O.val(P)}if(Q.type=="infinite"){Q.type="repeat"}O.data("mask",Q);O.removeAttr(H);O.bind("keydown.mask",{func:E._onKeyDown,thisObj:E},E._onMask).bind("keypress.mask",{func:E._onKeyPress,thisObj:E},E._onMask).bind("keyup.mask",{func:E._onKeyUp,thisObj:E},E._onMask).bind("paste.mask",{func:E._onPaste,thisObj:E},E._onMask).bind("focus.mask",E._onFocus).bind("blur.mask",E._onBlur).bind("change.mask",E._onChange)}})},unset:function(F){var E=D(F);return E.each(function(){var H=D(this);if(H.data("mask")){var G=H.data("mask").maxLength;if(G!=-1){H.attr("maxLength",G)}H.unbind(".mask").removeData("mask")}})},string:function(J,F){this.init();var I={};if(typeof J!="string"){J=String(J)}switch(typeof F){case"string":if(this.masks[F]){I=D.extend(I,this.masks[F])}else{I.mask=F}break;case"object":I=F}if(!I.fixedChars){I.fixedChars=this.options.fixedChars}var E=new RegExp(I.fixedChars),G=new RegExp(I.fixedChars,"g");if((I.type=="reverse")&&I.defaultValue){if(typeof this.signals[I.defaultValue.charAt(0)]!="undefined"){var H=J.charAt(0);I.signal=(typeof this.signals[H]!="undefined")?this.signals[H]:this.signals[I.defaultValue.charAt(0)];I.defaultValue=I.defaultValue.substring(1)}}return this.__maskArray(J.split(""),I.mask.replace(G,"").split(""),I.mask.split(""),I.type,I.maxLength,I.defaultValue,E,I.signal)},_onFocus:function(G){var F=D(this),E=F.data("mask");E.inputFocusValue=F.val();E.changed=false;if(E.selectCharsOnFocus){F.select()}},_onBlur:function(G){var F=D(this),E=F.data("mask");if(E.inputFocusValue!=F.val()&&!E.changed){F.trigger("change")}},_onChange:function(E){D(this).data("mask").changed=true},_onMask:function(E){var G=E.data.thisObj,F={};F._this=E.target;F.$this=D(F._this);if(F.$this.attr("readonly")){return true}F.data=F.$this.data("mask");F[F.data.type]=true;F.value=F.$this.val();F.nKey=G.__getKeyNumber(E);F.range=G.__getRange(F._this);F.valueArray=F.value.split("");return E.data.func.call(G,E,F)},_onKeyDown:function(F,G){this.ignore=D.inArray(G.nKey,this.ignoreKeys)>-1||F.ctrlKey||F.metaKey||F.altKey;if(this.ignore){var E=this.keyRep[G.nKey];G.data.onValid.call(G._this,E?E:"",G.nKey)}return C?this._keyPress(F,G):true},_onKeyUp:function(E,F){if(F.nKey==9||F.nKey==16){return true}if(F.data.type=="repeat"){this.__autoTab(F);return true}return this._onPaste(E,F)},_onPaste:function(F,G){if(G.reverse){this.__changeSignal(F.type,G)}var E=this.__maskArray(G.valueArray,G.data.maskNonFixedCharsArray,G.data.maskArray,G.data.type,G.data.maxLength,G.data.defaultValue,G.data.fixedCharsReg,G.data.signal);G.$this.val(E);if(!G.reverse&&G.data.defaultValue.length&&(G.range.start==G.range.end)){this.__setRange(G._this,G.range.start,G.range.end)}if((D.browser.msie||D.browser.safari)&&!G.reverse){this.__setRange(G._this,G.range.start,G.range.end)}if(this.ignore){return true}this.__autoTab(G);return true},_onKeyPress:function(L,E){if(this.ignore){return true}if(E.reverse){this.__changeSignal(L.type,E)}var M=String.fromCharCode(E.nKey),O=E.range.start,I=E.value,G=E.data.maskArray;if(E.reverse){var H=I.substr(0,O),K=I.substr(E.range.end,I.length);I=H+M+K;if(E.data.signal&&(O-E.data.signal.length>0)){O-=E.data.signal.length}}var N=I.replace(E.data.fixedCharsRegG,"").split(""),F=this.__extraPositionsTill(O,G,E.data.fixedCharsReg);E.rsEp=O+F;if(E.repeat){E.rsEp=0}if(!this.rules[G[E.rsEp]]||(E.data.maxLength!=-1&&N.length>=E.data.maxLength&&E.repeat)){E.data.onOverflow.call(E._this,M,E.nKey);return false}else{if(!this.rules[G[E.rsEp]].test(M)){E.data.onInvalid.call(E._this,M,E.nKey);return false}else{E.data.onValid.call(E._this,M,E.nKey)}}var J=this.__maskArray(N,E.data.maskNonFixedCharsArray,G,E.data.type,E.data.maxLength,E.data.defaultValue,E.data.fixedCharsReg,E.data.signal,F);E.$this.val(J);return(E.reverse)?this._keyPressReverse(L,E):(E.fixed)?this._keyPressFixed(L,E):true},_keyPressFixed:function(E,F){if(F.range.start==F.range.end){if((F.rsEp==0&&F.value.length==0)||F.rsEp<F.value.length){this.__setRange(F._this,F.rsEp,F.rsEp+1)}}else{this.__setRange(F._this,F.range.start,F.range.end)}return true},_keyPressReverse:function(E,F){if(D.browser.msie&&((F.range.start==0&&F.range.end==0)||F.range.start!=F.range.end)){this.__setRange(F._this,F.value.length)}return false},__autoTab:function(F){if(F.data.autoTab&&((F.$this.val().length>=F.data.maskArray.length&&!F.repeat)||(F.data.maxLength!=-1&&F.valueArray.length>=F.data.maxLength&&F.repeat))){var E=this.__getNextInput(F._this,F.data.autoTab);if(E){F.$this.trigger("blur");E.focus().select()}}},__changeSignal:function(F,G){if(G.data.signal!==false){var E=(F=="paste")?G.value.charAt(0):String.fromCharCode(G.nKey);if(this.signals&&(typeof this.signals[E]!="undefined")){G.data.signal=this.signals[E]}}},__getKeyNumber:function(E){return(E.charCode||E.keyCode||E.which)},__maskArray:function(M,H,G,J,E,K,N,L,F){if(J=="reverse"){M.reverse()}M=this.__removeInvalidChars(M,H,J=="repeat"||J=="infinite");if(K){M=this.__applyDefaultValue.call(M,K)}M=this.__applyMask(M,G,F,N);switch(J){case"reverse":M.reverse();return(L||"")+M.join("").substring(M.length-G.length);case"infinite":case"repeat":var I=M.join("");return(E!=-1&&M.length>=E)?I.substring(0,E):I;default:return M.join("").substring(0,G.length)}return""},__applyDefaultValue:function(G){var E=G.length,F=this.length,H;for(H=F-1;H>=0;H--){if(this[H]==G.charAt(0)){this.pop()}else{break}}for(H=0;H<E;H++){if(!this[H]){this[H]=G.charAt(H)}}return this},__removeInvalidChars:function(H,G,E){for(var F=0,I=0;F<H.length;F++){if(G[I]&&this.rules[G[I]]&&!this.rules[G[I]].test(H[F])){H.splice(F,1);if(!E){I--}F--}if(!E){I++}}return H},__applyMask:function(H,F,I,E){if(typeof I=="undefined"){I=0}for(var G=0;G<H.length+I;G++){if(F[G]&&E.test(F[G])){H.splice(G,0,F[G])}}return H},__extraPositionsTill:function(H,F,E){var G=0;while(E.test(F[H++])){G++}return G},__getNextInput:function(N,F){var I=N.form.elements,H=D.inArray(N,I)+1,M=null,J;for(J=H;J<I.length;J++){M=D(I[J]);if(this.__isNextInput(M,F)){return M}}var E=document.forms,G=D.inArray(N.form,E)+1,L,K=null;for(L=G;L<E.length;L++){K=E[L].elements;for(J=0;J<K.length;J++){M=D(K[J]);if(this.__isNextInput(M,F)){return M}}}return null},__isNextInput:function(G,E){var F=G.get(0);return F&&(F.offsetWidth>0||F.offsetHeight>0)&&F.nodeName!="FIELDSET"&&(E===true||(typeof E=="string"&&G.is(E)))},__setRange:function(G,H,E){if(typeof E=="undefined"){E=H}if(G.setSelectionRange){G.setSelectionRange(H,E)}else{var F=G.createTextRange();F.collapse();F.moveStart("character",H);F.moveEnd("character",E-H);F.select()}},__getRange:function(F){if(!D.browser.msie){return{start:F.selectionStart,end:F.selectionEnd}}var G={start:0,end:0},E=document.selection.createRange();G.start=0-E.duplicate().moveStart("character",-100000);G.end=G.start+E.text.length;return G},unmaskedVal:function(E){return D(E).val().replace(D.mask.fixedCharsRegG,"")}}});D.fn.extend({setMask:function(E){return D.mask.set(this,E)},unsetMask:function(){return D.mask.unset(this)},unmaskedVal:function(){return D.mask.unmaskedVal(this[0])}})})(jQuery)

jQuery(function() {
    bwec_scripts.init();
});

var bwec_scripts = {

    init : function() {
        bwec_scripts.validatePrice();
        bwec_scripts.showHidePriceOff();
        bwec_scripts.masks();
        bwec_scripts.uninstallPlugin();
        bwec_scripts.quebaratoMetabox();
        bwec_scripts.validateStoreBuscape();
        bwec_scripts.validateShippingFields();
    },

    /**
     * Validates the price value, can't be zero or empty. Also validate if
     * off price is less than price.
     */
    validatePrice : function() {

        var post_type = jQuery( '#post_type' ).val();

        if ( 'bwec-products' != post_type )
            return true;

        jQuery( '#publish' ).click( function( event ) {

            var price       = bwec_scripts._sanitize_price( jQuery( '#bwec_product_price' ).val() );
            var price_off   = bwec_scripts._sanitize_price( jQuery( '#bwec_product_price_off' ).val() );
            var display_off = jQuery( '#bwec_product_display_price_off' ).is(':checked');

            if ( display_off && price_off >= price )
                return bwec_scripts._abort_product_publish( event, 'O preço promocional precisa ser menor que o preço do produto.' )

            if ( display_off && ( price_off <= 0.00 || isNaN( price_off ) ) )
                return bwec_scripts._abort_product_publish( event, 'É necessário cadastrar um preço promocional para o produto.' );

            if ( price <= 0.00 || isNaN( price ) )
                return bwec_scripts._abort_product_publish( event, 'É necessário cadastrar um preço de venda para o produto.');
        } );
    },

    _sanitize_price : function( price ) {
        price = price.replace( '.', '' );
        price = price.replace( ',', '.' );
        price = parseFloat(price);
        return price;
    },

    _abort_product_publish : function( event, message ) {
        alert( message );
        event.preventDefault();
//        jQuery('#dashboard_quick_press #publishing-action img.waiting').css('visibility', 'hidden');
//        jQuery('#quick-press .submit input[type="submit"], #quick-press .submit input[type="reset"]').attr('disabled','');

        jQuery( '#ajax-loading' ).css( 'visibility', 'hidden' );
        jQuery( '#publish' ).removeClass( 'button-primary-disabled' );
    },

    showHidePriceOff : function() {
        jQuery( '#bwec_product_display_price_off' ).click( function() {
            var checked = this.checked;

            if ( checked )
                jQuery( '#bwec-price-off' ).show();
            else {
                jQuery( '#bwec-price-off' ).hide();
                jQuery( '#bwec_product_price_off' ).val( '0,00' );
            }
        } );
    },

    masks : function() {

        jQuery( '#cep' ).setMask( {mask : '99999-999'} );
        jQuery( '#fone, #celular' ).setMask( {mask : '(99) 9999-9999'} );
        jQuery( '#cnpj' ).setMask( {mask : '99.999.999/9999-99'} );
        jQuery( '#cpf' ).setMask( {mask : '999.999.999-58'} );

        // Tela de cadastro de produtos - Campos de preço e peso
        jQuery( '#bwec_product_price' ).setMask( {mask : '99,999.999.999.999', type : 'reverse'} );
        jQuery( '#bwec_product_price_off' ).setMask( {mask : '99,999.999.999.999', type : 'reverse'} );
        jQuery( '#bwec_product_weight' ).setMask( {mask : '999,99', type : 'reverse'} );
    },

    uninstallPlugin : function() {
        jQuery( '#bwec-uninstall' ).click( function( event ) {
            if ( !confirm( 'Tem certeza que deseja desinstalar o plugin?' ) )
                event.preventDefault();
        } );
    },

    quebaratoMetabox : function() {
        var $queBaratoPublish = jQuery( '#bwec_product_quebarato_publish' ),
            $queBaratoOptions = jQuery( '#bwec-quebarato-options' );
        
        if ( $queBaratoPublish.is( ':checked' ) ) {
            $queBaratoOptions.show();
        }
        
        $queBaratoPublish.click( function() {
            if ( $queBaratoPublish.is( ':checked' ) ) {
                $queBaratoOptions.fadeIn();
            } else {
                $queBaratoOptions.fadeOut();
            }
        })
        
    },
    
    validateStoreBuscape : function() {
        jQuery( '#save-buscape' ).click( function( event ) {
            var store = jQuery( '#buscape-store' ).val();
            if ( jQuery.trim( store ) == '' ) {
                alert( 'Insira o nome da loja antes de salvar.' );
                event.preventDefault();
            }
        } );
        jQuery( '#save-quebarato' ).click( function( event ) {
            var user = jQuery( '#quebarato-user' ).val();
            if ( jQuery.trim( user ) == '' ) {
                alert( 'Insira o nome de usuário antes de salvar.' );
                event.preventDefault();
            }
        } );
    },
    
    validateShippingFields : function() {
        
        jQuery( '#save-shipping' ).click( function() {
            
            var zip = jQuery( '#cep' ).val();
            if ( zip.length < 9 ) {
                alert( 'Preencha seu CEP corretamente.' );
                jQuery( '#cep' ).focus();
                return false;
            }
            
            var length = parseInt( jQuery( '#package-length' ).val() );
            if ( length < 18 || length > 90 ) {
                alert( 'O comprimento da encomenda deve ter no mínimo 18 e no máximo 90 centímetros.' );
                jQuery( '#package-length' ).focus();
                return false;
            }
            
            var height = parseInt( jQuery( '#package-height' ).val() );
            if ( height < 2 || height > 90 ) {
                alert( 'A altura da encomenda deve ter no mínimo 2 e no máximo 90 centímetros.' );
                jQuery( '#package-height' ).focus();
                return false;
            }
            if ( height > length ) {
                alert( 'A altura da encomenda não deve ser maior que o comprimento.' );
                jQuery( '#package-height' ).focus();
                return false;
            }
            
            var width = parseInt( jQuery( '#package-width' ).val() );
            if ( width < 5 || width > 90 ) {
                alert( 'A largura da encomenda deve ter no mínimo 5 e no máximo 90 centímetros.' );
                jQuery( '#package-width' ).focus();
                return false;
            }
            if ( length < 25 && width < 11 ) {
                alert( 'A largura da encomenda deve ser maior que 11 centímetros sempre que a largura for menor que 25' );
                jQuery( '#package-width' ).focus();
                return false;
            }
            
            var diameter = parseInt( jQuery( '#package-diameter' ).val() );
            if ( diameter < 5 || diameter > 90 ) {
                alert( 'O diâmetro da encomenda deve ter no mínimo 5 e no máximo 90 centímetros.' );
                jQuery( '#package-height' ).focus();
                return false;
            }
            
            if ( ( length + width + height ) > 160 ) {
                alert( 'A soma resultante do comprimento + largura + altura não deve superar a 160 centímetros.' );
                jQuery( '#package-length' ).focus();
                return false;
            }
            
            if ( ( length + ( diameter*2 ) ) > 104 ) {
                alert( 'A soma resultante do comprimento + o dobro do diâmetro não deve superar a 104 cm.' );
                jQuery( '#package-length' ).focus();
                return false;
            }
            
        } );
    }
}
