const ROOT_SELECTOR = "#user-role-assignment";

const initUserRoleAssignment = () => {
	const root = document.querySelector(ROOT_SELECTOR);
	if (!root) {
		return;
	}

	const userSelect = root.querySelector("#user_id");
	const form = root.querySelector("#user-role-assignment-form");
	const clearButton = root.querySelector("#clear-role-selection");
	const submitButton = root.querySelector("#assign-roles-submit");
	const roleCount = root.querySelector("#selected-role-count");
	const roleBox = root.querySelector("#current-role-box");
	const roleChips = root.querySelector("#current-role-chips");
	const fetchTemplate = root.dataset.fetchUserRolesTemplate;
	const assignRoute = root.dataset.assignRoute;
	const csrfToken = root.dataset.csrfToken || "";
	if (!userSelect || !form || !clearButton || !submitButton || !roleCount || !roleBox || !roleChips || !fetchTemplate || !assignRoute) {
		return;
	}

	const roleCheckboxes = () => Array.from(root.querySelectorAll('input[name="roles[]"]'));

	const updateSelectedCount = () => {
		const selected = roleCheckboxes().filter((checkbox) => checkbox.checked).length;
		roleCount.textContent = `Selected: ${selected}`;
	};

	const setCheckedRoles = (roleIds = []) => {
		const roleIdSet = new Set(roleIds.map((id) => String(id)));
		roleCheckboxes().forEach((checkbox) => {
			checkbox.checked = roleIdSet.has(checkbox.value);
		});
		updateSelectedCount();
	};

	const getCheckedRoleIds = () => {
		return roleCheckboxes()
			.filter((checkbox) => checkbox.checked)
			.map((checkbox) => Number.parseInt(checkbox.value, 10))
			.filter((id) => Number.isInteger(id));
	};

	const renderCurrentRoleChips = () => {
		const checkedLabels = roleCheckboxes()
			.filter((checkbox) => checkbox.checked)
			.map((checkbox) => {
				const label = root.querySelector(`label[for=\"${checkbox.id}\"] span`);
				return label ? label.textContent.trim() : null;
			})
			.filter(Boolean);

		roleChips.innerHTML = "";
		if (checkedLabels.length === 0) {
			roleBox.classList.add("hidden");
			return;
		}

		checkedLabels.forEach((name) => {
			const chip = document.createElement("span");
			chip.className =
				"rounded-full border border-blue-200 bg-white px-3 py-1.5 text-xs font-semibold text-blue-700 shadow-sm dark:border-blue-700 dark:bg-blue-900/50 dark:text-blue-300";
			chip.textContent = name;
			roleChips.appendChild(chip);
		});

		roleBox.classList.remove("hidden");
	};

	const loadUserRoles = async () => {
		const userId = userSelect.value;
		if (!userId) {
			setCheckedRoles([]);
			renderCurrentRoleChips();
			return;
		}

		submitButton.disabled = true;
		try {
			const response = await fetch(fetchTemplate.replace("__ID__", userId), {
				headers: {
					Accept: "application/json",
				},
			});
			const data = await response.json();
			if (!response.ok || !data.success) {
				throw new Error(data.message || "Failed to load user roles.");
			}

			setCheckedRoles(data.roleIds || []);
			renderCurrentRoleChips();
		} catch (error) {
			if (window.toast) {
				window.toast.error(error?.message || "Error loading user roles.");
			}
		} finally {
			submitButton.disabled = false;
		}
	};

	const submitRoleAssignment = async () => {
		if (!userSelect.value) {
			if (window.toast) {
				window.toast.error("Please select a user.");
			}
			return;
		}

		submitButton.disabled = true;
		const previousText = submitButton.textContent;
		submitButton.textContent = "Assigning...";

		try {
			const payload = {
				user_id: Number.parseInt(userSelect.value, 10),
				roles: getCheckedRoleIds(),
			};

			const response = await fetch(assignRoute, {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					"X-CSRF-TOKEN": csrfToken,
					"X-Requested-With": "XMLHttpRequest",
					Accept: "application/json",
				},
				body: JSON.stringify(payload),
			});
			const data = await response.json();
			if (!response.ok || !data.success) {
				throw new Error(data.message || "Failed to assign roles.");
			}

			if (window.toast) {
				window.toast.success(data.message || "Roles assigned successfully.");
			}
			renderCurrentRoleChips();
		} catch (error) {
			if (window.toast) {
				window.toast.error(error?.message || "Error assigning roles.");
			}
		} finally {
			submitButton.disabled = false;
			submitButton.textContent = previousText;
		}
	};

	userSelect.addEventListener("change", loadUserRoles);
	form.addEventListener("submit", (event) => {
		event.preventDefault();
		submitRoleAssignment();
	});

	clearButton.addEventListener("click", () => {
		roleCheckboxes().forEach((checkbox) => {
			checkbox.checked = false;
		});
		updateSelectedCount();
		renderCurrentRoleChips();
	});

	roleCheckboxes().forEach((checkbox) => {
		checkbox.addEventListener("change", () => {
			updateSelectedCount();
			renderCurrentRoleChips();
		});
	});

	updateSelectedCount();
};

if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", initUserRoleAssignment);
} else {
	initUserRoleAssignment();
}
