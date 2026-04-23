(function () {
	var mq = window.matchMedia("(max-width: 1023px)");
	var burger = document.querySelector(".chrysotile-nav-burger");
	var drawer = document.getElementById("chrysotile-nav-drawer");
	var closeBtn = document.querySelector(".chrysotile-nav-drawer-close");
	var scrim = document.querySelector(".chrysotile-nav-drawer-scrim");

	if (!burger || !drawer) {
		return;
	}

	function setOpen(open) {
		drawer.classList.toggle("is-open", open);
		burger.setAttribute("aria-expanded", open ? "true" : "false");
		drawer.setAttribute("aria-hidden", open ? "false" : "true");
		document.body.classList.toggle("chrysotile-nav-open", open);
		document.body.style.overflow = open ? "hidden" : "";
	}

	function onBurgerClick() {
		if (!mq.matches) {
			return;
		}
		setOpen(!drawer.classList.contains("is-open"));
	}

	function close() {
		setOpen(false);
	}

	burger.addEventListener("click", onBurgerClick);
	if (closeBtn) {
		closeBtn.addEventListener("click", close);
	}
	if (scrim) {
		scrim.addEventListener("click", close);
	}

	document.addEventListener("keydown", function (e) {
		if (e.key === "Escape" && drawer.classList.contains("is-open")) {
			close();
		}
	});

	mq.addEventListener("change", function () {
		if (!mq.matches) {
			close();
		}
	});
})();
