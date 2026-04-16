const ROOT_SELECTOR = "#permission-assignment-dashboard";

const initPermissionAssignment = () => {
	const root = document.querySelector(ROOT_SELECTOR);
	if (!root) {
		return;
	}

	const form = root.querySelector("#permission-assignment-form");
	const roleSelect = root.querySelector("#role_id");
	const checkAllButton = root.querySelector("#permissions-check-all");
	const uncheckAllButton = root.querySelector("#permissions-uncheck-all");
	const moduleToggleButtons = Array.from(root.querySelectorAll(".module-toggle"));
	const fetchTemplate = root.dataset.fetchRolePermissionsTemplate;
	if (!form || !roleSelect || !checkAllButton || !uncheckAllButton || !fetchTemplate) {
		return;
	}

	const checkboxes = () => Array.from(root.querySelectorAll(".permission-checkbox"));

	const setAllChecked = (checked) => {
		checkboxes().forEach((checkbox) => {
			checkbox.checked = checked;
		});
	};

	const setModuleChecked = (moduleName, checked) => {
		checkboxes()
			.filter((checkbox) => checkbox.dataset.module === moduleName)
			.forEach((checkbox) => {
				checkbox.checked = checked;
			});
	};

	const loadRolePermissions = async () => {
		const roleId = roleSelect.value;
		if (!roleId) {
			setAllChecked(false);
			return;
		}

		try {
			const response = await fetch(fetchTemplate.replace("__ROLE__", roleId), {
				headers: {
					Accept: "application/json",
				},
			});
			const data = await response.json();
			if (!response.ok || !data.success) {
				throw new Error(data.message || "Failed to load role permissions.");
			}

			const activeSlugs = new Set((data.permissions || []).map((slug) => String(slug)));
			checkboxes().forEach((checkbox) => {
				const slug = checkbox.dataset.slug || "";
				checkbox.checked = activeSlugs.has(slug);
			});
		} catch (error) {
			if (window.toast) {
				window.toast.error(error?.message || "Error loading role permissions.");
			}
		}
	};

	roleSelect.addEventListener("change", loadRolePermissions);

	checkAllButton.addEventListener("click", () => {
		setAllChecked(true);
	});

	uncheckAllButton.addEventListener("click", () => {
		setAllChecked(false);
	});

	moduleToggleButtons.forEach((button) => {
		button.addEventListener("click", () => {
			const moduleName = button.dataset.module;
			if (!moduleName) {
				return;
			}

			const moduleChecks = checkboxes().filter((checkbox) => checkbox.dataset.module === moduleName);
			if (moduleChecks.length === 0) {
				return;
			}

			const shouldEnable = moduleChecks.some((checkbox) => !checkbox.checked);
			setModuleChecked(moduleName, shouldEnable);
		});
	});
};

if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", initPermissionAssignment);
} else {
	initPermissionAssignment();
}
