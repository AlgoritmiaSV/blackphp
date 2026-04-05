document.addEventListener('DOMContentLoaded', function (){
	const tables = document.querySelectorAll("table.data_viewer");

	tables.forEach(table => {
		const headers = table.querySelectorAll("th");
		const tbody = table.querySelector("tbody");
		let sortDirection = 1;
		let currentIndex = -1;

		headers.forEach((header, index) => {
			const indicator = document.createElement("span");
			indicator.className = "sort-indicator";
			header.appendChild(indicator);
			header.style.cursor = "pointer";

			header.addEventListener("click", () => {
				if (index === currentIndex) sortDirection *= -1;
				else {
					sortDirection = 1;
					currentIndex = index;
				}

				// Update visual indicators
				headers.forEach((h, i) => {
					h.querySelector(".sort-indicator").textContent = i === index
						? (sortDirection === 1 ? "▲" : "▼")
						: "";
				});

				const allRows = Array.from(tbody.querySelectorAll("tr"));
				const dataRows = allRows.filter(row => !row.classList.contains("void_tr"));
				const voidRows = allRows.filter(row => row.classList.contains("void_tr"));

				dataRows.sort((a, b) => {
					const aCell = a.children[index];
					const bCell = b.children[index];

					const aValue = aCell.dataset.order ?? aCell.textContent.trim();
					const bValue = bCell.dataset.order ?? bCell.textContent.trim();

					if (!isNaN(aValue) && !isNaN(bValue)) {
						return (Number(aValue) - Number(bValue)) * sortDirection;
					}
					return aValue.localeCompare(bValue) * sortDirection;
				});

				const fragment = document.createDocumentFragment();
				dataRows.forEach(row => fragment.appendChild(row));
				voidRows.forEach(row => fragment.appendChild(row));

				tbody.innerHTML = "";
				tbody.appendChild(fragment);
			});
		});
	});
});
