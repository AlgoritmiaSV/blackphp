document.addEventListener("DOMContentLoaded", () => {
const tabsContainer = document.querySelector(".tabs");
const tabs = tabsContainer.querySelectorAll(".tab");
const contents = tabsContainer.querySelectorAll(".tab-content");
const hiddenInput = tabsContainer.querySelector('input[type="hidden"]');

tabs.forEach(tab => {
	tab.addEventListener("click", () => {
		// Remove active class from all tabs and contents
		tabs.forEach(t => t.classList.remove("active"));
		contents.forEach(c => c.classList.remove("active"));

		// Add active class to clicked tab and corresponding content
		tab.classList.add("active");
		const target = tabsContainer.querySelector(`#${tab.dataset.tab}`);
		if (target) target.classList.add("active");

		// Update hidden input value
		if (hiddenInput) hiddenInput.value = tab.dataset.tab;
	});
});

// Optional: Set initial value on page load
const activeTab = tabsContainer.querySelector(".tab.active");
if (activeTab && hiddenInput) {
	hiddenInput.value = activeTab.dataset.tab;
}
});
