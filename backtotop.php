<style>
.to-top {
    position: fixed;
    right: 18px;
    bottom: 92px;
    z-index: 998;
    width: 58px;
    height: 58px;
    border: 0;
    border-radius: 50%;
    cursor: pointer;
    background: conic-gradient(#7FD9A8 var(--p, 0%), rgba(255, 255, 255, 0.18) 0);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
    opacity: 0;
    visibility: hidden;
    transform: translateY(14px);
    transition: opacity 0.3s, transform 0.3s, visibility 0.3s;
}
.to-top.show { opacity: 1; visibility: visible; transform: none; }
.to-top span {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #071B35;
    color: #7FD9A8;
    font-size: 17px;
    font-weight: bold;
    line-height: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
    transition: background 0.3s, color 0.3s;
}
.to-top span small {
    font-size: 9px;
    letter-spacing: 1px;
    font-weight: 700;
}
.to-top:hover span { background: #2FA36B; color: #fff; }
@media (max-width: 650px) {
    .to-top { right: 14px; bottom: 80px; width: 52px; height: 52px; }
    .to-top span { width: 44px; height: 44px; }
}
</style>

<button type="button" class="to-top" id="toTop" title="Back to top" aria-label="Back to top"><span>&#8593;<small>TOP</small></span></button>

<script>
(function () {
    var btn = document.getElementById("toTop");
    if (!btn) return;

    function update() {
       var y = window.pageYOffset;
        if (!y) { y = document.documentElement.scrollTop; }
        var max = document.documentElement.scrollHeight - window.innerHeight;
        var pct = max > 0 ? Math.min(100, Math.round((y / max) * 100)) : 0;
        btn.style.setProperty("--p", pct + "%");
        btn.classList.toggle("show", y > 300);
    }

    window.addEventListener("scroll", update, { passive: true });
    window.addEventListener("resize", update);
    update();

    btn.addEventListener("click", function () {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
})();
</script>