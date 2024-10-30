<?php

class BWEC_QueBarato {

    private $_api_key = 'bd1291a376bef2548e8e7856edb10be1';
    private $_api_url = 'http://api.quebarato.com';

    public function __construct()
    {
        add_action( 'publish_'.BWEC_CPT_PRODUCTS , array( &$this, 'publish_post' ), 11 );
    }

    public function metabox_markup( $post_id )
    {
        $quebarato = get_option( BWEC_OPTION_QUEBARATO_USER );
        if ( !empty( $quebarato ) ) :
            $saved_in_quebarato = get_post_meta( $post_id, BWEC_POSTMETA_QUEBARATO_PUBLISH, true );
            if ( $saved_in_quebarato ) :
                $product_quebarato_url = get_post_meta( $post_id, BWEC_POSTMETA_QUEBARATO_URL, true );
                ?>
                <div class="updated"><p><strong>Seu anúncio já está publicado no QueBarato! </strong><a href="<?php echo $product_quebarato_url; ?>" target="_blank">Confira.</a></p></div>
                <?php
            else :
                $error_message = get_transient( 'bwec_quebarato_error_message' );
                if ( !empty( $error_message ) )
                    echo "<div class='error'><p>$error_message[$post_id]</p></div>";
                delete_transient( 'bwec_quebarato_error_message' );
    ?>
<script language="Javascript" type="text/javascript">
    jQuery(document).ready(function($) {

            function setHeader( xhr ) {
                xhr.setRequestHeader( 'X-QB-Key', '<?php echo $this->_api_key; ?>');
            }

            function root() {

                var categoryServiceUri = '<?php echo $this->_api_url; ?>/v1/category';

                var estrutura = $('.tree_select li');

                var apiKey = '<?php echo $this->_api_key; ?>';

                $.ajax({
                 url: categoryServiceUri,
                 type: 'GET',
                 dataType: 'json',
                 success: function(categories) {
                         var options = '';
                                $.each(categories, function(i, obj) {
                                        var rootName = obj.name;
                                        var rootUri = obj.href;
                                        options += '<option value="' + rootUri + '">' + rootName + '</option>';
                                });

                                $(".selectG:first").append(options);

                     },
                 error: function() {
                        var html_error = '<div class="error"><p>O QueBarato! encontra-se indisponível no momento. Tente novamente em instantes.</p></div>';
                        $( '#bwec-quebarato-options' ).html( html_error );
                     },
                 beforeSend: setHeader
               });
            }

            $(".selectG:first").live('change', function(){
                    $("#qb_category").val("");
                    attributes($(this));
            });

            $(".selectG").live('change', function(){
                    $("#qb_category").val("");
                    children($(this));
            });

            function children(el){

                    var estrutura = $('.tree_select li:last').clone();
                    var categoryServiceUri = '<?php echo $this->_api_url; ?>/v1';
                    el.closest('li').nextAll().remove();
                    el.find("option:selected").each(function () {
                            if($(this).val() != 'option'){
                                    $.ajax({
                                     url: categoryServiceUri + $(this).val(),
                                     type: 'GET',
                                     dataType: 'json',
                                     success: function(data){
                                                            if(data.children != null){
                                                                    var options = '';

                                                                    $.each(data.children, function(i,obj) {
                                                                            var childName = obj.name;
                                                                            var childUri = obj.href;
                                                                            options += '<option value="' + childUri + '">' + childName + '</option>';
                                                                    });

                                                                    $('option', estrutura).not('option:first').remove();
                                                                    $('.selectG', estrutura).append(options);
                                                                    $( 'option', estrutura ).filter( ':first' ).text( 'Selecione uma subcategoria' );
                                                                    $('#category_selects').append(estrutura);

                                                            } else {

                                                                    $("#qb_category").val(data.id);

                                                            }
                                                    },
                                     error: function() {

                                         },
                                     beforeSend: setHeader
                                });
                            }
                      });
            }

            function attributes(root){

                    root.find("option:selected").each(function () {

                            var rootUri = root.val();

                            if ( rootUri == "/category/8" || rootUri == "/category/3" || rootUri == "/category/4" || rootUri == "/category/6" )
                                $("#condition").fadeOut();
                            else
                                $("#condition").fadeIn();
                    });

            }
    root();
    });
</script>
            <p>
                <label for="bwec_product_quebarato_publish">
                    <input type="checkbox" name="bwec_quebarato_publish" id="bwec_product_quebarato_publish" value="1" />
                    Publicar este anúncio no QueBarato!
                </label>
            </p>
            <div id="bwec-quebarato-options" class="quebarato-hidden">
                <div class="line section10">
                    <div class="middleCol">
                            <div class="line separator">
                                    <ul id="category_selects" class="tree_select">
                                            <li id="categoryselect">
                                                    <div class="line">
                                                            <div class="unit">
                                                                    <select class="selectG active_text_filled">
                                                                            <option value="option">Selecione uma categoria</option>
                                                                    </select>
                                                            </div>
                                                            <div class="unit">
                                                            <?php
                                                            echo '<img style="display: none;"
                                                                            src="'.get_option('siteurl').'/wp-content/plugins/postad/loading.gif"
                                                                            class="loading">'; ?>
                                                            </div>
                                                    </div>
                                            </li>
                                    </ul>
                            </div>
                    </div>
                </div>

                <p id="condition">
                    <label>Estado do produto: </label>
                    <label for="bwec_product_quebarato_condition_new"><input type="radio" id="bwec_product_quebarato_condition_new" name="bwec_quebarato_condition" value="novo" checked="checked" /> Novo</label>
                    <label for="bwec_product_quebarato_condition_used"><input type="radio" id="bwec_product_quebarato_condition_used" name="bwec_quebarato_condition" value="usado" /> Usado</label>
                </p>
                <input type="hidden" id="qb_category" name="qb_category" />
            </div>
    <?php
            endif;
        endif;
    }

