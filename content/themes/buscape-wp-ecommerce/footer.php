            </div><!-- / wrapper-content -->

            <div id="footer" class="grid_12">

                <?php
                wp_nav_menu( array(
                    'theme_location'    => 'footer',
                    'container'         => '',
                    'menu_class'        => 'nav-footer',
                    'fallback_cb'       => false
                ) );
                ?>

                <div id="method-payment">
                    
                    <h4 class="title">Formas de Pagamento</h4>
                    <img src="<?php bloginfo( 'template_url' ); ?>/assets/images/footer-pg-digital.png" width="87" height="32" alt="Pagamento Digital" />
                    <ul>
                        <li class="visa">Visa</li>
                        <li class="american-express">American Express</li>
                        <li class="master-card">Master Card</li>
                        <li class="diner-club">Diner Club</li>
                        <li class="hipercard">Hipercard</li>
                        <li class="aura">Aura</li>
                        <li class="saldo-virtual">Saldo Virtual</li>
                        <li class="boleto">Boleto</li>
                        <li class="itau">Itaú</li>
                        <li class="banco-brasil">Banco do Brasil</li>
                        <li class="bradesco">Bradesco</li>
                    </ul>
                </div><!-- / method-payment -->

                <?php
                    $store_data = get_option( BWEC_OPTION_CONTACT_INFORMATION );
                    if ( !empty ( $store_data['logradouro'] ) ) :
                ?>
                <div id="address">
                    <h4 class="title">Informações de contato</h4>
                    <span>
                        <?php printf( '%s, %s %s - %s <br />%s-%s <br />%s', 
                              $store_data['logradouro'], $store_data['numero'], 
                              $store_data['complemento'], $store_data['bairro'],
                              $store_data['cidade'], $store_data['uf'], $store_data['fone'] ); ?>
                    </span>
                </div>
                <?php endif; ?>

                <div class="branding">
                    <a href="<?php echo BP_WP_ECOMMERCE_SITE_URL; ?>" title="<?php bloginfo(); ?> - <?php bloginfo( 'description' ); ?>">
                        <?php
                            global $obj_bwec_options;
                            $obj_bwec_options->the_store_brand();
                        ?>
                    </a>
                </div>

                <?php 
                    $ebit_id    = get_option( BWEC_OPTION_EBIT_ID );
                    if ( !empty ( $ebit_id ) ) :
                ?>
                <div class="selo-ebit">
                    <a id="seloEbit" href="http://www.ebit.com.br/#<?php echo $ebit_id; ?>" target="_blank" onclick="redir(this.href);">Avaliação de Lojas e-bit</a>
                    <script type="text/javascript" id="getSelo"  src="https://a248.e.akamai.net/f/248/52872/0s/img.ebit.com.br/ebitBR/selo-ebit/js/getSelo.js?<?php echo $ebit_id; ?>"></script>
                </div>
                <?php endif; ?>

                <div class="footer-logos">
                    <p class="buscape-logo"><a href="http://www.buscapecompany.com/" title="Buscapé - Comparação de Preços, Produtos e Serviços" target="_blank">Buscapé</a></p>
                    <p class="apiki-logo"><a href="http://apiki.com" title="Apiki. Primeira empresa brasileira 100% especializada em WordPress" target="_blank">Apiki</a></p>
                </div><!-- /grid_2 -->

            </div><!-- / footer -->

        </div><!-- / wrapper -->
        
        <?php wp_footer(); ?>
    </body>
</html>
