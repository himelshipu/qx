const ROOT_SELECTOR = "#users-dashboard";
const FILTER_FORM_SELECTOR = "#users-filters-form";
const RESULTS_SELECTOR = "#users-results";

const debounce = (fn, wait = 300) => {
	let timeout;
	return (...args) => {
		clearTimeout(timeout);
		timeout = setTimeout(() => fn(...args), wait);
	};
};

const initUsersDashboard = () => {
	const root = document.querySelector(ROOT_SELECTOR);
	if (!root) {
		return;
	}

	const form = root.querySelector(FILTER_FORM_SELECTOR);
	const results = root.querySelector(RESULTS_SELECTOR);
	const tableRoute = root.dataset.filterResultsRoute;
	const statusToggleTemplate = root.dataset.statusToggleTemplate;
	const csrfToken = root.dataset.csrfToken || "";
	if (!form || !results || !tableRoute || !statusToggleTemplate) {
		return;
	}

	let activeRequestController = null;

	const updateUrl = (params) => {
		const url = new URL(window.location.href);
		["q", "status", "role", "page"].forEach((key) => {
			const value = params.get(key);
			if (value && value !== "all") {
				url.searchParams.set(key, value);
			} else {
				url.searchParams.delete(key);
			}
		});
		window.history.replaceState({}, "", url);
	};

	const bindStatusToggles = () => {
		root.querySelectorAll("[data-status-toggle]").forEach((toggle) => {
			toggle.addEventListener("change", async () => {
				const id = toggle.dataset.id;
				const previous = !toggle.checked;
				toggle.disabled = true;
				try {
					const response = await fetch(statusToggleTemplate.replace("__ID__", id), {
						method: "POST",
						headers: {
							"X-CSRF-TOKEN": csrfToken,
							"X-Requested-With": "XMLHttpRequest",
							Accept: "application/json",
						},
					});
					if (!response.ok) {
						throw new Error("Failed to update user status");
					}
					const data = await response.json();
					if (!data.success) {
						throw new Error(data.message || "Failed to update user status");
					}
					if (window.toast) {
						window.toast.success(data.message || "User status updated successfully.");
					}
				} catch (error) {
					toggle.checked = previous;
					if (window.toast) {
						window.toast.error(error?.message || "Unable to update user status right now.");
					}
				} finally {
					toggle.disabled = false;
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
		if (activeRequestController) {
			activeRequestController.abort();
		}
		activeRequestController = new AbortController();
		updateUrl(params);

		try {
			const response = await fetch(`${tableRoute}?${params.toString()}`, {
				headers: {
					"X-Requested-With": "XMLHttpRequest",
					Accept: "application/json",
				},
				signal: activeRequestController.signal,
			});

			if (!response.ok) {
				throw new Error("Failed to filter users");
			}

			const data = await response.json();
			if (!data.success || !data.html) {
				throw new Error("Invalid response");
			}

			const current = root.querySelector(RESULTS_SELECTOR);
			if (current) {
				current.outerHTML = data.html;
				rebind();
			}
		} catch (error) {
			if (error.name !== "AbortError") {
				window.location.href = `${form.action}?${params.toString()}`;
			}
		}
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

	const searchInput = form.querySelector("[name=\"q\"]");
	if (searchInput) {
		searchInput.addEventListener("input", debouncedRefresh);
	}

	["status", "role"].forEach((name) => {
		const input = form.querySelector(`[name=\"${name}\"]`);
		if (input) {
			input.addEventListener("change", debouncedRefresh);
		}
	});

	rebind();
};

if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", initUsersDashboard);
} else {
	initUsersDashboard();
}
