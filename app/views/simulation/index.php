<?php
$errorMsg = isset($_GET['error']) ? $_GET['error'] : null;
?>
<div class="page-header">
    <h1><i class="bi bi-calculator"></i> Simulation d'achats</h1>
    <a href="/achats" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Retour aux achats
    </a>
</div>

<?php if ($errorMsg): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= htmlspecialchars($errorMsg) ?>
    </div>
<?php endif; ?>

<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>
    <strong>Frais d'achat :</strong> <?= number_format($fraisPourcentage, 2) ?>%
</div>

<div class="row g-4">
    <!-- SÉLECTION -->
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <i class="bi bi-1-circle me-2"></i>Sélection du budget et de la ville
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Don en argent <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="selectDonArgent">
                        <option value="">-- Choisir un don --</option>
                        <?php foreach ($donsArgent as $d): ?>
                        <option value="<?= $d['id_don'] ?>" 
                                data-disponible="<?= $d['disponible'] ?>">
                            Don #<?= $d['id_don'] ?> — <?= htmlspecialchars($d['donateur_nom']) ?>
                            (<?= number_format($d['disponible'], 0, ',', ' ') ?> Ar)
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text fw-bold text-primary" id="budgetInfo"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Ville concernée <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="selectVille">
                        <option value="">-- Choisir une ville --</option>
                        <?php foreach ($villes as $v): ?>
                        <option value="<?= $v['id_ville'] ?>">
                            <?= htmlspecialchars($v['nom_ville']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="button" class="btn btn-info w-100" id="btnChargerBesoins">
                    <i class="bi bi-download me-1"></i>Charger les besoins de cette ville
                </button>
            </div>
        </div>
    </div>

    <!-- BESOINS -->
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <i class="bi bi-2-circle me-2"></i>Besoins restants
            </div>
            <div class="card-body">
                <div id="besoinsContainer" style="max-height: 400px; overflow-y: auto;">
                    <p class="text-muted text-center py-4">
                        Sélectionnez un don et une ville pour voir les besoins
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- RÉSULTAT SIMULATION -->
<div class="row mt-4" id="resultSection" style="display: none;">
    <div class="col-12">
        <div class="card shadow-sm border-primary">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-clipboard-check me-2"></i>Résultat de la simulation
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <small class="text-muted">Budget disponible</small>
                                <h4 class="mb-0" id="budgetDispo">0 Ar</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <small class="text-muted">Montant total</small>
                                <h4 class="mb-0 text-primary" id="montantTotal">0 Ar</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card" id="budgetRestantCard">
                            <div class="card-body text-center">
                                <small class="text-muted">Budget restant</small>
                                <h4 class="mb-0" id="budgetRestant">0 Ar</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Type de besoin</th>
                                <th class="text-end">Quantité</th>
                                <th class="text-end">Prix unitaire</th>
                                <th class="text-end">Base</th>
                                <th class="text-end">Frais</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody id="tableauAchats">
                        </tbody>
                    </table>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success flex-fill" id="btnValider">
                        <i class="bi bi-check-circle me-1"></i>Valider et enregistrer les achats
                    </button>
                    <button type="button" class="btn btn-secondary" id="btnRefaire">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refaire
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const fraisPourcentage = <?= $fraisPourcentage ?>;
let besoinsData = [];
let simulationId = null;

const selectDon = document.getElementById('selectDonArgent');
const selectVille = document.getElementById('selectVille');
const budgetInfo = document.getElementById('budgetInfo');
const btnCharger = document.getElementById('btnChargerBesoins');
const besoinsContainer = document.getElementById('besoinsContainer');
const resultSection = document.getElementById('resultSection');
const btnValider = document.getElementById('btnValider');
const btnRefaire = document.getElementById('btnRefaire');

selectDon.addEventListener('change', () => {
    const opt = selectDon.options[selectDon.selectedIndex];
    const dispo = opt?.dataset?.disponible;
    if (dispo) {
        budgetInfo.textContent = `Budget : ${parseFloat(dispo).toLocaleString('fr-FR')} Ar`;
    } else {
        budgetInfo.textContent = '';
    }
});

btnCharger.addEventListener('click', async () => {
    const idVille = selectVille.value;
    if (!idVille) {
        alert('Veuillez sélectionner une ville');
        return;
    }

    btnCharger.disabled = true;
    besoinsContainer.innerHTML = '<p class="text-center"><i class="spinner-border spinner-border-sm me-2"></i>Chargement...</p>';

    try {
        const resp = await fetch(`/simulation/besoins?id_ville=${idVille}`);
        const data = await resp.json();
        besoinsData = data;

        if (!data.length) {
            besoinsContainer.innerHTML = '<p class="text-warning text-center py-4">Aucun besoin restant pour cette ville</p>';
            return;
        }

        let html = '<div class="list-group">';
        data.forEach(b => {
            html += `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>${b.type_nom}</strong>
                        <small class="text-muted">${parseFloat(b.prix_unitaire).toLocaleString('fr-FR')} Ar/${b.unite}</small>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <small class="text-muted">Manquant : ${parseFloat(b.quantite_manquante).toLocaleString('fr-FR')} ${b.unite}</small>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control qte-input" 
                                       data-id="${b.id_type_besoin}"
                                       data-prix="${b.prix_unitaire}"
                                       data-unite="${b.unite}"
                                       data-nom="${b.type_nom}"
                                       max="${b.quantite_manquante}"
                                       step="0.01" min="0" placeholder="Quantité">
                                <span class="input-group-text">${b.unite}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += `</div>
                 <button type="button" class="btn btn-primary w-100 mt-3" id="btnSimuler">
                     <i class="bi bi-calculator me-1"></i>Simuler ces achats
                 </button>`;
        
        besoinsContainer.innerHTML = html;

        document.getElementById('btnSimuler').addEventListener('click', simuler);
    } catch (error) {
        besoinsContainer.innerHTML = '<p class="text-danger text-center">Erreur de chargement</p>';
    } finally {
        btnCharger.disabled = false;
    }
});

async function simuler() {
    const idDon = selectDon.value;
    if (!idDon) {
        alert('Veuillez sélectionner un don en argent');
        return;
    }

    const inputs = document.querySelectorAll('.qte-input');
    const achats = [];

    inputs.forEach(input => {
        const qte = parseFloat(input.value || 0);
        if (qte > 0) {
            achats.push({
                id_type_besoin: input.dataset.id,
                quantite: qte,
            });
        }
    });

    if (!achats.length) {
        alert('Veuillez saisir au moins une quantité');
        return;
    }

    try {
        const resp = await fetch('/simulation/simuler', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_don_argent: idDon, achats }),
        });

        const data = await resp.json();

        if (data.error) {
            alert('Erreur : ' + data.error);
            return;
        }

        simulationId = data.id_simulation;
        afficherResultat(data);
    } catch (error) {
        alert('Erreur lors de la simulation');
    }
}

function afficherResultat(data) {
    document.getElementById('budgetDispo').textContent = data.budget_disponible.toLocaleString('fr-FR') + ' Ar';
    document.getElementById('montantTotal').textContent = data.montant_total.toLocaleString('fr-FR') + ' Ar';
    document.getElementById('budgetRestant').textContent = data.budget_restant.toLocaleString('fr-FR') + ' Ar';

    const card = document.getElementById('budgetRestantCard');
    if (data.budget_suffisant) {
        card.classList.remove('bg-danger', 'text-white');
        card.classList.add('bg-success', 'text-white');
    } else {
        card.classList.remove('bg-success');
        card.classList.add('bg-danger', 'text-white');
    }

    let tbody = '';
    data.achats.forEach(a => {
        tbody += `
            <tr>
                <td>${a.nom}</td>
                <td class="text-end">${a.quantite.toLocaleString('fr-FR')} ${a.unite}</td>
                <td class="text-end">${a.prix_unitaire.toLocaleString('fr-FR')} Ar</td>
                <td class="text-end">${a.montant_base.toLocaleString('fr-FR')} Ar</td>
                <td class="text-end text-muted">${a.montant_frais.toLocaleString('fr-FR')} Ar</td>
                <td class="text-end fw-bold">${a.montant_total.toLocaleString('fr-FR')} Ar</td>
            </tr>
        `;
    });
    document.getElementById('tableauAchats').innerHTML = tbody;

    btnValider.disabled = !data.budget_suffisant;
    resultSection.style.display = 'block';
    resultSection.scrollIntoView({ behavior: 'smooth' });
}

btnValider.addEventListener('click', async () => {
    if (!simulationId) return;

    if (!confirm('Confirmer la validation de ces achats ?')) return;

    btnValider.disabled = true;

    try {
        const resp = await fetch('/simulation/valider', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_simulation: simulationId }),
        });

        window.location.href = '/achats?success=simulation_validee';
    } catch (error) {
        alert('Erreur lors de la validation');
        btnValider.disabled = false;
    }
});

btnRefaire.addEventListener('click', () => {
    resultSection.style.display = 'none';
    simulationId = null;
    window.scrollTo({ top: 0, behavior: 'smooth' });
});
</script>