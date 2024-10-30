<tr>
    <td>
        <a href="{cart.items.product.link}" title="{cart.items.attribute.title}">
            {cart.items.product.thumb}
        </a>  
        <p><a href="{cart.items.product.link}" title="{cart.items.attribute.title}">{cart.items.product.title}</a></p>
    </td>
    <td>
        <a class="menos-quantidade" href="{cart.items.update.less.link}" title="Remover 1 item">
            <span>-</span>
        </a>
        {cart.items.product.quantity}
        <a class="mais-quantidade" href="{cart.items.update.more.link}" title="Adicionar 1 item">
            <span>+</span>
        </a>
    </td>
    <td>{cart.items.product.unit.price}</td>
    <td>{cart.items.product.total.price}</td>
    <td>
        <a class="closed" href="{cart.items.product.delete.link}">X</a>
    </td>
</tr>