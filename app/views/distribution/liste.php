<?php
$success = isset($_GET['success']);
$deleted = isset($_GET['deleted']);
$errorMsg = isset($_GET['error']) ? urldecode($_GET['error']) : null;
$prefillDon = isset($_GET['don']) ? (int)$_GET['don'] : 0;

$messages = [
    'simulation_validee' => 'Simulation validée ! Les distributions ont été enregistrées.',
    'simulation_invalide' => 'Données de simulation invalides.',
];
?>

<div class="page-header">
    <h1><i class="bi bi-send-fill"></i> Distributions</h1>
    <a href="/besoins/create" class="btn btn-warning btn-sm" title="Créer rapidement un besoin">
        <i class="bi bi-plus-circle me-1"></i>Créer un besoin
    </a>
</div>

<?php if ($success): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?= $errorMsg === 'simulation_validee' ? $messages[$errorMsg] : 'Distribution enregistrée avec succès.' ?>
    </div>
<?php endif; ?>

<?php if ($deleted): ?>
    <div class="alert alert-info">
        <i class="bi bi-trash-fill me-2"></i>Distribution annulée.
    </div>
<?php endif; ?>

<?php if ($errorMsg && !$success): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= $messages[$errorMsg] ?? htmlspecialchars($errorMsg) ?>
    </div>
<?php endif; ?>

<!-- ONGLETS -->
<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item">
        <button class="nav-link active nav-tabs-dark" data-bs-toggle="tab" data-bs-target="#manuelle">
            <i class="bi bi-hand-index me-1"></i>Distribution manuelle
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link nav-tabs-dark" data-bs-toggle="tab" data-bs-target="#automatique">
            <i class="bi bi-cpu me-1"></i>Distribution automatique
        </button>
    </li>
</ul>

