(function () {
	var wrap = document.querySelector('.chrysotile-main-search--toggle');
	if (!wrap) {
		return;
	}

	var btn = wrap.querySelector('.chrysotile-search-toggle');
	var panel = wrap.querySelector('.chrysotile-search-dropdown');
	var input = wrap.querySelector('input[type="search"]');

	if (!btn || !panel) {
		return;
	}

	function setOpen(open) {
		wrap.classList.toggle('is-open', open);
		btn.setAttribute('aria-expanded', open ? 'true' : 'false');
		panel.hidden = !open;
		if (open && input) {
			window.setTimeout(function () {
				input.focus();
			}, 20);
		}
	}

	btn.addEventListener('click', function (e) {
		e.stopPropagation();
		setOpen(!wrap.classList.contains('is-open'));
	});

	document.addEventListener('click', function (e) {
		if (!wrap.classList.contains('is-open')) {
			return;
		}
		if (!wrap.contains(e.target)) {
			setOpen(false);
		}
	});

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && wrap.classList.contains('is-open')) {
			setOpen(false);
			btn.focus();
		}
	});
})();
