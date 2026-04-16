import Sortable from "sortablejs";

const ROOT_SELECTOR = "#knowledge-base-dashboard";
const FILTER_FORM_SELECTOR = "#knowledge-base-filters-form";
const RESULTS_SELECTOR = "#knowledge-base-results";
const SORTABLE_SELECTOR = "#knowledge-base-sortable";

const debounce = (fn, wait = 300) => {
	let timeout;
	return (...args) => {
		clearTimeout(timeout);
		timeout = setTimeout(() => fn(...args), wait);
	};
};

const updateUrl = (params) => {
	const url = new URL(window.location.href);
	["q", "status", "featured", "page"].forEach((key) => {
		const value = params.get(key);
		if (value && value !== "all") {
			url.searchParams.set(key, value);
		} else {
			url.searchParams.delete(key);
		}
	});
	window.history.replaceState({}, "", url);
};

const initKnowledgeBaseDashboard = () => {
	const root = document.querySelector(ROOT_SELECTOR);
	if (!root) {
		return;
	}

	const filterForm = root.querySelector(FILTER_FORM_SELECTOR);
	const resultsContainer = root.querySelector(RESULTS_SELECTOR);
	if (!filterForm || !resultsContainer) {
		return;
	}

	const csrfToken = root.dataset.csrfToken || "";
	const filterRoute = root.dataset.filterResultsRoute;
	const reorderRoute = root.dataset.reorderRoute;
	const statusTemplate = root.dataset.statusToggleTemplate;

	const bindPagination = () => {
		const links = root.querySelectorAll("[data-pagination-container] a");
		links.forEach((link) => {
			link.addEventListener("click", async (event) => {
				event.preventDefault();
				const href = link.getAttribute("href");
				if (!href) {
					return;
				}
				const url = new URL(href, window.location.origin);
				await refreshResults(url.searchParams);
			});
		});
	};

	const bindStatusToggles = () => {
		const toggles = root.querySelectorAll("[data-status-toggle]");
		toggles.forEach((toggle) => {
			toggle.addEventListener("change", async () => {
				if (!statusTemplate) {
					return;
				}
				const articleId = toggle.dataset.id;
				const prev = !toggle.checked;
				const endpoint = statusTemplate.replace("__ID__", articleId);

				try {
					const response = await fetch(endpoint, {
						method: "POST",
						headers: {
							"X-CSRF-TOKEN": csrfToken,
							"X-Requested-With": "XMLHttpRequest",
							Accept: "application/json",
						},
					});

					if (!response.ok) {
						throw new Error("Unable to update status");
					}

					const data = await response.json();
					const publishedAtNode = root.querySelector(`[data-published-at=\"${articleId}\"]`);
					if (publishedAtNode) {
						publishedAtNode.textContent = data.published_at || "-";
					}
				} catch (error) {
					toggle.checked = prev;
				}
			});
		});
	};

	const bindReorder = () => {
		const sortableEl = root.querySelector(SORTABLE_SELECTOR);
		if (!sortableEl || !reorderRoute) {
			return;
		}

		Sortable.create(sortableEl, {
			animation: 150,
			handle: "button",
			onEnd: async () => {
				const orderedIds = Array.from(sortableEl.querySelectorAll("tr[data-id]")).map((row) => row.dataset.id);
				try {
					await fetch(reorderRoute, {
						method: "POST",
						headers: {
							"Content-Type": "application/json",
							"X-CSRF-TOKEN": csrfToken,
							"X-Requested-With": "XMLHttpRequest",
							Accept: "application/json",
						},
						body: JSON.stringify({ ordered_ids: orderedIds }),
					});
				} catch (error) {
					// Keep UI order; server-side order can be retried by another drag.
				}
			},
		});
	};

	const rebind = () => {
		bindPagination();
		bindStatusToggles();
		bindReorder();
	};

	const refreshResults = async (params = new URLSearchParams(new FormData(filterForm))) => {
		if (!filterRoute) {
			return;
		}
		updateUrl(params);

		const endpoint = `${filterRoute}?${params.toString()}`;
		const response = await fetch(endpoint, {
			headers: {
				"X-Requested-With": "XMLHttpRequest",
				Accept: "text/html",
			},
		});

		if (!response.ok) {
			return;
		}

		const html = await response.text();
		resultsContainer.outerHTML = html;
		const nextResults = root.querySelector(RESULTS_SELECTOR);
		if (nextResults) {
			rebind();
		}
	};

	const debouncedRefresh = debounce(() => {
		const params = new URLSearchParams(new FormData(filterForm));
		params.delete("page");
		refreshResults(params);
	}, 350);

	filterForm.addEventListener("submit", (event) => {
		event.preventDefault();
		const params = new URLSearchParams(new FormData(filterForm));
		params.delete("page");
		refreshResults(params);
	});

	["status", "featured"].forEach((name) => {
		const input = filterForm.querySelector(`[name=\"${name}\"]`);
		if (!input) {
			return;
		}
		input.addEventListener("change", debouncedRefresh);
	});

	const searchInput = filterForm.querySelector("[name=\"q\"]");
	if (searchInput) {
		searchInput.addEventListener("input", debouncedRefresh);
	}

	rebind();
};

if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", initKnowledgeBaseDashboard);
} else {
	initKnowledgeBaseDashboard();
}
