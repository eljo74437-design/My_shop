(function () {
    var cfg = window.CARE_CONFIG;
    if (!cfg) return;

    var fab = document.getElementById("careFab");
    var panel = document.getElementById("carePanel");
    var body = document.getElementById("careBody");
    var chips = document.getElementById("careChips");
    var form = document.getElementById("careForm");
    var input = document.getElementById("careInput");
    var mail = document.getElementById("careMail");
    var started = false;

    function mailHref(text) {
        var subject = "Customer care - MyShop";
        var msg = "Hello MyShop team,\n\n" + (text ? text + "\n\n" : "") +
                  "Order number (if any): \nMy name: \n";
        return "mailto:" + cfg.email + "?subject=" + encodeURIComponent(subject) +
               "&body=" + encodeURIComponent(msg);
    }
    mail.href = mailHref("");

    function scrollDown() { body.scrollTop = body.scrollHeight; }

    function addMsg(text, who, withMail) {
        var d = document.createElement("div");
        d.className = "care-msg " + who;
        d.textContent = text;
        if (withMail) {
            var a = document.createElement("a");
            a.className = "care-msg-mail";
            a.href = mailHref("");
            a.textContent = "✉ Email us";
            d.appendChild(document.createElement("br"));
            d.appendChild(a);
        }
        body.appendChild(d);
        scrollDown();
    }

    function botReply(text, withMail) {
        var t = document.createElement("div");
        t.className = "care-msg bot care-typing";
        t.innerHTML = "<i></i><i></i><i></i>";
        body.appendChild(t);
        scrollDown();
        setTimeout(function () {
            body.removeChild(t);
            addMsg(text, "bot", withMail);
        }, 600);
    }

    function answer(q) {
        var s = q.toLowerCase();
        var best = null, bestScore = 0;
        cfg.faq.forEach(function (f) {
            var score = 0;
            f[1].forEach(function (k) { if (s.indexOf(k) !== -1) score += k.length; });
            if (score > bestScore) { bestScore = score; best = f; }
        });
        if (best) {
            var isComplaint = /complain|damaged|wrong|refund/.test(best[1].join(" ") + best[0].toLowerCase());
            botReply(best[2], isComplaint);
        } else if (/^(hi|hello|hey|good (morning|afternoon|evening))\b/.test(s)) {
            botReply("Hello! 😊 How can I help you today?");
        } else if (/thank/.test(s)) {
            botReply("You're welcome! Is there anything else I can help with?");
        } else {
            botReply(cfg.fallback, true);
        }
    }

    function ask(q) {
        addMsg(q, "me");
        mail.href = mailHref("Regarding: " + q);
        answer(q);
    }

    cfg.faq.forEach(function (f) {
        var b = document.createElement("button");
        b.type = "button";
        b.textContent = f[0];
        b.addEventListener("click", function () { ask(f[0]); });
        chips.appendChild(b);
    });

    function open() {
        panel.hidden = false;
        requestAnimationFrame(function () { panel.classList.add("open"); });
        fab.classList.add("on");
        fab.setAttribute("aria-expanded", "true");
        if (!started) { started = true; botReply(cfg.hello); }
    }
    function close() {
        panel.classList.remove("open");
        fab.classList.remove("on");
        fab.setAttribute("aria-expanded", "false");
        setTimeout(function () { if (!panel.classList.contains("open")) panel.hidden = true; }, 250);
    }

    fab.addEventListener("click", function () {
        panel.classList.contains("open") ? close() : open();
    });
    document.getElementById("careClose").addEventListener("click", close);
    document.addEventListener("keydown", function (e) { if (e.key === "Escape") close(); });

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        var v = input.value.trim();
        if (!v) return;
        input.value = "";
        ask(v);
    });
})();