<?php $isEdit = $mode === 'edit'; ?>
<div class="page-header">
    <h1><i class="bi bi-geo-alt<?= $isEdit ? '-fill' : '' ?>"></i><?= $isEdit ? 'Modifier la ville' : 'Nouvelle ville' ?></h1>
    <a href="/villes" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour</a>
</div>

<?php if (!empty($flash_error)): ?>
<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($flash_error) ?></div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header"><i class="bi bi-pencil-square me-2"></i><?= $isEdit ? 'Modifier' : 'Ajouter' ?> une ville</div>
            <div class="card-body p-4">
                <form method="POST" action="<?= $isEdit ? '/villes/'.$ville['id_ville'].'/update' : '/villes/store' ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom de la ville <span class="text-danger">*</span></label>
                        <input type="text" name="nom_ville" class="form-control"
                               value="<?= htmlspecialchars($ville['nom_ville'] ?? '') ?>" required
                               placeholder="Ex: Antananarivo">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Région</label>
                        <input type="text" name="region" class="form-control"
                               value="<?= htmlspecialchars($ville['region'] ?? '') ?>"
                               placeholder="Ex: Analamanga">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-check-circle me-1"></i><?= $isEdit ? 'Mettre à jour' : 'Enregistrer' ?>
                        </button>
                        <a href="/villes" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>