<div class="tab-content">
    <!-- ========================================================================== -->
    <!-- ONGLET 1 : DISTRIBUTION MANUELLE -->
    <!-- ========================================================================== -->
    <div class="tab-pane fade show active" id="manuelle">
        <div class="row g-4">
            <!-- FORMULAIRE DISTRIBUTION MANUELLE -->
            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-header"><i class="bi bi-plus-circle me-2"></i>Nouvelle distribution</div>
                    <div class="card-body p-4">
                        <form method="POST" action="/distributions/store" id="formDist">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Don disponible <span class="text-danger">*</span></label>
                                <select name="id_don" class="form-select" required id="selectDon">
                                    <option value="">-- Choisir un don --</option>
                                    <?php foreach ($donsDisponibles as $d): ?>
                                    <option value="<?= $d['id_don'] ?>"
                                        data-type="<?= $d['id_type_besoin'] ?>"
                                        data-unite="<?= htmlspecialchars($d['unite']) ?>"
                                        data-disponible="<?= $d['quantite_disponible'] ?>"
                                        <?= $prefillDon === $d['id_don'] ? 'selected' : '' ?>>
                                        Don #<?= $d['id_don'] ?> — <?= htmlspecialchars($d['type_nom']) ?>
                                        (<?= number_format($d['quantite_disponible'], 2, ',', ' ') ?> <?= $d['unite'] ?> dispo)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text" id="donInfo"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Ville destinataire <span class="text-danger">*</span></label>
                                <select name="id_ville" class="form-select" required id="selectVille">
                                    <option value="">-- Choisir une ville --</option>
                                    <?php foreach ($villes as $v): ?>
                                    <option value="<?= $v['id_ville'] ?>"><?= htmlspecialchars($v['nom_ville']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Besoin à satisfaire <span class="text-danger">*</span></label>
                                <select name="id_besoin" class="form-select" required id="selectBesoin">
                                    <option value="">-- D'abord choisir un don et une ville --</option>
                                </select>
                                <div class="form-text text-warning mt-2" id="besoinInfo" style="display:none;">
                                    <i class="bi bi-exclamation-circle me-1"></i>Aucun besoin trouvé. 
                                    <a href="/besoins/create" class="text-warning fw-bold">Créer un besoin</a>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Quantité à attribuer <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="quantite_attribuee" class="form-control" step="0.01" min="0.01"
                                           required id="qteAttrib" placeholder="0">
                                    <span class="input-group-text" id="uniteAttrib">unité</span>
                                </div>
                                <div class="form-text" id="maxInfo"></div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-send me-1"></i>Valider la distribution
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- HISTORIQUE -->
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header"><i class="bi bi-list-ul me-2"></i>Historique (<?= count($distributions) ?>)</div>
                    <div class="table-responsive" style="max-height:520px;overflow-y:auto">
                        <table class="table table-hover mb-0">
                            <thead class="sticky-top">
                                <tr>
                                    <th>Date</th>
                                    <th>Ville</th>
                                    <th>Type</th>
                                    <th class="text-end">Quantité</th>
                                    <th class="text-end">Valeur</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($distributions)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">Aucune distribution</td></tr>
                            <?php else: ?>
                                <?php foreach ($distributions as $dist): ?>
                                <tr>
                                    <td class="small"><?= date('d/m/Y', strtotime($dist['date_distribution'])) ?></td>
                                    <td><?= htmlspecialchars($dist['nom_ville']) ?></td>
                                    <td><?= htmlspecialchars($dist['type_nom']) ?></td>
                                    <td class="text-end"><?= number_format($dist['quantite_attribuee'], 2, ',', ' ') ?> <?= $dist['unite'] ?></td>
                                    <td class="text-end small"><?= number_format($dist['valeur'], 0, ',', ' ') ?> Ar</td>
                                    <td>
                                        <form method="POST" action="/distributions/<?= $dist['id_distribution'] ?>/delete" class="d-inline" onsubmit="return confirm('Êtes-vous sûr ?');"><button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button></form>
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
    </div>

    <!-- ========================================================================== -->
    <!-- ONGLET 2 : DISTRIBUTION AUTOMATIQUE -->
    <!-- ========================================================================== -->
    <div class="tab-pane fade" id="automatique">
        <div class="row g-4">
            <!-- SÉLECTION DU DON -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <i class="bi bi-box-seam me-2"></i>Sélection du don
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Don à distribuer <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="selectDonAuto">
                                <option value="">-- Choisir un don --</option>
                                <?php foreach ($donsDisponibles as $d): ?>
                                <option value="<?= $d['id_don'] ?>"
                                        data-type="<?= htmlspecialchars($d['type_nom']) ?>"
                                        data-unite="<?= htmlspecialchars($d['unite']) ?>"
                                        data-dispo="<?= $d['quantite_disponible'] ?>">
                                    Don #<?= $d['id_don'] ?> — <?= htmlspecialchars($d['type_nom']) ?>
                                    (<?= number_format($d['quantite_disponible'], 2, ',', ' ') ?> <?= $d['unite'] ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="alert alert-info" id="infoDonAuto" style="display:none;">
                            <strong>Type :</strong> <span id="infoType">-</span><br>
                            <strong>Disponible :</strong> <span id="infoDispo">-</span>
                        </div>

                        <hr>

                        <h6 class="mb-3"><i class="bi bi-gear me-2"></i>Mode de distribution</h6>
                        
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary" id="btnAnciennete" disabled>
                                <i class="bi bi-clock-history me-1"></i>
                                Par ancienneté
                            </button>
                            <button type="button" class="btn btn-outline-success" id="btnDemandeMin" disabled>
                                <i class="bi bi-arrow-down-short me-1"></i>
                                Demande minimale
                            </button>
                            <button type="button" class="btn btn-outline-info" id="btnProportionnalite" disabled>
                                <i class="bi bi-pie-chart me-1"></i>
                                Proportionnalité
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RÉSULTAT DE LA SIMULATION -->
            <div class="col-lg-8">
                <div class="card shadow-sm" id="resultSimulation" style="display:none;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>
                            <i class="bi bi-clipboard-check me-2"></i>
                            Résultat de la simulation : <strong id="modeNom"></strong>
                        </span>
                        <button type="button" class="btn btn-sm btn-secondary" id="btnReinitialiser">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Réinitialiser
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- STATISTIQUES -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center p-2">
                                        <small class="text-muted">Villes servies</small>
                                        <h5 class="mb-0" id="statVilles">-</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center p-2">
                                        <small class="text-muted">Distribué</small>
                                        <h5 class="mb-0" id="statDistribue">-</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center p-2">
                                        <small class="text-muted">Restant</small>
                                        <h5 class="mb-0" id="statRestant">-</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center p-2">
                                        <small>Satisfaction</small>
                                        <h5 class="mb-0" id="statSatisfaction">-</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TABLEAU DES DISTRIBUTIONS -->
                        <div class="table-responsive mb-3" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-hover">
                                <thead class="sticky-top">
                                    <tr>
                                        <th>Ville</th>
                                        <th class="text-end">Besoin</th>
                                        <th class="text-end">Attribué</th>
                                        <th class="text-end">% Satisfait</th>
                                    </tr>
                                </thead>
                                <tbody id="tableauResultat">
                                </tbody>
                            </table>
                        </div>

                        <!-- BOUTON VALIDATION -->
                        <button type="button" class="btn btn-success w-100" id="btnValiderSimu">
                            <i class="bi bi-check-circle me-1"></i>
                            Valider et enregistrer ces distributions
                        </button>
                    </div>
                </div>

                <div class="text-center text-muted py-5" id="placeholderSimu">
                    <i class="bi bi-arrow-left-circle display-1"></i>
                    <p class="mt-3">Sélectionnez un don et un mode de distribution</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JAVASCRIPT POUR LA DISTRIBUTION MANUELLE -->
<script>
const selDon   = document.getElementById('selectDon');
const selVille = document.getElementById('selectVille');
const selBes   = document.getElementById('selectBesoin');
const qteIn    = document.getElementById('qteAttrib');
const uniteIn  = document.getElementById('uniteAttrib');
const donInfo  = document.getElementById('donInfo');
const maxInfo  = document.getElementById('maxInfo');
const besoinInfo = document.getElementById('besoinInfo');

async function chargerBesoins() {
    const idVille = selVille.value;
    const optDon  = selDon.options[selDon.selectedIndex];
    const idType  = optDon?.dataset?.type;
    const dispo   = optDon?.dataset?.disponible;
    const unite   = optDon?.dataset?.unite || 'unité';

    uniteIn.textContent = unite;
    besoinInfo.style.display = 'none';

    if (dispo) {
        donInfo.textContent = `Disponible : ${parseFloat(dispo).toLocaleString('fr-FR')} ${unite}`;
        maxInfo.innerHTML   = `<span class="text-primary">Max : ${parseFloat(dispo).toLocaleString('fr-FR')} ${unite}</span>`;
        qteIn.max = dispo;
    }

    selBes.innerHTML = '<option value="">-- Chargement... --</option>';
    if (!idVille || !idType) { selBes.innerHTML = '<option value="">-- D\'abord choisir un don et une ville --</option>'; return; }

    const resp = await fetch(`/distributions/besoins?id_ville=${idVille}&id_type_besoin=${idType}`);
    const data = await resp.json();

    if (!data.length) {
        selBes.innerHTML = '<option value="">Aucun besoin ouvert pour cette ville/type</option>';
        besoinInfo.style.display = 'block';
    } else {
        selBes.innerHTML = '<option value="">-- Choisir un besoin --</option>' +
            data.map(b => `<option value="${b.id_besoin}">
                Besoin #${b.id_besoin} — manquant : ${parseFloat(b.quantite_manquante).toLocaleString('fr-FR')} ${b.unite}
            </option>`).join('');
    }
}

selDon.addEventListener('change', chargerBesoins);
selVille.addEventListener('change', chargerBesoins);

if (selDon.value) chargerBesoins();
</script>

<!-- JAVASCRIPT POUR LA DISTRIBUTION AUTOMATIQUE -->
<script>
let simulationData = null;
let donSelectionne = null;

const selectDonAuto = document.getElementById('selectDonAuto');
const infoDonAuto = document.getElementById('infoDonAuto');
const infoType = document.getElementById('infoType');
const infoDispo = document.getElementById('infoDispo');

const btnAnciennete = document.getElementById('btnAnciennete');
const btnDemandeMin = document.getElementById('btnDemandeMin');
const btnProportionnalite = document.getElementById('btnProportionnalite');
const btnReinitialiser = document.getElementById('btnReinitialiser');
const btnValiderSimu = document.getElementById('btnValiderSimu');

const resultSimulation = document.getElementById('resultSimulation');
const placeholderSimu = document.getElementById('placeholderSimu');
const modeNom = document.getElementById('modeNom');
const tableauResultat = document.getElementById('tableauResultat');

selectDonAuto.addEventListener('change', () => {
    const opt = selectDonAuto.options[selectDonAuto.selectedIndex];
    if (opt.value) {
        donSelectionne = {
            id: opt.value,
            type: opt.dataset.type,
            unite: opt.dataset.unite,
            dispo: opt.dataset.dispo,
        };
        
        infoType.textContent = donSelectionne.type;
        infoDispo.textContent = `${parseFloat(donSelectionne.dispo).toLocaleString('fr-FR')} ${donSelectionne.unite}`;
        infoDonAuto.style.display = 'block';

        btnAnciennete.disabled = false;
        btnDemandeMin.disabled = false;
        btnProportionnalite.disabled = false;
    } else {
        donSelectionne = null;
        infoDonAuto.style.display = 'none';
        btnAnciennete.disabled = true;
        btnDemandeMin.disabled = true;
        btnProportionnalite.disabled = true;
    }

    reinitialiserSimulation();
});

btnAnciennete.addEventListener('click', () => simuler('anciennete', 'Ancienneté'));
btnDemandeMin.addEventListener('click', () => simuler('demande_min', 'Demande minimale'));
btnProportionnalite.addEventListener('click', () => simuler('proportionnalite', 'Proportionnalité'));

async function simuler(mode, nom) {
    if (!donSelectionne) return;

    // Désactiver les boutons pendant la simulation
    btnAnciennete.disabled = true;
    btnDemandeMin.disabled = true;
    btnProportionnalite.disabled = true;

    try {
        const resp = await fetch('/distributions/simuler-auto', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id_don: donSelectionne.id,
                mode: mode,
            }),
        });

        const data = await resp.json();

        if (!data.success) {
            alert('Erreur : ' + (data.error || 'Simulation impossible'));
            btnAnciennete.disabled = false;
            btnDemandeMin.disabled = false;
            btnProportionnalite.disabled = false;
            return;
        }

        simulationData = data;
        afficherResultat(nom);
    } catch (error) {
        alert('Erreur lors de la simulation');
        btnAnciennete.disabled = false;
        btnDemandeMin.disabled = false;
        btnProportionnalite.disabled = false;
    }
}

