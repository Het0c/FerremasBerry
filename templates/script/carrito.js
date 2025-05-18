if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ready);
} else {
    ready();
}

function ready() {
    const iconoCarrito = document.querySelector('.icon-cart');
    const carrito = document.getElementById('carrito');

    iconoCarrito.addEventListener('click', () => {
        carrito.classList.toggle('activo');
    });

    document.querySelectorAll('.add-cart').forEach(button => {
        button.addEventListener('click', e => {
            const card = e.target.closest('.card-product');
            const titulo = card.querySelector('h3').innerText;
            const precio = card.querySelector('.price').textContent.trim();
            const imagenSrc = card.querySelector('img').src;
            agregarItemAlCarrito(titulo, precio, imagenSrc);
        });
    });
}



function agregarItemAlCarrito(titulo, precio, imagenSrc) {
    const carritoItems = document.querySelector('.carrito-items');

    const titulos = carritoItems.querySelectorAll('.carrito-item-titulo');
    for (let t of titulos) {
        if (t.innerText === titulo) {
            alert("El producto ya está en el carrito");
            return;
        }
    }

    const item = document.createElement('div');
    item.classList.add('carrito-item');

    item.innerHTML = `
        <img src="${imagenSrc}" width="80px" alt="">
        <div class="carrito-item-detalles">
            <span class="carrito-item-titulo">${titulo}</span>
            <div class="selector-cantidad">
                <i class="fa-solid fa-minus restar-cantidad"></i>
                <input type="text" value="1" class="carrito-item-cantidad" disabled>
                <i class="fa-solid fa-plus sumar-cantidad"></i>
            </div>
            <span class="carrito-item-precio">${precio}</span>
        </div>
        <span class="btn-eliminar">
            <i class="fa-solid fa-trash"></i>
        </span>
    `;

    item.querySelector('.btn-eliminar').addEventListener('click', (e) => {
        e.target.closest('.carrito-item').remove();
        actualizarTotalCarrito();
    });

    item.querySelector('.sumar-cantidad').addEventListener('click', (e) => {
        const input = e.target.parentElement.querySelector('.carrito-item-cantidad');
        input.value = parseInt(input.value) + 1;
        actualizarTotalCarrito();
    });

    item.querySelector('.restar-cantidad').addEventListener('click', (e) => {
        const input = e.target.parentElement.querySelector('.carrito-item-cantidad');
        let cantidad = parseInt(input.value);
        if (cantidad > 1) {
            input.value = cantidad - 1;
            actualizarTotalCarrito();
        }
    });

    carritoItems.appendChild(item);
    document.querySelector('.cart-empty').classList.add('hidden');
    document.querySelector('.cart-total').classList.remove('hidden');

    actualizarTotalCarrito();
}

function actualizarTotalCarrito() {
    const totalPagar = document.querySelector('.total-pagar');
    const carritoItems = document.querySelectorAll('.carrito-item');
    let total = 0;

    carritoItems.forEach(producto => {
        const precioTexto = producto.querySelector('.carrito-item-precio').innerText.replace('$', '').replace('.', '').replace(',', '.');
        const cantidad = parseInt(producto.querySelector('.carrito-item-cantidad').value);
        const precio = parseFloat(precioTexto);
        total += precio * cantidad;
    });

    totalPagar.innerText = `$${total.toLocaleString("es-CL")}`;
    

}




