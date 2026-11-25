<?php
$pageTitle = "My Cart | MyShop";
include 'header.php';
?>

<section class="cart-page">

    <h1 class="cart-title">My Cart</h1>

    <div id="cartEmpty" class="cart-empty" style="display:none;">
        <p>Your cart is empty.</p>
        <a href="index.php#products" class="primary-button">START SHOPPING</a>
    </div>

    <div id="cartContent" class="cart-layout">

        <div id="cartItems" class="cart-items"></div>

        <aside class="cart-summary">
            <h3>Order Summary</h3>
            <div class="summary-row">
                <span>Total</span>
                <strong id="cartTotal">$0.00</strong>
            </div>
            <a href="checkout.php" class="primary-button checkout-button">CHECKOUT</a>
            <button type="button" id="clearCart" class="clear-cart">CLEAR CART</button>
        </aside>

    </div>

</section>

<script>
(function () {

    const itemsBox = document.getElementById("cartItems");
    const totalBox = document.getElementById("cartTotal");
    const emptyBox = document.getElementById("cartEmpty");
    const contentBox = document.getElementById("cartContent");
    const counter = document.getElementById("cartCounter");

    function getCart() {
        let cart = [];
        try { cart = JSON.parse(localStorage.getItem("myshopCartItems")); } catch (e) {}
        if (!Array.isArray(cart)) cart = [];
        return cart;
    }

    function saveCart(cart) {
        try { localStorage.setItem("myshopCartItems", JSON.stringify(cart)); } catch (e) {}
    }

    function makeButton(text, className, onClick) {
        const button = document.createElement("button");
        button.type = "button";
        button.className = className;
        button.textContent = text;
        button.addEventListener("click", onClick);
        return button;
    }

    function render() {

        const cart = getCart();
        itemsBox.innerHTML = "";

        let total = 0;
        let count = 0;

        cart.forEach(function (item, index) {

            total += item.price * item.qty;
            count += item.qty;

            const row = document.createElement("div");
            row.className = "cart-item";

            const img = document.createElement("img");
            img.src = "assets/images/" + encodeURI(item.image);
            img.alt = item.name;

            const info = document.createElement("div");
            info.className = "cart-info";
            const name = document.createElement("h3");
            name.textContent = item.name;
            const price = document.createElement("p");
            price.textContent = "$" + item.price.toFixed(2);
            info.appendChild(name);
            info.appendChild(price);

            const qtyBox = document.createElement("div");
            qtyBox.className = "cart-qty";
            const qtyNumber = document.createElement("span");
            qtyNumber.textContent = item.qty;

            qtyBox.appendChild(makeButton("−", "qty-button", function () {
                if (cart[index].qty > 1) {
                    cart[index].qty--;
                } else {
                    cart.splice(index, 1);
                }
                saveCart(cart);
                render();
            }));
            qtyBox.appendChild(qtyNumber);
            qtyBox.appendChild(makeButton("+", "qty-button", function () {
                cart[index].qty++;
                saveCart(cart);
                render();
            }));

            const lineTotal = document.createElement("strong");
            lineTotal.className = "cart-line-total";
            lineTotal.textContent = "$" + (item.price * item.qty).toFixed(2);

            const removeButton = makeButton("Remove", "remove-button", function () {
                cart.splice(index, 1);
                saveCart(cart);
                render();
            });

            row.appendChild(img);
            row.appendChild(info);
            row.appendChild(qtyBox);
            row.appendChild(lineTotal);
            row.appendChild(removeButton);
            itemsBox.appendChild(row);
        });

        totalBox.textContent = "$" + total.toFixed(2);

        if (counter) {
            counter.textContent = "My Cart (" + count + ")";
        }

        if (cart.length === 0) {
            emptyBox.style.display = "block";
            contentBox.style.display = "none";
        } else {
            emptyBox.style.display = "none";
            contentBox.style.display = "grid";
        }
    }

    document.getElementById("clearCart").addEventListener("click", function () {
        saveCart([]);
        render();
    });

    render();

})();
</script>

<?php include 'footer.php'; ?>