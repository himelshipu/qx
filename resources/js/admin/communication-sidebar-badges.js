const ROOT_SELECTOR = "#sidebar";

const refreshBadges = async () => {
    const root = document.querySelector(ROOT_SELECTOR);
    if (!root) {
        return;
    }

    const badgesRoute = root.dataset.badgesRoute || "";
    if (!badgesRoute) {
        return;
    }

    try {
        const response = await fetch(badgesRoute, {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            return;
        }

        const counts = await response.json();
        document.querySelectorAll("[data-sidebar-badge-key]").forEach((badge) => {
            const key = badge.dataset.sidebarBadgeKey || "";
            const value = Number.parseInt(counts[key] ?? 0, 10);

            if (Number.isInteger(value) && value > 0) {
                badge.textContent = value > 99 ? "99+" : String(value);
                badge.classList.remove("hidden");
                badge.setAttribute("aria-hidden", "false");
                return;
            }

            badge.textContent = "";
            badge.classList.add("hidden");
            badge.setAttribute("aria-hidden", "true");
        });
    } catch (error) {
        console.error("Failed to refresh sidebar badges:", error);
    }
};

const initCommunicationSidebarBadges = () => {
    const root = document.querySelector(ROOT_SELECTOR);
    if (!root) {
        return;
    }

    const intervalSeconds = Number.parseInt(root.dataset.badgesRefreshSeconds || "60", 10);

    refreshBadges();
    window.setInterval(refreshBadges, Math.max(intervalSeconds, 15) * 1000);

    document.addEventListener("visibilitychange", () => {
        if (!document.hidden) {
            refreshBadges();
        }
    });
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initCommunicationSidebarBadges);
} else {
    initCommunicationSidebarBadges();
}
