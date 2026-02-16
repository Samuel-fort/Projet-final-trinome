<?php $isEdit = $mode === 'edit'; ?>
<div class="page-header">
    <h1><i class="bi bi-person-plus-fill"></i><?= $isEdit ? 'Modifier le donateur' : 'Nouveau donateur' ?></h1>
    <a href="/donateurs" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour</a>
</div>

<?php if (!empty($flash_error)): ?>
<div class="alert alert-danger"><?= htmlspecialchars($flash_error) ?></div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header"><i class="bi bi-pencil-square me-2"></i><?= $isEdit ? 'Modifier' : 'Ajouter' ?> un donateur</div>
            <div class="card-body p-4">
                <form method="POST" action="<?= $isEdit ? '/donateurs/'.$donateur['id_donateur'].'/update' : '/donateurs/store' ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Type de donateur</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type_donateur" id="typeParticulier"
                                       value="particulier" <?= ($donateur['type_donateur'] ?? 'particulier') === 'particulier' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="typeParticulier">Particulier</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type_donateur" id="typeEntreprise"
                                       value="entreprise" <?= ($donateur['type_donateur'] ?? '') === 'entreprise' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="typeEntreprise">Entreprise / ONG</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label fw-semibold">Prénom</label>
                            <input type="text" name="prenom" class="form-control"
                                   value="<?= htmlspecialchars($donateur['prenom'] ?? '') ?>" placeholder="Prénom">
                        </div>
                        <div class="col">
                            <label class="form-label fw-semibold">Nom</label>
                            <input type="text" name="nom" class="form-control"
                                   value="<?= htmlspecialchars($donateur['nom'] ?? '') ?>" placeholder="Nom">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Organisation</label>
                        <input type="text" name="organisation" class="form-control"
                               value="<?= htmlspecialchars($donateur['organisation'] ?? '') ?>"
                               placeholder="Nom de l'entreprise ou ONG">
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input type="tel" name="telephone" class="form-control"
                                   value="<?= htmlspecialchars($donateur['telephone'] ?? '') ?>" placeholder="034...">
                        </div>
                        <div class="col">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= htmlspecialchars($donateur['email'] ?? '') ?>" placeholder="email@example.com">
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-check-circle me-1"></i><?= $isEdit ? 'Mettre à jour' : 'Enregistrer' ?>
                        </button>
                        <a href="/donateurs" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>