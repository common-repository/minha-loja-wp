<div id="car-buy">

    <ul class="steps">
        <li>Carrinho</li>
        <li class="step-payment">Pagamento Digital</li>
        <li class="step-request current">Confirmação do Pedido</li>
    </ul>
    
    <p class="success">Seu pedido foi confirmado com sucesso.
        <?php if ( isset( $_POST['id_pedido'] ) ) : ?>
            <span>O número do seu pacote é <?php echo $_POST['id_pedido']; ?></span>
        <?php endif; ?>
    </p>
    
    <?php 
        $ebit_id = get_option( BWEC_OPTION_EBIT_ID );
        if ( !empty ( $ebit_id ) ) :
    ?>
    <p class="ebit-pesquisa">
        <a href="https://www.ebitempresa.com.br/bitrate/pesquisa1.asp?empresa=1<?php echo $ebit_id; ?>5">
            <img border="0" name="banner" src="https://www.ebitempresa.com.br/bitrate/banners/b1<?php echo $ebit_id; ?>5.gif" alt="O que você achou desta loja?" width="468" height="60" />
        </a>
    </p>
    <?php endif; ?>
    
    <ul id="my-account">
        <li><a href="https://www.pagamentodigital.com.br/minha_conta/PaginaInicial/index" title="Acessar minha conta" target="_blank">Acessar minha conta</a></li>
        <li><a href="https://www.pagamentodigital.com.br/minha_conta/Transacoes/EmAndamento" title="Meus Pedidos" target="_blank">Meus Pedidos</a></li>
    </ul>
</div>

<?php get_template_part( 'box' , 'other-products' ); ?>