    public function publish_post( $post_id )
    {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        if ( !isset( $_POST['bwec_quebarato_publish'] ) ) return;

        if ( !class_exists( 'QueBaratoBaseAPI' ) && file_exists( BWEC_CLASSES_PATH . '/QueBaratoBaseAPI.class.php' ) )
            require  BWEC_CLASSES_PATH . '/QueBaratoBaseAPI.class.php';

        $user           = get_option( BWEC_OPTION_QUEBARATO_USER );
        $pass           = base64_decode( get_option( BWEC_OPTION_QUEBARATO_PASS ) );
        $quebarato_api  = new QueBaratoBaseAPI( $this->_api_url, $this->_api_key );
        $description    = apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) );

        $data_arr       = array(
            'title'             => get_the_title( $post_id ),
            'description'       => str_replace( array( "\r","\n", "\t" ), '', strip_tags( $description, '<b><p><ul><ol><li><i><u>' ) ), // Retira todas as tags HTML do post content do produto, permitindo somente os especificados.
            'category'          => array( 'href' => '/category/'.$_POST['qb_category'] ),
            'locale'            => array( 'zip' => get_option( BWEC_OPTION_SOURCE_ZIPCODE ) ),
            'condition'         => $_POST['bwec_quebarato_condition'],
            'price'             => array( 'amount' => $this->_get_product_price(), 'currency' => 'BRL' ),
            'paymentMethods'    => $this->_get_payment_methods()
        );

        $data           = json_encode( $data_arr );
        $return_data    = $quebarato_api->post( '/v1/ad', $data, $user, $pass, 'application/json' );

        if ( $return_data->resultCode != 201 ) :
            $message[$post_id] = ( isset( $return_data->result->trace[0] ) ) ? "O seu produto não foi publicado no QueBarato! " . $return_data->result->trace[0] : "O QueBarato! encontra-se indisponível no momento. Tente novamente em instantes.";
            set_transient( 'bwec_quebarato_error_message', $message );
            return;
        endif;

        $location       = $return_data->header['extra']->Location;

        // Url
        $url_return = $quebarato_api->get('/v1'.$location);
        update_post_meta( $post_id, BWEC_POSTMETA_QUEBARATO_URL, $url_return->result->skinUrl );

        // Image
        $image_src      = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
        $image_data     = array( 'image' => '@'.str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $image_src[0] ) );
        $image_return   = $quebarato_api->post( '/v1'.$location.'/media/image', $image_data, $user, $pass, "multipart/form-data" );

        add_post_meta( $post_id, BWEC_POSTMETA_QUEBARATO_PUBLISH, true );
    }

    private function _sanitize_price( $price )
    {
        $price = str_replace( '.', '', $price );
        $price = str_replace( ',' , '.', $price );

        return $price;
    }

    private function _get_payment_methods()
    {
        $methods = get_option( BWEC_OPTION_QUEBARATO_PAYMENT );

        $array_methods = array();

        foreach ( (array)$methods as $method )
            array_push ( $array_methods, array( 'href' => $method ) );

        return $array_methods;
    }

    private function _get_product_price()
    {
        $price = $_POST['bwec_aditional_data'][BWEC_POSTMETA_PRICE];
        $price_off = $_POST['bwec_aditional_data'][BWEC_POSTMETA_PRICE_OFF];

        if ( !empty( $price_off ) )
            $price = $price_off;

        return $this->_sanitize_price( $price );
    }


}

global $obj_bwec_quebarato;
$obj_bwec_quebarato = new BWEC_QueBarato();