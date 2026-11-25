document.addEventListener("DOMContentLoaded", function () {

    // ===== 1. CART =====

    const cartCounter = document.getElementById("cartCounter");
    const addButtons = document.querySelectorAll(".add-cart");

    function getCart() {
        let cart = [];
        try { cart = JSON.parse(localStorage.getItem("myshopCartItems")); } catch (e) {}
        if (!Array.isArray(cart)) cart = [];
        return cart;
    }

    function saveCart(cart) {
        try { localStorage.setItem("myshopCartItems", JSON.stringify(cart)); } catch (e) {}
    }

    function updateCart() {
        const total = getCart().reduce(function (sum, item) {
            return sum + item.qty;
        }, 0);
        if (cartCounter) {
            cartCounter.textContent = "My Cart (" + total + ")";

            const floatCount = document.getElementById("floatCartCount");
        if (floatCount) {
            floatCount.textContent = total;
            floatCount.classList.remove("bump");
            void floatCount.offsetWidth;
            floatCount.classList.add("bump");
        }
        }
    }

    updateCart();

    addButtons.forEach(function (button) {
        button.addEventListener("click", function () {

            const cart = getCart();
            const name = button.dataset.name;

            const existing = cart.find(function (item) {
                return item.name === name;
            });

            if (existing) {
                existing.qty++;
            } else {
                cart.push({
                    name: name,
                    price: Number(button.dataset.price),
                    image: button.dataset.image,
                    qty: 1
                });
            }

            saveCart(cart);
            updateCart();

            button.textContent = "ADDED ✓";
            button.classList.add("added");

            clearTimeout(button.resetTimer);
            button.resetTimer = setTimeout(function () {
                button.textContent = "ADD +";
                button.classList.remove("added");
            }, 1200);
        });
    });


    // ===== 2. SEARCH =====

    const searchInput = document.getElementById("productSearch");
    const searchButton = document.getElementById("searchButton");
    const productCards = document.querySelectorAll(".product-card");

    function searchProducts() {
        if (!searchInput) return;
        const term = searchInput.value.toLowerCase().trim();

        productCards.forEach(function (card) {
            const nameEl = card.querySelector("h3");
            const catEl = card.querySelector(".product-category");
            const text = (nameEl ? nameEl.textContent : "") + " " +
                         (catEl ? catEl.textContent : "");
            card.classList.toggle("search-hidden", !text.toLowerCase().includes(term));
        });
    }

    if (searchInput) searchInput.addEventListener("input", searchProducts);
    if (searchButton) searchButton.addEventListener("click", searchProducts);


    // ===== 3. FEATURED CAROUSEL =====

    const featuredGrid = document.querySelector("#products .product-grid");

    if (featuredGrid) {

        const carousel = document.createElement("div");
        carousel.className = "product-carousel";
        featuredGrid.parentNode.insertBefore(carousel, featuredGrid);
        carousel.appendChild(featuredGrid);

        featuredGrid.classList.remove("product-grid");
        featuredGrid.classList.add("product-carousel-track");

        const controls = document.createElement("div");
        controls.className = "carousel-controls";

        const previousButton = document.createElement("button");
        previousButton.type = "button";
        previousButton.className = "carousel-button";
        previousButton.textContent = "‹";

        const nextButton = document.createElement("button");
        nextButton.type = "button";
        nextButton.className = "carousel-button";
        nextButton.textContent = "›";

        controls.appendChild(previousButton);
        controls.appendChild(nextButton);
        carousel.appendChild(controls);

        function moveCarousel(direction) {
            const firstCard = featuredGrid.querySelector(".product-card");
            if (!firstCard) return;
            let gap = parseFloat(getComputedStyle(featuredGrid).columnGap);
            if (!gap) gap = 25;
            featuredGrid.scrollBy({
                left: direction * (firstCard.offsetWidth + gap),
                behavior: "smooth"
            });
        }

        previousButton.addEventListener("click", function () { moveCarousel(-1); });
        nextButton.addEventListener("click", function () { moveCarousel(1); });

        let carouselTimer;

        function startCarousel() {
            clearInterval(carouselTimer);
            carouselTimer = setInterval(function () {
                const atEnd = featuredGrid.scrollLeft + featuredGrid.clientWidth >=
                              featuredGrid.scrollWidth - 5;
                if (atEnd) {
                    featuredGrid.scrollTo({ left: 0, behavior: "smooth" });
                } else {
                    moveCarousel(1);
                }
            }, 3500);
        }

        startCarousel();

        carousel.addEventListener("mouseenter", function () { clearInterval(carouselTimer); });
        carousel.addEventListener("mouseleave", startCarousel);
        carousel.addEventListener("touchstart", function () { clearInterval(carouselTimer); }, { passive: true });
        carousel.addEventListener("touchend", startCarousel);
    }


    // ===== 4. COUNTDOWN =====

    const hoursDisplay = document.getElementById("hours");
    const minutesDisplay = document.getElementById("minutes");
    const secondsDisplay = document.getElementById("seconds");

    function updateCountdown() {
        if (!(hoursDisplay && minutesDisplay && secondsDisplay)) return;

        const now = new Date();
        const endOfDay = new Date(now);
        endOfDay.setHours(24, 0, 0, 0);

        const remaining = endOfDay.getTime() - now.getTime();
        const hours = Math.floor(remaining / 3600000);
        const minutes = Math.floor((remaining % 3600000) / 60000);
        const seconds = Math.floor((remaining % 60000) / 1000);

        hoursDisplay.textContent = String(hours).padStart(2, "0");
        minutesDisplay.textContent = String(minutes).padStart(2, "0");
        secondsDisplay.textContent = String(seconds).padStart(2, "0");
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);

    // ===== DRAGGABLE FLOATING CART =====

    const floatCart = document.querySelector(".float-cart");

    if (floatCart) {

        let dragging = false;
        let moved = false;
        let startX = 0;
        let startY = 0;
        let startLeft = 0;
        let startTop = 0;

        function placeCart(left, top) {
            const maxLeft = window.innerWidth - floatCart.offsetWidth - 5;
            const maxTop = window.innerHeight - floatCart.offsetHeight - 5;
            left = Math.min(Math.max(5, left), maxLeft);
            top = Math.min(Math.max(5, top), maxTop);
            floatCart.style.left = left + "px";
            floatCart.style.top = top + "px";
            floatCart.style.right = "auto";
            floatCart.style.bottom = "auto";
        }

        try {
            const saved = JSON.parse(localStorage.getItem("myshopCartPos"));
            if (saved) placeCart(saved.left, saved.top);
        } catch (e) {}

        floatCart.addEventListener("pointerdown", function (e) {
            dragging = true;
            moved = false;
            const rect = floatCart.getBoundingClientRect();
            startX = e.clientX;
            startY = e.clientY;
            startLeft = rect.left;
            startTop = rect.top;
            floatCart.setPointerCapture(e.pointerId);
        });

        floatCart.addEventListener("pointermove", function (e) {
            if (!dragging) return;
            const dx = e.clientX - startX;
            const dy = e.clientY - startY;
            if (Math.abs(dx) + Math.abs(dy) > 8) moved = true;
            if (moved) placeCart(startLeft + dx, startTop + dy);
        });

        floatCart.addEventListener("pointerup", function () {
            dragging = false;
            if (moved) {
                const rect = floatCart.getBoundingClientRect();
                try {
                    localStorage.setItem("myshopCartPos", JSON.stringify({ left: rect.left, top: rect.top }));
                } catch (e) {}
            }
        });

        floatCart.addEventListener("click", function (e) {
            if (moved) {
                e.preventDefault();
                moved = false;
            }
        });
    }
});