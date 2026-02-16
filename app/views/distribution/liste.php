<?php
$success = isset($_GET['success']);
$deleted = isset($_GET['deleted']);
$errorMsg = isset($_GET['error']) ? urldecode($_GET['error']) : null;
$prefillDon = isset($_GET['don']) ? (int)$_GET['don'] : 0;
?>
<div class="page-header">
    <h1><i class="bi bi-send-fill"></i>Distributions</h1>
</div>

<?php if ($success): ?><div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>Distribution enregistrée avec succès.</div><?php endif; ?>
<?php if ($deleted): ?><div class="alert alert-info"><i class="bi bi-trash-fill me-2"></i>Distribution annulée.</div><?php endif; ?>
<?php if ($errorMsg && $errorMsg !== '1'): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($errorMsg) ?></div><?php endif; ?>
<?php if ($errorMsg === '1'): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Tous les champs sont requis.</div><?php endif; ?>

<div class="row g-4">

    <!-- FORMULAIRE DISTRIBUTION -->
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

    <!-- LISTE DES DISTRIBUTIONS -->
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
                                <form method="POST" action="/distributions/<?= $dist['id_distribution'] ?>/delete" class="d-inline" onsubmit="return confirm('Êtes-vous sûr ? Cette action est irréversible.');"><button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash me-1"></i>Annuler</button></form>
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
const selDon   = document.getElementById('selectDon');
const selVille = document.getElementById('selectVille');
const selBes   = document.getElementById('selectBesoin');
const qteIn    = document.getElementById('qteAttrib');
const uniteIn  = document.getElementById('uniteAttrib');
const donInfo  = document.getElementById('donInfo');
const maxInfo  = document.getElementById('maxInfo');

async function chargerBesoins() {
    const idVille = selVille.value;
    const optDon  = selDon.options[selDon.selectedIndex];
    const idType  = optDon?.dataset?.type;
    const dispo   = optDon?.dataset?.disponible;
    const unite   = optDon?.dataset?.unite || 'unité';

    uniteIn.textContent = unite;

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
    } else {
        selBes.innerHTML = '<option value="">-- Choisir un besoin --</option>' +
            data.map(b => `<option value="${b.id_besoin}">
                Besoin #${b.id_besoin} — manquant : ${parseFloat(b.quantite_manquante).toLocaleString('fr-FR')} ${b.unite}
            </option>`).join('');
    }
}

selDon.addEventListener('change', chargerBesoins);
selVille.addEventListener('change', chargerBesoins);

// Pré-remplir si paramètre ?don=X
if (selDon.value) chargerBesoins();
</script>