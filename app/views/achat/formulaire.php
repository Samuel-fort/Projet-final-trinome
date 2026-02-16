<?php
$errorMsg = isset($_GET['error']) ? $_GET['error'] : null;

$messages = [
    'champs' => 'Tous les champs sont requis.',
    'type_introuvable' => 'Type de besoin introuvable.',
    'budget_insuffisant' => 'Budget insuffisant pour cet achat.',
];
?>
<div class="page-header">
    <h1><i class="bi bi-plus-circle"></i>Nouvel achat</h1>
    <a href="/achats" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

<?php if ($errorMsg): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= $messages[$errorMsg] ?? htmlspecialchars($errorMsg) ?>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <i class="bi bi-credit-card me-2"></i>Ajouter un achat
            </div>
            <div class="card-body">
                <form method="POST" action="/achats/store" id="formAchat">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Don en argent <span class="text-danger">*</span>
                        </label>
                        <select name="id_don_argent" class="form-select" required id="selectDon">
                            <option value="">-- Choisir un don --</option>
                            <?php foreach ($donsArgent as $d): ?>
                            <option value="<?= $d['id_don'] ?>" data-disponible="<?= $d['disponible'] ?>">
                                Don #<?= $d['id_don'] ?> — <?= htmlspecialchars($d['donateur_nom']) ?>
                                (<?= number_format($d['disponible'], 0, ',', ' ') ?> Ar disponible)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text fw-bold text-primary" id="donInfo"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Type de besoin à acheter <span class="text-danger">*</span>
                        </label>
                        <select name="id_type_besoin" class="form-select" required id="selectType">
                            <option value="">-- Choisir un type --</option>
                            <?php foreach ($typesBesoins as $t): ?>
                            <option value="<?= $t['id_type_besoin'] ?>" 
                                    data-prix="<?= $t['prix_unitaire'] ?>"
                                    data-unite="<?= htmlspecialchars($t['unite']) ?>">
                                <?= htmlspecialchars($t['nom']) ?> (<?= $t['unite'] ?> — <?= number_format($t['prix_unitaire'], 0, ',', ' ') ?> Ar)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text" id="typeInfo"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Quantité à acheter <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" name="quantite" class="form-control" step="0.01" min="0.01" 
                                   required id="inputQte" placeholder="0">
                            <span class="input-group-text" id="uniteLabel">unité</span>
                        </div>
                    </div>

                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label small text-muted">Montant base (HT)</label>
                                <div class="h5" id="montantBase">0 Ar</div>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted">Frais (<?= number_format($fraisPourcentage, 2) ?>%)</label>
                                <div class="h5 text-warning" id="montantFrais">0 Ar</div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label small text-muted">Montant total (TTC)</label>
                                <div class="h4 text-primary fw-bold" id="montantTotal">0 Ar</div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="btnSubmit" disabled>
                        <i class="bi bi-check-circle me-1"></i>Valider l'achat
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Informations
            </div>
            <div class="card-body small">
                <p class="text-muted mb-2">
                    <strong>Frais d'achat :</strong> Les achats incluent des frais de <?= number_format($fraisPourcentage, 2) ?>%.
                </p>
                <p class="text-muted mb-2">
                    <strong>Budget :</strong> Choisissez d'abord un don en argent pour voir le budget disponible.
                </p>
                <p class="text-muted mb-0">
                    <strong>Types :</strong> Sélectionnez le type de besoin à acheter et la quantité désirée.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
const selectDon = document.getElementById('selectDon');
const selectType = document.getElementById('selectType');
const inputQte = document.getElementById('inputQte');
const donInfo = document.getElementById('donInfo');
const typeInfo = document.getElementById('typeInfo');
const uniteLabel = document.getElementById('uniteLabel');
const montantBase = document.getElementById('montantBase');
const montantFrais = document.getElementById('montantFrais');
const montantTotal = document.getElementById('montantTotal');
const btnSubmit = document.getElementById('btnSubmit');

const fraisPourcentage = <?= $fraisPourcentage ?>;

function calculer() {
    const optType = selectType.options[selectType.selectedIndex];
    const optDon = selectDon.options[selectDon.selectedIndex];
    
    const qte = parseFloat(inputQte.value) || 0;
    const prixUnitaire = parseFloat(optType?.dataset?.prix) || 0;
    const unite = optType?.dataset?.unite || 'unité';
    const disponible = parseFloat(optDon?.dataset?.disponible) || 0;

    uniteLabel.textContent = unite;

    if (qte <= 0 || prixUnitaire <= 0) {
        montantBase.textContent = '0 Ar';
        montantFrais.textContent = '0 Ar';
        montantTotal.textContent = '0 Ar';
        btnSubmit.disabled = true;
        return;
    }

    const mBase = qte * prixUnitaire;
    const mFrais = mBase * (fraisPourcentage / 100);
    const mTotal = mBase + mFrais;

    montantBase.textContent = mBase.toLocaleString('fr-FR') + ' Ar';
    montantFrais.textContent = mFrais.toLocaleString('fr-FR') + ' Ar';
    montantTotal.textContent = mTotal.toLocaleString('fr-FR') + ' Ar';

    // Vérifier le budget disponible
    if (selectDon.value && mTotal > disponible) {
        montantTotal.classList.add('text-danger');
        btnSubmit.disabled = true;
        typeInfo.textContent = '⚠️ Budget insuffisant !';
    } else {
        montantTotal.classList.remove('text-danger');
        btnSubmit.disabled = !selectDon.value || !selectType.value || qte <= 0;
        typeInfo.textContent = '';
    }
}

selectDon.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const disponible = parseFloat(opt?.dataset?.disponible) || 0;
    if (this.value) {
        donInfo.textContent = `Disponible : ${disponible.toLocaleString('fr-FR')} Ar`;
    } else {
        donInfo.textContent = '';
    }
    calculer();
});

selectType.addEventListener('change', calculer);
inputQte.addEventListener('input', calculer);
</script>
