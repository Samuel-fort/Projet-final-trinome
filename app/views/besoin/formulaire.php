<?php $isEdit = $mode === 'edit'; ?>
<div class="page-header">
    <h1><i class="bi bi-clipboard-plus"></i><?= $isEdit ? 'Modifier le besoin' : 'Saisir un besoin' ?></h1>
    <a href="/besoins" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour</a>
</div>

<?php if (!empty($flash_error)): ?>
<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($flash_error) ?></div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header"><i class="bi bi-pencil-square me-2"></i><?= $isEdit ? 'Modifier' : 'Nouveau' ?> besoin</div>
            <div class="card-body p-4">
                <form method="POST" action="<?= $isEdit ? '/besoins/'.$besoin['id_besoin'].'/update' : '/besoins/store' ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ville sinistrée <span class="text-danger">*</span></label>
                        <select name="id_ville" class="form-select" required>
                            <option value="">-- Choisir une ville --</option>
                            <?php foreach ($villes as $v): ?>
                            <option value="<?= $v['id_ville'] ?>"
                                <?= ($besoin['id_ville'] ?? 0) == $v['id_ville'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($v['nom_ville']) ?> (<?= htmlspecialchars($v['region'] ?? '') ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Type de besoin <span class="text-danger">*</span></label>
                        <select name="id_type_besoin" class="form-select" required id="selectType">
                            <option value="">-- Choisir un type --</option>
                            <?php
                            $lastCat = '';
                            foreach ($types as $t):
                                if ($t['nom_categorie'] !== $lastCat):
                                    if ($lastCat !== '') echo '</optgroup>';
                                    echo '<optgroup label="'.htmlspecialchars(ucfirst($t['nom_categorie'])).'">';
                                    $lastCat = $t['nom_categorie'];
                                endif;
                            ?>
                            <option value="<?= $t['id_type_besoin'] ?>"
                                data-unite="<?= htmlspecialchars($t['unite']) ?>"
                                data-prix="<?= $t['prix_unitaire'] ?>"
                                <?= ($besoin['id_type_besoin'] ?? 0) == $t['id_type_besoin'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['nom']) ?> (<?= $t['prix_unitaire'] ?> Ar/<?= $t['unite'] ?>)
                            </option>
                            <?php endforeach; if ($lastCat !== '') echo '</optgroup>'; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Quantité demandée <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="quantite_demandee" class="form-control" step="0.01" min="0.01"
                                   value="<?= $besoin['quantite_demandee'] ?? '' ?>" required placeholder="0">
                            <span class="input-group-text" id="uniteLabel">unité</span>
                        </div>
                    </div>

                    <!-- Aperçu valeur -->
                    <div class="alert alert-light border" id="apercuValeur" style="display:none">
                        <small class="text-muted">Valeur estimée : <strong id="valeurEstimee">0 Ar</strong></small>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-check-circle me-1"></i><?= $isEdit ? 'Mettre à jour' : 'Enregistrer' ?>
                        </button>
                        <a href="/besoins" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const selectType = document.getElementById('selectType');
const uniteLabel = document.getElementById('uniteLabel');
const qteInput   = document.querySelector('input[name="quantite_demandee"]');
const apercu     = document.getElementById('apercuValeur');
const valeurEl   = document.getElementById('valeurEstimee');

function updateApercu() {
    const opt = selectType.options[selectType.selectedIndex];
    if (!opt || !opt.value) { apercu.style.display='none'; return; }
    const unite = opt.dataset.unite || 'unité';
    const prix  = parseFloat(opt.dataset.prix) || 0;
    const qte   = parseFloat(qteInput.value) || 0;
    uniteLabel.textContent = unite;
    apercu.style.display = 'block';
    valeurEl.textContent  = new Intl.NumberFormat('fr-FR').format(Math.round(qte * prix)) + ' Ar';
}

selectType.addEventListener('change', updateApercu);
qteInput.addEventListener('input', updateApercu);
updateApercu();
</script>