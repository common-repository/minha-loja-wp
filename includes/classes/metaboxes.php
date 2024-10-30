<?php
class BWEC_Metaboxes {

    public function __construct()
    {
        add_action( 'save_'.BWEC_CPT_PRODUCTS, array( $this, 'save_postmeta' ), 11 );
        add_action( 'draft_'.BWEC_CPT_PRODUCTS, array( $this, 'save_postmeta' ), 11 );
        add_action( 'publish_'.BWEC_CPT_PRODUCTS, array( $this, 'save_postmeta' ), 11 );
    }

    public function create_metaboxes()
    {
        add_meta_box( 'buscape-wp-ecommerce', __( 'Additional data', BWEC_TEXTDOMAIN ), array( $this, 'show_metabox' ), BWEC_CPT_PRODUCTS, 'side', 'high' );
    }

    /**
     * Display the metabox in edit form.
     *
     * @global object $post
     */
    public function show_metabox()
    { 
        global $post, $obj_bwec_quebarato;
        
        $weight = get_post_meta( $post->ID, BWEC_POSTMETA_WEIGHT, true );
        $weight = ( !empty( $weight ) ) ? number_format(  $weight, 3, ',', '.' ) : '' ;
        
        $price = get_post_meta( $post->ID, BWEC_POSTMETA_PRICE, true );
        $price = ( !empty( $price ) ) ? number_format(  $price, 2, ',', '.' ) : '' ;
        
        $price_off = get_post_meta( $post->ID, BWEC_POSTMETA_PRICE_OFF, true );
        $price_off = ( !empty( $price_off ) && $price_off != '0.00' ) ? number_format(  $price_off, 2, ',', '.' ) : '' ;
    ?>
            <div id="bwec-product-details">
                <p>
                    <label for="bwec_product_cod">Código Referência: </label>
                    <input type="text" value="<?php echo bwec_get_product_cod(); ?>" id="bwec_product_cod" name="bwec_aditional_data[<?php echo BWEC_POSTMETA_CODE; ?>]" />
                    <span class="description">Código de referência do produto na loja.</span>
                </p>
                <p>
                    <label for="bwec_product_weight">Peso (KG): </label>
                    <input type="text" value="<?php echo $weight; ?>" id="bwec_product_weight" name="bwec_aditional_data[<?php echo BWEC_POSTMETA_WEIGHT; ?>]" />
                    <span class="description">Ex 0,300. O peso do produto é imprescindível para o calculo do frete.</span>
                </p>
                <p>
                    <label for="bwec_product_price">Preço: </label>
                    <input type="text" value="<?php echo $price; ?>" id="bwec_product_price" name="bwec_aditional_data[<?php echo BWEC_POSTMETA_PRICE; ?>]" />
                    <span class="description">Preço de venda do produto.</span>
                </p>
                <p>
                    <input type="checkbox" name="bwec_product_display_price_off" id="bwec_product_display_price_off" 
                    <?php checked( ( bwec_get_product_price_off() != '0,00' ), true ); ?> />
                    <label for="bwec_product_display_price_off">Exibir preço promocional</label>
                </p>
                <p id="bwec-price-off"<?php echo ( bwec_get_product_price_off() != '0,00' ) ? '' : ' class="bwec-hidden"'; ?>>
                    <input type="text" value="<?php echo $price_off; ?>" id="bwec_product_price_off" name="bwec_aditional_data[<?php echo BWEC_POSTMETA_PRICE_OFF; ?>]" />
                    <span class="description">Inclua um valor menor que o preço para oferecer desconto nos produtos.
                    Caso este campo seja preenchido, seu valor será considerado como o preço de venda do produto.</span>
                </p>
                <p>
                    <input type="hidden" name="bwec_aditional_data[<?php echo BWEC_POSTMETA_DISPLAY_PORTIONS; ?>]" id="bwec_product_display_portions_hack" value="0" />
                    <input type="checkbox" name="bwec_aditional_data[<?php echo BWEC_POSTMETA_DISPLAY_PORTIONS; ?>]" id="bwec_product_display_portions" value="1" <?php echo ( bwec_product_display_portions() ) ? 'checked="checked"' : ''; ?>/>
                    <label for="bwec_product_display_portions">Exibir condições de parcelamento</label>
                </p>
            </div>
    <?php
        $obj_bwec_quebarato->metabox_markup( $post->ID );
    }

    /**
     * Save the post meta value.
     */
    public function save_postmeta( $post_id )
    {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        
        if ( !isset( $_POST['bwec_aditional_data'] ) ) return;

        extract( $_POST, EXTR_SKIP );

        if ( isset( $bwec_aditional_data ) ) {
            foreach ( (array)$bwec_aditional_data as $meta_key => $meta_value )
                update_post_meta ( $post_id, $meta_key, $this->_sanitize( $meta_value, $meta_key ) );
        }
    }

    /**
     * Sanitize the post meta values before save.
     *
     * @param <type> $meta_value
     * @param <type> $meta_key
     * @return The post meta sanitized.
     */
    private function _sanitize( $meta_value, $meta_key )
    {
        switch ( $meta_key )
        {
            case BWEC_POSTMETA_PRICE :
            case BWEC_POSTMETA_PRICE_OFF :
            case BWEC_POSTMETA_WEIGHT :
                // Changes the value to american pattern to facilitate the operations with the value.
                $meta_value = str_replace( '.', '', $meta_value );
                $meta_value = str_replace( ',', '.', $meta_value );
                break;

            default :
                break;
        }

        return $meta_value;
    }
}

global $obj_bwec_metaboxes;
$obj_bwec_metaboxes = new BWEC_Metaboxes();

/**
 * Creates the metaboxes used in plugin.
 */
function bwec_create_metaboxes()
{
    global $obj_bwec_metaboxes;

    $obj_bwec_metaboxes->create_metaboxes();
}