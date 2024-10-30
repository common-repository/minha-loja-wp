<div id="car-buy">

    <ul class="steps">
        <li class="current">Carrinho</li>
        <li class="step-payment">Pagamento Digital</li>
        <li class="step-request">Confirmação do Pedido</li>
    </ul>

    <form id="form-shopping-cart" action="{shopping.cart.form.action}" method="post">
        <ul class="close-request">
            <li><a href="{table.finish.label.continue}" title="Continuar comprando">Continuar comprando</a></li>
            <li class="btn">
                <input type="submit" name="bwec_close_shopp" value="{table.finish.label.checkout}" class="bwec_close_cart" />
            </li>
        </ul>
        <table>
            <tbody>
                <tr class="first">
                    <th>{table.head.label.product}</th>
                    <th>{table.head.label.quantity}</th>
                    <th>{table.head.label.unit.price}</th>
                    <th colspan="2">{table.head.label.total.price}</th>
                </tr>
                {table.body.items}
            </tbody>
        </table>

        <span class="total">Sub-Total: {table.finish.label.subtotal.price}</span>

        <div class="form-cep">
            <span>Digite o CEP do endereço de entrega para calcular o valor do frete.</span>
            <label for="cep">CEP</label>
            <input type="text" id="cep" name="bwec_zipcode_value" value="{table.finish.zipcode.value}" />

            <div class="opcao-envio">
                <label>
                    <input type="radio" name="bwec_zipcode_mode" value="sedex" {table.finish.mode.sedex.checked} />
                    <img src="{table.finish.label.continue}/wp-content/plugins/minha-loja-wp/content/themes/buscape-wp-ecommerce/assets/images/sedex.png" width="81" height="21" alt="Sedex" />
                </label>

                <label>
                    <input type="radio" name="bwec_zipcode_mode" value="pac" {table.finish.mode.pac.checked} />
                    <img src="{table.finish.label.continue}/wp-content/plugins/minha-loja-wp/content/themes/buscape-wp-ecommerce/assets/images/pac.png" width="82" height="21" alt="PAC" />
                </label>
            </div>

        </div>


        <div class="button-frete">
            <input type="submit" value="Calcular o Frete" class="calcular-frete" id="bwec_zipcode" name="bwec_zipcode" />
            <input type="hidden" name="frete_action" id="frete_action" value="{table.finish.zipcode.action.url}" />
        </div>

        {table.finish.cep.value.markup}

        <span class="subtotal">Total: {table.finish.label.total.price}</span>

        <div id="pd-data">{table.finish.pd.data.fields}</div>

        <ul class="close-request">
            <li><a href="{table.finish.label.continue}" title="Continuar comprando">Continuar comprando</a></li>
            <li class="btn">
                <input type="submit" name="bwec_close_shopp" value="{table.finish.label.checkout}" class="bwec_close_cart" />
            </li>
        </ul>

    </form>

</div>
