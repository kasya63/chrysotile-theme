(function () {
	"use strict";

	var wrap = document.querySelector(".chrysotile-nav-more-wrap");
	if (!wrap) {
		return;
	}

	var btn = wrap.querySelector(".chrysotile-nav-more");
	var panel = wrap.querySelector(".chrysotile-nav-more-dropdown");
	if (!btn || !panel) {
		return;
	}

	function setOpen(open) {
		wrap.classList.toggle("is-open", open);
		btn.setAttribute("aria-expanded", open ? "true" : "false");
	}

	btn.addEventListener("click", function (e) {
		e.preventDefault();
		e.stopPropagation();
		setOpen(!wrap.classList.contains("is-open"));
	});

	document.addEventListener("click", function (e) {
		if (!wrap.contains(e.target)) {
			setOpen(false);
		}
	});

	document.addEventListener("keydown", function (e) {
		if (e.key === "Escape") {
			setOpen(false);
		}
	});

	panel.addEventListener("click", function (e) {
		if (e.target && e.target.closest("a")) {
			setOpen(false);
		}
	});
})();
