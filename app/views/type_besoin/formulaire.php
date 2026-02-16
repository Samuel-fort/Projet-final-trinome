<?php $isEdit = $mode === 'edit'; ?>
<div class="page-header">
    <h1><i class="bi bi-tag"></i><?= $isEdit ? 'Modifier le type' : 'Nouveau type de besoin' ?></h1>
    <a href="/types-besoins" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour</a>
</div>

<?php if (!empty($flash_error)): ?>
<div class="alert alert-danger"><?= htmlspecialchars($flash_error) ?></div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header"><i class="bi bi-pencil-square me-2"></i><?= $isEdit ? 'Modifier' : 'Ajouter' ?> un type de besoin</div>
            <div class="card-body p-4">
                <form method="POST" action="<?= $isEdit ? '/types-besoins/'.$type['id_type_besoin'].'/update' : '/types-besoins/store' ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catégorie <span class="text-danger">*</span></label>
                        <select name="id_categorie" class="form-select" required>
                            <option value="">-- Choisir une catégorie --</option>
                            <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id_categorie'] ?>"
                                <?= ($type['id_categorie'] ?? 0) == $c['id_categorie'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst($c['nom_categorie'])) ?>
                                <?php if ($c['description']): ?> — <?= htmlspecialchars($c['description']) ?><?php endif; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control" required
                               value="<?= htmlspecialchars($type['nom'] ?? '') ?>" placeholder="Ex: Riz, Tôle, Aide financière">
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label fw-semibold">Unité <span class="text-danger">*</span></label>
                            <input type="text" name="unite" class="form-control" required
                                   value="<?= htmlspecialchars($type['unite'] ?? '') ?>" placeholder="kg, litre, pièce, Ar...">
                        </div>
                        <div class="col">
                            <label class="form-label fw-semibold">Prix unitaire (Ar) <span class="text-danger">*</span></label>
                            <input type="number" name="prix_unitaire" class="form-control" step="0.01" min="0.01" required
                                   value="<?= $type['prix_unitaire'] ?? '' ?>" placeholder="0.00">
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-check-circle me-1"></i><?= $isEdit ? 'Mettre à jour' : 'Enregistrer' ?>
                        </button>
                        <a href="/types-besoins" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>