<?php
$success = isset($_GET['success']);
$deleted = isset($_GET['deleted']);
$errorMsg = isset($_GET['error']) ? $_GET['error'] : null;

$messages = [
    'champs' => 'Tous les champs sont requis.',
    'type_introuvable' => 'Type de besoin introuvable.',
    'budget_insuffisant' => 'Budget insuffisant pour cet achat.',
    'simulation_validee' => 'Simulation validée avec succès ! Les achats ont été enregistrés.',
];
?>
<div class="page-header">
    <h1><i class="bi bi-cart-fill"></i> Achats de besoins</h1>
    <div>
        <a href="/simulation" class="btn btn-info btn-sm me-2">
            <i class="bi bi-calculator me-1"></i>Simuler des achats
        </a>
        <a href="/achats/create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Nouvel achat
        </a>
    </div>
</div>

<?php if ($success): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?= $errorMsg === 'simulation_validee' ? $messages[$errorMsg] : 'Achat enregistré avec succès.' ?>
    </div>
<?php endif; ?>

<?php if ($deleted): ?>
    <div class="alert alert-info">
        <i class="bi bi-trash-fill me-2"></i>Achat annulé.
    </div>
<?php endif; ?>

<?php if ($errorMsg && !$success): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= $messages[$errorMsg] ?? htmlspecialchars($errorMsg) ?>
    </div>
<?php endif; ?>

<!-- Info frais -->
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>
    <strong>Frais d'achat appliqués :</strong> <?= number_format($fraisPourcentage, 2) ?>%
    <span class="text-muted">(configurable dans la base de données)</span>
</div>

<div class="row g-4">
    <!-- FORMULAIRE ACHAT -->
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header">
                <i class="bi bi-plus-circle me-2"></i>Nouvel achat avec l'argent
            </div>
            <div class="card-body p-4">
                <form method="POST" action="/achats/store" id="formAchat">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Don en argent <span class="text-danger">*</span>
                        </label>
                        <select name="id_don_argent" class="form-select" required id="selectDonArgent">
                            <option value="">-- Choisir un don --</option>
                            <?php foreach ($donsArgent as $d): ?>
                            <option value="<?= $d['id_don'] ?>" 
                                    data-disponible="<?= $d['disponible'] ?>">
                                Don #<?= $d['id_don'] ?> — <?= htmlspecialchars($d['donateur_nom']) ?>
                                (<?= number_format($d['disponible'], 0, ',', ' ') ?> Ar dispo)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text" id="budgetInfo"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Type de besoin à acheter <span class="text-danger">*</span>
                        </label>
                        <select name="id_type_besoin" class="form-select" required id="selectType">
                            <option value="">-- Choisir un type --</option>
                            <?php foreach ($typesBesoins as $t): ?>
                                <?php if ($t['id_categorie'] != 3): // Pas la catégorie "argent" ?>
                                <option value="<?= $t['id_type_besoin'] ?>"
                                        data-prix="<?= $t['prix_unitaire'] ?>"
                                        data-unite="<?= htmlspecialchars($t['unite']) ?>">
                                    <?= htmlspecialchars($t['nom']) ?> 
                                    (<?= number_format($t['prix_unitaire'], 0, ',', ' ') ?> Ar/<?= $t['unite'] ?>)
                                </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Quantité <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" name="quantite" class="form-control" 
                                   step="0.01" min="0.01" required id="inputQuantite" placeholder="0">
                            <span class="input-group-text" id="uniteAchat">unité</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row g-2 small">
                                    <div class="col-6">
                                        <strong>Prix unitaire :</strong>
                                        <div id="prixUnitaire">-</div>
                                    </div>
                                    <div class="col-6">
                                        <strong>Montant base :</strong>
                                        <div id="montantBase">-</div>
                                    </div>
                                    <div class="col-6">
                                        <strong>Frais (<?= $fraisPourcentage ?>%) :</strong>
                                        <div id="montantFrais">-</div>
                                    </div>
                                    <div class="col-6">
                                        <strong class="text-primary">Total à payer :</strong>
                                        <div class="text-primary fw-bold" id="montantTotal">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-cart-check me-1"></i>Valider l'achat
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- LISTE DES ACHATS -->
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header">
                <i class="bi bi-list-ul me-2"></i>Historique des achats (<?= count($achats) ?>)
            </div>
            <div class="table-responsive" style="max-height:520px;overflow-y:auto">
                <table class="table table-hover mb-0">
                    <thead class="sticky-top">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th class="text-end">Qté</th>
                            <th class="text-end">Prix U.</th>
                            <th class="text-end">Frais</th>
                            <th class="text-end">Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($achats)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Aucun achat enregistré
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($achats as $a): ?>
                        <tr>
                            <td class="small"><?= date('d/m/Y', strtotime($a['date_achat'])) ?></td>
                            <td><?= htmlspecialchars($a['type_nom']) ?></td>
                            <td class="text-end">
                                <?= number_format($a['quantite_achetee'], 2, ',', ' ') ?> <?= $a['unite'] ?>
                            </td>
                            <td class="text-end small">
                                <?= number_format($a['prix_unitaire'], 0, ',', ' ') ?> Ar
                            </td>
                            <td class="text-end small text-muted">
                                <?= number_format($a['frais_pourcentage'], 1) ?>%
                            </td>
                            <td class="text-end fw-bold">
                                <?= number_format($a['montant_total'], 0, ',', ' ') ?> Ar
                            </td>
                            <td>
                                <form method="POST" action="/achats/<?= $a['id_achat'] ?>/delete" 
                                      class="d-inline" 
                                      onsubmit="return confirm('Êtes-vous sûr ?');">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const fraisPourcentage = <?= $fraisPourcentage ?>;
