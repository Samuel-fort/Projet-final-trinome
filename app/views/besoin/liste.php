<?php
$success = isset($_GET['success']);
$deleted = isset($_GET['deleted']);
$filtreVille = isset($_GET['ville']) ? (int)$_GET['ville'] : 0;
?>
<div class="page-header">
    <h1><i class="bi bi-clipboard-list-fill"></i>Besoins des sinistrés</h1>
    <a href="/besoins/create" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Saisir un besoin</a>
</div>

<?php if ($success): ?><div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>Besoin enregistré avec succès.</div><?php endif; ?>
<?php if ($deleted): ?><div class="alert alert-info"><i class="bi bi-trash-fill me-2"></i>Besoin supprimé.</div><?php endif; ?>

<!-- FILTRE -->
<div class="card mb-3 shadow-sm">
    <div class="card-body py-2">
        <form method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="d-flex align-items-center gap-2">
                <label class="fw-semibold small mb-0">Filtrer :</label>
                <input type="text" name="ville" class="form-control form-control-sm" placeholder="ID ville" value="<?= $filtreVille ?: '' ?>" style="width:100px">
            </div>
            <button type="submit" class="btn btn-sm btn-outline-primary">Filtrer</button>
            <?php if ($filtreVille): ?><a href="/besoins" class="btn btn-sm btn-outline-secondary">Tout voir</a><?php endif; ?>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header"><i class="bi bi-list-ul me-2"></i>Liste des besoins (<?= count($besoins) ?>)</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Ville</th>
                    <th>Type de besoin</th>
                    <th>Catégorie</th>
                    <th class="text-end">Qté demandée</th>
                    <th class="text-end">Qté reçue</th>
                    <th class="text-center">Couverture</th>
                    <th class="text-end">Valeur totale</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($besoins)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Aucun besoin enregistré</td></tr>
            <?php else: ?>
                <?php foreach ($besoins as $b):
                    $taux = $b['quantite_demandee'] > 0 ? round(($b['quantite_recue'] / $b['quantite_demandee']) * 100) : 0;
                    $badgeClass = match($b['nom_categorie']) {
                        'nature' => 'badge-nature', 'materiaux' => 'badge-materiaux', default => 'badge-argent'
                    };
                    $barColor = $taux >= 80 ? 'success' : ($taux >= 40 ? 'warning' : 'danger');
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($b['nom_ville']) ?></strong></td>
                    <td><?= htmlspecialchars($b['type_nom']) ?></td>
                    <td><span class="badge <?= $badgeClass ?>"><?= $b['nom_categorie'] ?></span></td>
                    <td class="text-end"><?= number_format($b['quantite_demandee'], 2, ',', ' ') ?> <?= $b['unite'] ?></td>
                    <td class="text-end text-success"><?= number_format($b['quantite_recue'], 2, ',', ' ') ?> <?= $b['unite'] ?></td>
                    <td class="text-center" style="min-width:110px">
                        <div class="progress mb-1">
                            <div class="progress-bar bg-<?= $barColor ?>" style="width:<?= $taux ?>%"></div>
                        </div>
                        <small><?= $taux ?>%</small>
                    </td>
                    <td class="text-end small"><?= number_format($b['valeur_totale'], 0, ',', ' ') ?> Ar</td>
                    <td class="text-center">
                        <a href="/besoins/<?= $b['id_besoin'] ?>/edit" class="btn btn-sm btn-primary me-2">
                            <i class="bi bi-pencil me-1"></i>Modifier
                        </a>
                        <form method="POST" action="/besoins/<?= $b['id_besoin'] ?>/delete" class="d-inline" onsubmit="return confirm('Êtes-vous sûr ? Cette action est irréversible.');"><button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash me-1"></i>Supprimer</button></form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>