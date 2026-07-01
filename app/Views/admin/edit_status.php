<!-- Partial view: edit_status.php -->
<div class="card p-3 mb-3">
    <form action="/admin/update-status/<?= $item['id'] ?>" method="post">
        <?= csrf_field() ?>
        <div class="row g-2">
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="Menunggu" <?= $item['status'] === 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                    <option value="Diterima" <?= $item['status'] === 'Diterima' ? 'selected' : '' ?>>Diterima</option>
                    <option value="Ditolak" <?= $item['status'] === 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                </select>
            </div>
            <div class="col-md-6">
                <input type="text" name="catatan" class="form-control" placeholder="Catatan (opsional)"
                    value="<?= esc($item['catatan']) ?>">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Simpan</button>
            </div>
        </div>
    </form>
</div>