function getCampaignShowRoot() {
    return document.querySelector('[data-campaign-show-root]');
}

function getCampaignShowConfig() {
    const root = getCampaignShowRoot();

    return {
        root,
        status: root?.dataset.campaignStatus || '',
        updateStatusUrl: root?.dataset.updateStatusUrl || '',
    };
}

window.campaignStatusForm = function campaignStatusForm() {
    const config = getCampaignShowConfig();

    return {
        selectedStatus: config.status,
        isLoading: false,
        showFeedback: false,
        feedbackText: '',
        feedbackClass: '',

        async updateStatus() {
            const currentStatus = config.status;
            const newStatus = this.selectedStatus;

            if (! config.updateStatusUrl || newStatus === currentStatus) {
                return;
            }

            this.isLoading = true;
            this.showFeedback = false;

            try {
                const response = await fetch(config.updateStatusUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status: newStatus }),
                });

                const data = await response.json();

                if (data.success) {
                    this.feedbackClass = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300';
                    this.feedbackText = data.message;
                    this.showFeedback = true;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    this.feedbackClass = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300';
                    this.feedbackText = data.message || 'Failed to update status';
                    this.showFeedback = true;
                    this.selectedStatus = currentStatus;
                }
            } catch (error) {
                console.error('Error updating campaign status:', error);
                this.feedbackClass = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300';
                this.feedbackText = 'An error occurred. Please try again.';
                this.showFeedback = true;
                this.selectedStatus = currentStatus;
            } finally {
                this.isLoading = false;
            }
        },
    };
};

function initApplicationsTable() {
    const table = document.getElementById('js-applications-table');
    if (! table) {
        return;
    }

    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody?.querySelectorAll('tr') || []);
    const selectAllCheckbox = document.getElementById('js-select-all');
    const rowCheckboxes = document.querySelectorAll('.js-row-checkbox');
    const batchActionsContainer = document.getElementById('js-batch-actions');
    const batchCountEl = document.getElementById('js-batch-count');
    const batchApproveBtn = document.getElementById('js-batch-approve');
    const batchRejectBtn = document.getElementById('js-batch-reject');
    const batchCancelBtn = document.getElementById('js-batch-cancel');
    const searchInput = document.getElementById('js-table-search');
    const statusFilter = document.getElementById('js-status-filter');
    const resetBtn = document.getElementById('js-filter-reset');

    const updateBatchUI = () => {
        const selectedCount = document.querySelectorAll('.js-row-checkbox:checked').length;

        if (batchCountEl) {
            batchCountEl.textContent = String(selectedCount);
        }

        if (batchActionsContainer) {
            batchActionsContainer.style.display = selectedCount > 0 ? 'flex' : 'none';
        }
    };

    resetBtn?.addEventListener('click', () => {
        if (searchInput) {
            searchInput.value = '';
        }
        if (statusFilter) {
            statusFilter.value = '';
        }

        rows.forEach((row) => {
            row.style.display = '';
        });
    });

    searchInput?.addEventListener('keyup', function () {
        const searchTerm = this.value.toLowerCase();

        rows.forEach((row) => {
            const searchData = row.dataset.search || '';
            row.style.display = searchData.includes(searchTerm) ? '' : 'none';
        });
    });

    statusFilter?.addEventListener('change', function () {
        const selectedStatus = this.value;

        rows.forEach((row) => {
            const rowStatus = row.dataset.status || '';
            row.style.display = ! selectedStatus || rowStatus === selectedStatus ? '' : 'none';
        });
    });

    selectAllCheckbox?.addEventListener('change', function () {
        rowCheckboxes.forEach((cb) => {
            const isVisible = cb.closest('tr')?.style.display !== 'none';
            if (isVisible) {
                cb.checked = this.checked;
            }
        });
        updateBatchUI();
    });

    rowCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', updateBatchUI);
    });

    batchApproveBtn?.addEventListener('click', () => {
        const checkedCheckboxes = Array.from(document.querySelectorAll('.js-row-checkbox:checked'));
        if (checkedCheckboxes.length === 0) return;

        let submitted = 0;
        checkedCheckboxes.forEach((checkbox) => {
            const row = checkbox.closest('tr');
            const approveForm = row?.querySelector('.js-approve-form');
            if (approveForm) {
                submitted += 1;
                approveForm.submit();
            }
        });

        if (submitted === 0) {
            window.toast?.warning('Declined influencers cannot be approved again.');
        }
    });

    batchRejectBtn?.addEventListener('click', () => {
        const checkedCheckboxes = Array.from(document.querySelectorAll('.js-row-checkbox:checked'));
        if (checkedCheckboxes.length === 0) return;

        let submitted = 0;
        checkedCheckboxes.forEach((checkbox) => {
            const row = checkbox.closest('tr');
            const rejectForm = row?.querySelector('.js-reject-form');
            if (rejectForm) {
                submitted += 1;
                rejectForm.submit();
            }
        });

        if (submitted === 0) {
            window.toast?.warning('Approved influencers cannot be declined.');
        }
    });

    batchCancelBtn?.addEventListener('click', () => {
        rowCheckboxes.forEach((cb) => {
            cb.checked = false;
        });

        if (selectAllCheckbox) {
            selectAllCheckbox.checked = false;
        }

        updateBatchUI();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initApplicationsTable();
});
