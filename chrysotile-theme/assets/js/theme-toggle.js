(function () {
	function updateThemeUI() {
		var buttons = document.querySelectorAll("[data-theme-toggle]");
		var logo = document.getElementById("chrysotile-logo");
		if (!buttons.length) {
			return;
		}

		var isDark = document.documentElement.classList.contains("theme-dark");
		buttons.forEach(function (button) {
			button.setAttribute("aria-pressed", String(isDark));

			if (button.classList.contains("chrysotile-theme-toggle--icon")) {
				var moon = button.querySelector("[data-theme-icon-moon]");
				var sun = button.querySelector("[data-theme-icon-sun]");
				if (moon && sun) {
					moon.hidden = isDark;
					sun.hidden = !isDark;
				}
				var lightLabel = button.getAttribute("data-label-light") || "";
				var darkLabel = button.getAttribute("data-label-dark") || "";
				button.setAttribute("aria-label", isDark ? lightLabel : darkLabel);
				return;
			}

			button.textContent = isDark ? "Темная" : "Светлая";
		});

		if (logo && logo.dataset) {
			var nextSrc = isDark ? logo.dataset.logoDark : logo.dataset.logoLight;
			if (nextSrc && logo.src !== nextSrc) {
				logo.src = nextSrc;
			}
		}
	}

	function applyTheme(theme) {
		if (theme === "dark") {
			document.documentElement.classList.add("theme-dark");
		} else {
			document.documentElement.classList.remove("theme-dark");
		}
		updateThemeUI();
	}

	var savedTheme = localStorage.getItem("chrysotileTheme");
	if (savedTheme) {
		applyTheme(savedTheme);
	} else {
		updateThemeUI();
	}

	document.addEventListener("click", function (event) {
		var button = event.target.closest("[data-theme-toggle]");
		if (!button) {
			return;
		}

		var isDark = document.documentElement.classList.contains("theme-dark");
		var nextTheme = isDark ? "light" : "dark";
		localStorage.setItem("chrysotileTheme", nextTheme);
		applyTheme(nextTheme);
	});
})();
