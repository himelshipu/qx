import Sortable from "sortablejs";

export const SORTABLE_SHARED_CLASSES = {
    ghost: "dashboard-sortable-ghost",
    drag: "dashboard-sortable-drag",
};

/**
 * Create a standardized sortable instance for dashboard lists.
 */
export function createDashboardSortable(element, options = {}) {
    if (!element) {
        return null;
    }

    return new Sortable(element, {
        animation: 150,
        ghostClass: SORTABLE_SHARED_CLASSES.ghost,
        dragClass: SORTABLE_SHARED_CLASSES.drag,
        ...options,
    });
}
