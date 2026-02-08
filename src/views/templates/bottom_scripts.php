<!-- third-party scripts -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/plugin/customParseFormat.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr" defer></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/vi.js" defer></script>

<?php require_once __DIR__ . '/../../utils/Asset.php'; ?>

<!-- custom scripts -->
<script src="<?= queryAssetWithVersion('/utils/scripts/init-utils.js') ?>" type="module" defer></script>
<script src="<?= queryAssetWithVersion('/utils/scripts/helpers.js') ?>" type="module" defer></script>
<script src="<?= queryAssetWithVersion('/utils/scripts/api-client.js') ?>" type="module" defer></script>
<script src="<?= queryAssetWithVersion('/utils/scripts/components.js') ?>" type="module" defer></script>