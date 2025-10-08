(() => {
	const getStoredTheme = () => localStorage.getItem("theme");
	const setStoredTheme = (theme) => localStorage.setItem("theme", theme);

	const getPreferredTheme = () => {
		const storedTheme = getStoredTheme();
		if (storedTheme) {
			return storedTheme;
		}

		return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
	};

	const setTheme = (theme) => {
		if (theme === "auto") {
			document.documentElement.setAttribute(
				"data-bs-theme",
				window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"
			);

			document.querySelectorAll(".card").forEach((card) => {
				card.setAttribute(
					"data-bs-theme",
					window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"
				);
			});

			document
				.querySelector(".navbar")
				.setAttribute(
					"data-bs-theme",
					window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"
				);
		} else {
			document.documentElement.setAttribute("data-bs-theme", theme);

			document.querySelectorAll(".card").forEach((card) => {
				card.setAttribute("data-bs-theme", theme);
			});

			document.querySelector(".navbar").setAttribute("data-bs-theme", theme);
		}

		updateIconsForTheme(document.documentElement.getAttribute("data-bs-theme"));
	};

	function updateIconsForTheme(theme) {
		const icons = document.querySelectorAll(".theme-icon");
		icons.forEach((icon) => {
			if (!icon.src) return;
			if (theme === "dark" && icon.src.includes("/light/")) {
				icon.src = icon.src.replace("/light/", "/dark/");
			} else if (theme === "light" && icon.src.includes("/dark/")) {
				icon.src = icon.src.replace("/dark/", "/light/");
			}
		});
	}

	// Apply saved theme
	setTheme(getPreferredTheme());

	const showActiveTheme = (theme, focus = false) => {
		const themeSwitcher = document.querySelector("#bd-theme");
		if (!themeSwitcher) return;

		const activeIcon = document.querySelector(".theme-icon-active");
		const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`);

		// Update all buttons
		document.querySelectorAll("[data-bs-theme-value]").forEach((element) => {
			element.classList.remove("active");
			element.setAttribute("aria-pressed", "false");
		});

		btnToActive.classList.add("active");
		btnToActive.setAttribute("aria-pressed", "true");

		// Update button icons
		const icon = btnToActive.querySelector(".theme-icon").cloneNode(true);
		icon.classList.remove("me-2");
		activeIcon.replaceWith(icon);
		icon.classList.add("theme-icon-active");
		icon.classList.add("lh-1");

		if (focus) themeSwitcher.focus();
	};

	// Update theme automatically if system preference changes and user chose “auto”
	window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", () => {
		const storedTheme = getStoredTheme();
		if (storedTheme === "auto") {
			setTheme(getPreferredTheme());
		}
	});

	window.addEventListener("DOMContentLoaded", () => {
		// Use stored theme, not system preference
		const currentTheme = getStoredTheme() || getPreferredTheme();
		showActiveTheme(currentTheme);

		document.querySelectorAll("[data-bs-theme-value]").forEach((toggle) => {
			toggle.addEventListener("click", () => {
				const theme = toggle.getAttribute("data-bs-theme-value");
				setStoredTheme(theme);
				setTheme(theme);
				showActiveTheme(theme, true);
			});
		});
	});

	// Watch for manual theme changes
	const observer = new MutationObserver(() => {
		const newTheme = document.documentElement.getAttribute("data-bs-theme");
		updateIconsForTheme(newTheme);
	});
	observer.observe(document.documentElement, { attributes: true, attributeFilter: ["data-bs-theme"] });
})();