const selectDon = document.getElementById('selectDonArgent');
const selectType = document.getElementById('selectType');
const inputQte = document.getElementById('inputQuantite');
const budgetInfo = document.getElementById('budgetInfo');
const uniteAchat = document.getElementById('uniteAchat');
const prixUnitaireDiv = document.getElementById('prixUnitaire');
const montantBaseDiv = document.getElementById('montantBase');
const montantFraisDiv = document.getElementById('montantFrais');
const montantTotalDiv = document.getElementById('montantTotal');

function updateBudgetInfo() {
    const opt = selectDon.options[selectDon.selectedIndex];
    const dispo = opt?.dataset?.disponible;
    if (dispo) {
        budgetInfo.textContent = `Budget disponible : ${parseFloat(dispo).toLocaleString('fr-FR')} Ar`;
    } else {
        budgetInfo.textContent = '';
    }
    calculer();
}

function updateTypeInfo() {
    const opt = selectType.options[selectType.selectedIndex];
    const unite = opt?.dataset?.unite || 'unité';
    uniteAchat.textContent = unite;
    calculer();
}

function calculer() {
    const optType = selectType.options[selectType.selectedIndex];
    const prix = parseFloat(optType?.dataset?.prix || 0);
    const unite = optType?.dataset?.unite || 'unité';
    const qte = parseFloat(inputQte.value || 0);

    if (!prix || !qte) {
        prixUnitaireDiv.textContent = '-';
        montantBaseDiv.textContent = '-';
        montantFraisDiv.textContent = '-';
        montantTotalDiv.textContent = '-';
        return;
    }

    const base = qte * prix;
    const frais = base * (fraisPourcentage / 100);
    const total = base + frais;

    prixUnitaireDiv.textContent = prix.toLocaleString('fr-FR') + ' Ar/' + unite;
    montantBaseDiv.textContent = base.toLocaleString('fr-FR') + ' Ar';
    montantFraisDiv.textContent = frais.toLocaleString('fr-FR') + ' Ar';
    montantTotalDiv.textContent = total.toLocaleString('fr-FR') + ' Ar';
}

selectDon.addEventListener('change', updateBudgetInfo);
selectType.addEventListener('change', updateTypeInfo);
inputQte.addEventListener('input', calculer);
</script>