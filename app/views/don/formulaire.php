<div class="page-header">
    <h1><i class="bi bi-gift"></i>Enregistrer un don</h1>
    <a href="/dons" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour</a>
</div>

<?php if (!empty($flash_error)): ?>
<div class="alert alert-danger"><?= htmlspecialchars($flash_error) ?></div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header"><i class="bi bi-plus-circle me-2"></i>Nouveau don</div>
            <div class="card-body p-4">
                <form method="POST" action="/dons/store">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Donateur <small class="text-muted">(optionnel)</small></label>
                        <select name="id_donateur" class="form-select">
                            <option value="">-- Don anonyme --</option>
                            <?php foreach ($donateurs as $d):
                                $label = trim(($d['prenom'] ?? '') . ' ' . ($d['nom'] ?? ''));
                                if ($d['organisation']) $label .= ($label ? ' — ' : '') . $d['organisation'];
                                if (!$label) $label = 'Anonyme #' . $d['id_donateur'];
                            ?>
                            <option value="<?= $d['id_donateur'] ?>"><?= htmlspecialchars($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Ou <a href="/donateurs/create" target="_blank">créer un nouveau donateur</a></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Type de don <span class="text-danger">*</span></label>
                        <select name="id_type_besoin" class="form-select" required id="selectType">
                            <option value="">-- Choisir --</option>
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
                                    data-prix="<?= $t['prix_unitaire'] ?>">
                                <?= htmlspecialchars($t['nom']) ?> (<?= $t['prix_unitaire'] ?> Ar/<?= $t['unite'] ?>)
                            </option>
                            <?php endforeach; if ($lastCat !== '') echo '</optgroup>'; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Quantité <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="quantite" class="form-control" step="0.01" min="0.01" required
                                   id="qteInput" placeholder="0">
                            <span class="input-group-text" id="uniteLabel">unité</span>
                        </div>
                    </div>

                    <div class="alert alert-light border" id="apercuValeur" style="display:none">
                        <small class="text-muted">Valeur estimée : <strong id="valeurEstimee">0 Ar</strong></small>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-check-circle me-1"></i>Enregistrer le don
                        </button>
                        <a href="/dons" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const sel = document.getElementById('selectType');
const qte = document.getElementById('qteInput');
const lbl = document.getElementById('uniteLabel');
const apercu = document.getElementById('apercuValeur');
const val    = document.getElementById('valeurEstimee');
function update() {
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) { apercu.style.display='none'; return; }
    lbl.textContent = opt.dataset.unite || 'unité';
    apercu.style.display = 'block';
    val.textContent = new Intl.NumberFormat('fr-FR').format(Math.round((parseFloat(qte.value)||0) * (parseFloat(opt.dataset.prix)||0))) + ' Ar';
}
sel.addEventListener('change', update);
qte.addEventListener('input', update);
</script>