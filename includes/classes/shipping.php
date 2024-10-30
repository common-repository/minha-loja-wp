<?php

class BWEC_Shipping
{
    public function  __construct()
    {
        
    }

    /**
     * Calculate the value of shipping.
     * 
     * @global type $obj_bwec_wpdb
     * @global type $obj_bwec_cart
     * @param type $cart_total_price
     * @return type 
     */
    public function calculate_shipping( $cart_total_price )
    {
        global $obj_bwec_wpdb, $obj_bwec_cart;
        
        // Suprime os erros do XML para não exibí-los
        libxml_use_internal_errors( true );

        $cart_id = $obj_bwec_cart->get_cart_id();
        $zipcode = $obj_bwec_wpdb->get_meta_value( 'CEP', $cart_id );
        $mode    = $obj_bwec_wpdb->get_meta_value( 'CEP_MODE', $cart_id );
        
        if ( empty( $mode ) )
            $mode = 'sedex';
        
        if ( empty( $zipcode ) ) return;            
        
        $response           = $this->_get_correios_xml( $zipcode, $mode );
        $shipping_details   = simplexml_load_string( $response );
        
        if ( $shipping_details ) :
            $shipping_value     = str_replace( ',', '.', $shipping_details->cServico->Valor );
            $shipping_limit     = get_option( BWEC_OPTION_FREE_SHIPPING );        

            if ( $shipping_limit > 0 && $cart_total_price > $shipping_limit )
                $shipping_value = 0.00;

            return array( 'cep'   => $zipcode,
                          'modo'  => $mode,
                          'valor' => $shipping_value,
                          'prazo' => $shipping_details->cServico->PrazoEntrega 
                        );
        else :
                $xml_errors = libxml_get_errors();
                libxml_clear_errors();

                return array( 'cep'   => $zipcode,
                              'modo'  => $mode,
                              'valor' => 0.00,
                              'prazo' => 0,
                              'erro'  => $xml_errors[0]->message );
        endif;
    }

    /**
     * Update the CEP in meta data table.
     * 
     * @global type $obj_bwec_wpdb
     * @global type $obj_bwec_cart 
     */
    public function update_meta_cep()
    {
        global $obj_bwec_wpdb, $obj_bwec_cart;

        $cart_id = $obj_bwec_cart->get_cart_id();

        $cep = isset( $_POST['bwec_zipcode'] ) ? preg_replace( '/[^\d]/', '', $_POST['bwec_zipcode_value'] ) : $obj_bwec_wpdb->get_meta_value( 'CEP', $cart_id );

        if ( !empty( $cep ) )
            $obj_bwec_wpdb->update_meta_value( 'CEP', $cart_id, $cep );

        if ( isset( $_POST['bwec_zipcode_mode'] ) )
            $obj_bwec_wpdb->update_meta_value( 'CEP_MODE', $cart_id, $_POST['bwec_zipcode_mode'] );
    }

    /**
     * Execute the request to correios webservice and retrieves the response.
     * 
     * @param type $zipcode
     * @param type $mode
     * @return type 
     */
    private function _get_correios_xml( $zipcode, $mode )
    {
        try {
            $ch = curl_init( 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?' . $this->_get_params( $zipcode, $mode ) );
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);            
        } catch (Exception $exc) {
            echo $exc->getMessage();
            exit;
        }

        curl_close($ch);

        return $response;
    }

    /**
     * Retrieves parameters to mount the correios request url.
     * 
     * @param type $zipcode
     * @param type $mode
     * @return type 
     */
    private function _get_params( $zipcode, $mode )
    {
        $arr_modes = array( 'pac' => 41106, 'sedex' => 40010, 'sedex10' => 40215 );
        
        $arr_params = array(
            'nCdEmpresa'            => '', // Código administrativo junto à ECT, para clientes com contrato.
            'sDsSenha'              => '', // 8 primeiros digitos do CNPJ do contrato.
            'nCdServico'            => $arr_modes[$mode], // Serviços que retornarão valor. 41106 - PAC; 40010 - SEDEX;
            'sCepOrigem'            => apply_filters( 'bwec_scep_origem', get_option( BWEC_OPTION_SOURCE_ZIPCODE ) ), // CEP de origem da encomenda sem hifens
            'sCepDestino'           => $zipcode, // CEP de destino da encomenda sem hifens
            'nVlPeso'               => $this->_get_products_weight(), // Peso dos produtos em KG.
            'nCdFormato'            => get_option( BWEC_OPTION_CORREIO_FORMAT ), // 1 - Caixa pacote; 2 - Rolo prisma
            'nVlComprimento'        => get_option( BWEC_OPTION_CORREIO_LENGTH ), // Comprimento em centímetros
            'nVlAltura'             => get_option( BWEC_OPTION_CORREIO_HEIGHT ), // Altura em centímetros
            'nVlLargura'            => get_option( BWEC_OPTION_CORREIO_WIDTH ), // Largura em centimetros
            'nVlDiametro'           => get_option( BWEC_OPTION_CORREIO_DIAMETER ), // Diametro em centímetros
            'sCdMaoPropria'         => 'N', // Serviço adicional mão própria
            'nVlValorDeclarado'     => 0, // Serviço de valor declarado
            'sCdAvisoRecebimento'   => 'N', // Serviço de AR
            'StrRetorno'            => 'XML', // Formato de retorno da pesquisa
        );

        foreach( (array)$arr_params as $key => $value )
            $params[] = $key . '=' . $value;

        return implode( '&', $params );
    }

    /**
     * Returns the weight of all products in current shop cart. The value is
     * used to calculate the shipping price.
     *
     * @return decimal The weight in pounds, follow format 0.000
     */
    private function _get_products_weight()
    {
        global $obj_bwec_cart;

        $products_in_cart = $obj_bwec_cart->get_products();

        $weight = 0.000;
        foreach ( $products_in_cart as $product )
            $weight += get_post_meta ( $product->ID , BWEC_POSTMETA_WEIGHT, true )*$product->cart_quantity;

        if ( $weight == 0 )
            $weight = 0.10;
        
        return $weight;
    }
}

global $obj_bwec_shipping;
$obj_bwec_shipping = new BWEC_Shipping();