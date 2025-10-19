function menuSearchEvent () {
	const searchInput = document.querySelector(".menu_search");

	if (searchInput) {
		searchInput.addEventListener("input", function () {
			const query = this.value.toLowerCase();
			const containers = document.querySelectorAll(".menu_container");

			containers.forEach(container => {
				const items = container.querySelectorAll(".menu_item");
				let visibleCount = 0;

				items.forEach(item => {
					const text = item.textContent.toLowerCase();
					if (text.includes(query)) {
						item.style.display = "";
						visibleCount++;
					} else {
						item.style.display = "none";
					}
				});

				// Hide container and its title if no items are visible
				const title = container.previousElementSibling;
				if (visibleCount === 0) {
					container.style.display = "none";
					if (title && title.classList.contains("menu_title")) {
						title.style.display = "none";
					}
				} else {
					container.style.display = "";
					if (title && title.classList.contains("menu_title")) {
						title.style.display = "";
					}
				}
			});
		});
	}
};