function afficherResultat(nomMode) {
    modeNom.textContent = nomMode;

    // Stats
    const stats = simulationData.stats;
    document.getElementById('statVilles').textContent = 
        `${stats.nb_villes_servies} / ${stats.nb_villes_total}`;
    document.getElementById('statDistribue').textContent = 
        `${stats.quantite_distribuee.toLocaleString('fr-FR')} ${simulationData.unite}`;
    document.getElementById('statRestant').textContent = 
        `${stats.quantite_restante.toLocaleString('fr-FR')} ${simulationData.unite}`;
    document.getElementById('statSatisfaction').textContent = 
        `${stats.taux_satisfaction}%`;

    // Tableau
    let html = '';
    simulationData.distributions.forEach(dist => {
        const pct = dist.quantite_manquante > 0 
            ? ((dist.quantite_attribuee / dist.quantite_manquante) * 100).toFixed(1)
            : 0;
        const colorClass = pct >= 100 ? 'success' : pct >= 50 ? 'warning' : 'danger';
        
        html += `
            <tr>
                <td>${dist.nom_ville}</td>
                <td class="text-end">${dist.quantite_manquante.toLocaleString('fr-FR')} ${simulationData.unite}</td>
                <td class="text-end fw-bold">${dist.quantite_attribuee.toLocaleString('fr-FR')} ${simulationData.unite}</td>
                <td class="text-end">
                    <span class="badge bg-${colorClass}">${pct}%</span>
                </td>
            </tr>
        `;
    });

    tableauResultat.innerHTML = html;

    placeholderSimu.style.display = 'none';
    resultSimulation.style.display = 'block';
}

btnReinitialiser.addEventListener('click', reinitialiserSimulation);

function reinitialiserSimulation() {
    simulationData = null;
    resultSimulation.style.display = 'none';
    placeholderSimu.style.display = 'block';
    
    if (donSelectionne) {
        btnAnciennete.disabled = false;
        btnDemandeMin.disabled = false;
        btnProportionnalite.disabled = false;
    }
}

btnValiderSimu.addEventListener('click', async () => {
    if (!simulationData || !confirm('Confirmer l\'enregistrement de ces distributions ?')) return;

    btnValiderSimu.disabled = true;

    try {
        const resp = await fetch('/distributions/valider-simulation', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id_don: simulationData.id_don,
                distributions: simulationData.distributions,
            }),
        });

        window.location.href = '/distributions?success=simulation_validee';
    } catch (error) {
        alert('Erreur lors de la validation');
        btnValiderSimu.disabled = false;
    }
});
</script>