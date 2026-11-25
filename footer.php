<footer class="main-footer" id="contact">

        <div class="footer-container">

            <div class="footer-column footer-brand">

                <h2>STORE<span>FUSSION</span></h2>

                <p>
                    Your destination for quality products,
                    great discoveries and a better shopping
                    experience.
                </p>

            </div>


            <div class="footer-column">

                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#products">Shop</a></li>
                    <li><a href="#categories">Categories</a></li>
                    <li><a href="#about">About Us</a></li>
                </ul>

            </div>


            <div class="footer-column">

                <h3>Customer Service</h3>

                <ul>
                    <li><a href="#">My Account</a></li>
                    <li><a href="info.php?page=guide">Shopping Guide</a></li>
                    <li><a href="info.php?page=returns">Returns Policy</a></li>
                    <li><a href="info.php?page=privacy">Privacy Policy</a></li>
                </ul>

            </div>


            <div class="footer-column">

                <h3>Contact Us</h3>

                <ul>
                    <li>Email: support@storefussion0@gmail.com</li>
                </ul>

            </div>

        </div>


        <div class="footer-bottom">

            <p>
                &copy; <?php echo date("Y"); ?>
                MyShop. All Rights Reserved.
            </p>

            <p>Designed for a better shopping experience.</p>

        </div>

    </footer>

