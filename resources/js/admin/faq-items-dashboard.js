const ROOT_SELECTOR = "#faq-items-dashboard";
const FILTER_FORM_SELECTOR = "#faq-items-filters-form";
const RESULTS_SELECTOR = "#faq-items-results";

const debounce = (fn, wait = 300) => {
	let timeout;
	return (...args) => {
		clearTimeout(timeout);
		timeout = setTimeout(() => fn(...args), wait);
	};
};

const initFaqItemsDashboard = () => {
	const root = document.querySelector(ROOT_SELECTOR);
	if (!root) {
		return;
	}

	const form = root.querySelector(FILTER_FORM_SELECTOR);
	const route = root.dataset.filterResultsRoute;
	const statusTemplate = root.dataset.statusToggleTemplate;
	const csrfToken = root.dataset.csrfToken || "";
	if (!form || !route || !statusTemplate) {
		return;
	}

	const bindStatusToggles = () => {
		root.querySelectorAll("[data-status-toggle]").forEach((toggle) => {
			toggle.addEventListener("change", async () => {
				const id = toggle.dataset.id;
				const prev = !toggle.checked;
				try {
					const response = await fetch(statusTemplate.replace("__ITEM__", id), {
						method: "POST",
						headers: {
							"X-CSRF-TOKEN": csrfToken,
							"X-Requested-With": "XMLHttpRequest",
							Accept: "application/json",
						},
					});
					if (!response.ok) {
						throw new Error("Unable to toggle");
					}
				} catch (error) {
					toggle.checked = prev;
				}
			});
		});
	};

	const bindPagination = () => {
		root.querySelectorAll("[data-pagination-container] a").forEach((link) => {
			link.addEventListener("click", (event) => {
				event.preventDefault();
				const href = link.getAttribute("href");
				if (!href) {
					return;
				}
				const url = new URL(href, window.location.origin);
				refresh(url.searchParams);
			});
		});
	};

	const rebind = () => {
		bindStatusToggles();
		bindPagination();
	};

	const refresh = async (params = new URLSearchParams(new FormData(form))) => {
		const response = await fetch(`${route}?${params.toString()}`, {
			headers: {
				"X-Requested-With": "XMLHttpRequest",
				Accept: "text/html",
			},
		});
		if (!response.ok) {
			return;
		}
		const html = await response.text();
		const oldResults = root.querySelector(RESULTS_SELECTOR);
		if (!oldResults) {
			return;
		}
		oldResults.outerHTML = html;
		rebind();
	};

	const debouncedRefresh = debounce(() => {
		const params = new URLSearchParams(new FormData(form));
		params.delete("page");
		refresh(params);
	}, 350);

	form.addEventListener("submit", (event) => {
		event.preventDefault();
		const params = new URLSearchParams(new FormData(form));
		params.delete("page");
		refresh(params);
	});

	const search = form.querySelector("[name=\"q\"]");
	if (search) {
		search.addEventListener("input", debouncedRefresh);
	}

	const status = form.querySelector("[name=\"status\"]");
	if (status) {
		status.addEventListener("change", debouncedRefresh);
	}

	rebind();
};

if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", initFaqItemsDashboard);
} else {
	initFaqItemsDashboard();
}
