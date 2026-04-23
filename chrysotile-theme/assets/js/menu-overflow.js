(function () {
    function elementWidth(el) {
        if (!el || el.classList.contains("is-hidden-overflow")) {
            return 0;
        }
        var styles = window.getComputedStyle(el);
        return (
            el.offsetWidth +
            parseFloat(styles.marginLeft || 0) +
            parseFloat(styles.marginRight || 0)
        );
    }

    function buildOverflowMenu() {
        var container = document.querySelector(".chrysotile-main-nav-links");
        if (!container) {
            return;
        }

        var overflowTarget = container.querySelector("[data-overflow-target]");
        var categoryLinks = Array.prototype.slice.call(
            container.querySelectorAll("[data-overflow-item]")
        );
        var fixedItems = Array.prototype.slice.call(
            container.querySelectorAll(".chrysotile-logo, .chrysotile-nav-more-wrap, .chrysotile-radio-btn, .chrysotile-theme-toggle")
        );

        if (!overflowTarget || !categoryLinks.length) {
            return;
        }

        overflowTarget.innerHTML = "";
        categoryLinks.forEach(function (link) {
            link.classList.remove("is-hidden-overflow");
            link.removeAttribute("data-first-visible");
        });

        var totalWidth = 0;
        fixedItems.forEach(function (item) {
            totalWidth += elementWidth(item);
        });
        categoryLinks.forEach(function (link) {
            totalWidth += elementWidth(link);
        });

        var availableWidth = container.clientWidth - 10;
        for (var i = categoryLinks.length - 1; i >= 0 && totalWidth > availableWidth; i -= 1) {
            var linkToHide = categoryLinks[i];
            linkToHide.classList.add("is-hidden-overflow");
            totalWidth -= elementWidth(linkToHide);

            var clone = linkToHide.cloneNode(true);
            clone.classList.remove("is-hidden-overflow");
            clone.removeAttribute("data-overflow-item");
            overflowTarget.prepend(clone);
        }

        var firstVisible = categoryLinks.find(function (link) {
            return !link.classList.contains("is-hidden-overflow");
        });
        if (firstVisible) {
            firstVisible.setAttribute("data-first-visible", "true");
        }
    }

    window.addEventListener("resize", buildOverflowMenu);
    window.addEventListener("load", buildOverflowMenu);
    document.addEventListener("DOMContentLoaded", buildOverflowMenu);
})();
