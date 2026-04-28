@push('scripts')
	@if ($isBrand && $isParentOrder)
		<script>
			(function() {
				const detailsNodes = Array.from(document.querySelectorAll('details[data-child-order-details]'));
				if (detailsNodes.length === 0) {
					return;
				}

				const storageKey = 'order:' + @json((string) $order->id) + ':open-child-cards';

				const readOpenIds = () => {
					try {
						const parsed = JSON.parse(window.localStorage.getItem(storageKey) || '[]');
						return Array.isArray(parsed) ? new Set(parsed.map(String)) : new Set();
					} catch (error) {
						return new Set();
					}
				};

				const writeOpenIds = (openIds) => {
					window.localStorage.setItem(storageKey, JSON.stringify(Array.from(openIds)));
				};

				const openIds = readOpenIds();

				detailsNodes.forEach((detailsEl) => {
					const childId = String(detailsEl.dataset.childOrderDetails || '');
					if (!childId) {
						return;
					}

					if (openIds.has(childId)) {
						detailsEl.open = true;
					}

					detailsEl.addEventListener('toggle', () => {
						if (detailsEl.open) {
							openIds.add(childId);
						} else {
							openIds.delete(childId);
						}

						writeOpenIds(openIds);
					});
				});
			})();
		</script>
	@endif
@endpush