<script>
(function () {

    // Hero bubbles
    var hero = document.querySelector(".hero-section");
    if (hero) {
        var box = document.createElement("div");
        box.className = "bubbles";
        for (var i = 0; i < 14; i++) {
            var b = document.createElement("span");
            b.className = "bubble";
            var size = 15 + Math.random() * 55;
            b.style.width = size + "px";
            b.style.height = size + "px";
            b.style.left = Math.random() * 100 + "%";
            b.style.animationDuration = (8 + Math.random() * 12) + "s";
            b.style.animationDelay = (Math.random() * 8) + "s";
            box.appendChild(b);
        }
        hero.insertBefore(box, hero.firstChild);
    }

    // Scroll reveal (the featured slider is left alone)
    var targets = document.querySelectorAll(
        ".category-card, .benefit-item, .stat-card, .section-heading, " +
        ".promotion-content, .about-content, " +
        ".trending-section .product-card, .more-products .product-card"
    );

    if ("IntersectionObserver" in window) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    var el = entry.target;
                    el.classList.add("fx-show");
                    observer.unobserve(el);
                    setTimeout(function () { el.style.transitionDelay = ""; }, 1000);
                }
            });
        }, { threshold: 0.1 });

        targets.forEach(function (el, index) {
            el.classList.add("fx-hide");
            el.style.transitionDelay = ((index % 4) * 80) + "ms";
            observer.observe(el);
        });
    }

    // Added-to-cart pop-up
    var toast = document.createElement("div");
    toast.className = "fx-toast";
    document.body.appendChild(toast);
    var timer;

    document.addEventListener("click", function (e) {
        var button = e.target.closest(".add-cart");
        if (!button) return;
        toast.textContent = "✓ " + (button.dataset.name || "Item") + " added to cart";
        toast.classList.add("show");
        clearTimeout(timer);
        timer = setTimeout(function () { toast.classList.remove("show"); }, 1800);
    });

})();
</script>
<script>
(function () {

    function makeBubbles(section, count) {
        var box = document.createElement("div");
        box.className = "bubbles";
        for (var i = 0; i < count; i++) {
            var b = document.createElement("span");
            b.className = "bubble";
            var size = 15 + Math.random() * 55;
            b.style.width = size + "px";
            b.style.height = size + "px";
            b.style.left = Math.random() * 100 + "%";
            b.style.animationDuration = (8 + Math.random() * 12) + "s";
            b.style.animationDelay = (Math.random() * 8) + "s";
            box.appendChild(b);
        }
        section.classList.add("fx-section");
        section.insertBefore(box, section.firstChild);
    }

    // 1. Sparkles in the body sections
    var sections = document.querySelectorAll(
        ".category-section, .product-section, .more-products, .about-section"
    );
    sections.forEach(function (sec) {
        sec.classList.add("fx-section");
        var wrap = document.createElement("div");
        wrap.className = "sparkles";
        for (var i = 0; i < 12; i++) {
            var s = document.createElement("span");
            s.className = "sparkle";
            s.textContent = "✦";
            s.style.left = Math.random() * 100 + "%";
            s.style.top = Math.random() * 100 + "%";
            s.style.fontSize = (10 + Math.random() * 16) + "px";
            s.style.animationDuration = (3 + Math.random() * 4) + "s";
            s.style.animationDelay = (Math.random() * 5) + "s";
            wrap.appendChild(s);
        }
        sec.insertBefore(wrap, sec.firstChild);
    });

    // 2. Bubbles in the offer and newsletter sections
    var promo = document.querySelector(".promotion-section");
    if (promo) makeBubbles(promo, 12);
    var news = document.querySelector(".newsletter-section");
    if (news) makeBubbles(news, 12);

    // 3. Ticker band under the benefits
    var benefits = document.querySelector(".benefits-section");
    if (benefits) {
        var items = [
            "FAST DELIVERY",
            "NEW ARRIVALS EVERY WEEK",
            "QUALITY YOU CAN TRUST",
            "EXCLUSIVE DEALS",
            "SHOP WITH CONFIDENCE"
        ];
        var ticker = document.createElement("div");
        ticker.className = "ticker";
        var track = document.createElement("div");
        track.className = "ticker-track";
        for (var r = 0; r < 4; r++) {
            items.forEach(function (text) {
                var span = document.createElement("span");
                span.textContent = "✦ " + text;
                track.appendChild(span);
            });
        }
        ticker.appendChild(track);
        benefits.parentNode.insertBefore(ticker, benefits.nextSibling);
    }

    // 4. Count-up numbers
    var statHeads = document.querySelectorAll(".stat-card h3");
    if ("IntersectionObserver" in window) {
        var statObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                var h = entry.target;
                statObserver.unobserve(h);

                var match = h.textContent.trim().match(/^(\d+)(.*)$/);
                if (!match) return;
                var target = parseInt(match[1], 10);
                var suffix = match[2];
                var pad = match[1].length;
                var start = null;

                function step(time) {
                    if (start === null) start = time;
                    var progress = Math.min((time - start) / 1500, 1);
                    h.textContent = String(Math.round(target * progress)).padStart(pad, "0") + suffix;
                    if (progress < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
            });
        }, { threshold: 0.5 });

        statHeads.forEach(function (h) { statObserver.observe(h); });
    }

})();
</script>
<script>
(function () {
    var btn = document.getElementById("themeToggle");
    if (!btn) return;

    function apply(theme) {
        document.documentElement.setAttribute("data-theme", theme);
        btn.textContent = theme === "dark" ? "☀" : "🌙";
        try { localStorage.setItem("myshopTheme", theme); } catch (e) {}
    }

    apply(document.documentElement.getAttribute("data-theme"));

    btn.addEventListener("click", function () {
        var now = document.documentElement.getAttribute("data-theme");
        apply(now === "dark" ? "light" : "dark");
    });
})();
</script>
<script>
(function () {
    var back = document.createElement("div");
    back.className = "fx-modal-back";
    back.innerHTML =
        '<div class="fx-modal">' +
        '<div class="fx-modal-icon">👋</div>' +
        '<h3>Log out?</h3>' +
        '<p>You will need to log in again to see your orders.</p>' +
        '<div class="fx-modal-actions">' +
        '<button type="button" class="fx-modal-cancel">Stay</button>' +
        '<button type="button" class="fx-modal-ok">Log out</button>' +
        '</div></div>';
    document.body.appendChild(back);

    var target = "logout.php";

    function close() { back.classList.remove("show"); }

    document.addEventListener("click", function (e) {
        var link = e.target.closest('a[href$="logout.php"]');
        if (!link) return;
        e.preventDefault();
        e.stopImmediatePropagation();
        target = link.href;
        back.classList.add("show");
    }, true);

    back.querySelector(".fx-modal-cancel").addEventListener("click", close);
    back.addEventListener("click", function (e) { if (e.target === back) close(); });
    back.querySelector(".fx-modal-ok").addEventListener("click", function () {
        window.location.href = target;
    });
})();
</script>
<script>
(function () {
    var input = document.getElementById("productSearch");
    var button = document.getElementById("searchButton");
    if (!input) return;

    var cards = document.querySelectorAll(".product-card");
    var onShopPage = cards.length > 0;

    var note = document.createElement("div");
    note.className = "fx-toast";
    document.body.appendChild(note);
    var noteTimer;

    function say(text) {
        note.textContent = text;
        note.classList.add("show");
        clearTimeout(noteTimer);
        noteTimer = setTimeout(function () { note.classList.remove("show"); }, 2500);
    }

    function runSearch(scroll) {
        var term = input.value.toLowerCase().trim();
        var shown = 0;
        var first = null;

        cards.forEach(function (card) {
            var h = card.querySelector("h3");
            var c = card.querySelector(".product-category");
            var text = ((h ? h.textContent : "") + " " + (c ? c.textContent : "")).toLowerCase();
            var match = text.indexOf(term) !== -1;
            card.classList.toggle("search-hidden", !match);
            if (match) {
                shown++;
                if (!first) first = card;
            }
        });

        if (!scroll) return;
        if (term === "") return;

        if (shown === 0) {
            say('No products found for "' + input.value.trim() + '"');
            return;
        }
        say(shown + " product" + (shown === 1 ? "" : "s") + " found");
        first.scrollIntoView({ behavior: "smooth", block: "center" });
    }

    function go() {
        var term = input.value.trim();
        if (term === "") return;
        if (onShopPage) {
            runSearch(true);
        } else {
            window.location.href = "index.php?q=" + encodeURIComponent(term);
        }
    }

    if (button) button.addEventListener("click", go);

    input.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            go();
        }
    });

    input.addEventListener("input", function () {
        if (onShopPage) runSearch(false);
    });

    var q = new URLSearchParams(window.location.search).get("q");
    if (q && onShopPage) {
        input.value = q;
        setTimeout(function () { runSearch(true); }, 400);
    }
})();
</script>
<script>
(function () {

    // 1. Make sure each section has the right name tag
    var sections = [
        [".category-section", "categories"],
        [".product-section:not(.trending-section):not(.more-products)", "products"],
        [".trending-section", "trending"],
        [".promotion-section", "offers"],
        [".about-section", "about"],
        [".main-footer", "contact"]
    ];
    sections.forEach(function (pair) {
        var el = document.querySelector(pair[0]);
        if (el) { el.id = pair[1]; }
    });

    // 2. Point each menu button at its section
    var byText = {
        "CATEGORIES": "categories",
        "SHOP": "products",
        "TRENDING": "trending",
        "SPECIAL OFFERS": "offers",
        "ABOUT US": "about",
        "CONTACT": "contact"
    };
    document.querySelectorAll(".navigation a").forEach(function (a) {
        var key = a.textContent.trim().toUpperCase();
        if (byText[key]) { a.setAttribute("href", "index.php#" + byText[key]); }
    });

    // 3. Smooth scroll
    function goTo(hash) {
        if (!hash) return false;
        var el = document.getElementById(hash.replace("#", ""));
        if (!el) return false;
        el.scrollIntoView({ behavior: "smooth", block: "start" });
        return true;
    }

    function clean(path) { return path.replace(/index\.php$/, ""); }

    document.addEventListener("click", function (e) {
        var link = e.target.closest("a[href]");
        if (!link) return;
        var url;
        try { url = new URL(link.href, window.location.href); } catch (err) { return; }
        if (!url.hash) return;
        if (clean(url.pathname) !== clean(window.location.pathname)) return;
        if (goTo(url.hash)) {
            e.preventDefault();
            history.replaceState(null, "", url.hash);
        }
    });

    // 4. Arriving from another page with a section in the link
    if (window.location.hash) {
        setTimeout(function () { goTo(window.location.hash); }, 500);
    }

})();
</script>
<script>
(function () {

    var head = null;
    document.querySelectorAll("h1,h2,h3,h4,h5").forEach(function (h) {
        if (h.textContent.trim().toLowerCase() === "order summary") { head = h; }
    });
    if (!head) return;

    var card = head.parentElement;
    card.classList.add("sum-card");
    head.classList.add("sum-head");

    var all = Array.prototype.slice.call(card.querySelectorAll("*"));

    function deepest(list) {
        return list.filter(function (el) {
            return !list.some(function (o) { return o !== el && el.contains(o); });
        });
    }

    var itemRows = deepest(all.filter(function (el) {
        var t = el.textContent;
        return t.indexOf("\u00D7") !== -1 && t.indexOf("$") !== -1;
    }));
    itemRows.forEach(function (el) { el.classList.add("sum-row"); });

    var totals = deepest(all.filter(function (el) {
        var t = el.textContent.trim().toLowerCase();
        return t.indexOf("total") === 0 && el.textContent.indexOf("$") !== -1;
    }));
    if (totals.length) { totals[0].classList.add("sum-total"); }

})();
</script>
<?php include "support-widget.php"; ?>
<?php include "backtotop.php"; ?>
<script>
(function () {
    var form = document.getElementById("newsletterForm");
    if (!form) return;

    var note = document.createElement("small");
    note.className = "nl-note";
    note.textContent = "By subscribing you agree to get emails about new arrivals and offers. You can unsubscribe at any time.";
    form.insertAdjacentElement("afterend", note);

    var msg = document.createElement("div");
    msg.className = "nl-msg";
    note.insertAdjacentElement("afterend", msg);

    var button = form.querySelector("button");

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        button.disabled = true;
        var original = button.textContent;
        button.textContent = "...";
        msg.className = "nl-msg";

        fetch("subscribe.php", { method: "POST", body: new FormData(form) })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                msg.textContent = (data.ok ? "\u2713 " : "") + data.message;
                msg.className = "nl-msg show " + (data.ok ? "ok" : "bad");
                if (data.ok) form.reset();
            })
            .catch(function () {
                msg.textContent = "Sorry, something went wrong. Please try again.";
                msg.className = "nl-msg show bad";
            })
            .then(function () {
                button.disabled = false;
                button.textContent = original;
            });
    });
})();
</script>
</body>
</html>