<?php
$success = isset($_GET['success']);
$deleted = isset($_GET['deleted']);
$error   = isset($_GET['error']);
?>
<div class="page-header">
    <h1><i class="bi bi-gift-fill"></i>Dons reçus</h1>
    <a href="/dons/create" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Enregistrer un don</a>
</div>

<?php if ($success): ?><div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>Don enregistré avec succès.</div><?php endif; ?>
<?php if ($deleted): ?><div class="alert alert-info"><i class="bi bi-trash-fill me-2"></i>Don supprimé.</div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Impossible de supprimer ce don (des distributions lui sont associées).</div><?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header"><i class="bi bi-list-ul me-2"></i>Liste des dons (<?= count($dons) ?>)</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Donateur</th>
                    <th>Type</th>
                    <th>Catégorie</th>
                    <th class="text-end">Quantité</th>
                    <th class="text-end">Distribuée</th>
                    <th class="text-center">Statut</th>
                    <th class="text-end">Valeur</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($dons)): ?>
                <tr><td colspan="9" class="text-center text-muted py-4">Aucun don enregistré</td></tr>
            <?php else: ?>
                <?php foreach ($dons as $d):
                    $restant = $d['quantite'] - $d['quantite_distribuee'];
                    $pct = $d['quantite'] > 0 ? round(($d['quantite_distribuee'] / $d['quantite']) * 100) : 0;
                    $statusClass = $restant <= 0 ? 'success' : ($pct > 0 ? 'warning' : 'secondary');
                    $statusLabel = $restant <= 0 ? 'Distribué' : ($pct > 0 ? 'Partiel' : 'En attente');
                    $badgeClass = match($d['nom_categorie']) {
                        'nature' => 'badge-nature', 'materiaux' => 'badge-materiaux', default => 'badge-argent'
                    };
                ?>
                <tr>
                    <td class="small text-muted"><?= date('d/m/Y', strtotime($d['date_saisie'])) ?></td>
                    <td><?= htmlspecialchars(trim($d['donateur_nom'])) ?: '<em class="text-muted">Anonyme</em>' ?></td>
                    <td><?= htmlspecialchars($d['type_nom']) ?></td>
                    <td><span class="badge <?= $badgeClass ?>"><?= $d['nom_categorie'] ?></span></td>
                    <td class="text-end"><?= number_format($d['quantite'], 2, ',', ' ') ?> <?= $d['unite'] ?></td>
                    <td class="text-end text-success"><?= number_format($d['quantite_distribuee'], 2, ',', ' ') ?> <?= $d['unite'] ?></td>
                    <td class="text-center"><span class="badge bg-<?= $statusClass ?>"><?= $statusLabel ?></span></td>
                    <td class="text-end small"><?= number_format($d['valeur'], 0, ',', ' ') ?> Ar</td>
                    <td class="text-center">
                        <?php if ($restant > 0): ?>
                        <a href="/distributions?don=<?= $d['id_don'] ?>" class="btn btn-sm btn-success me-2" title="Distribuer">
                            <i class="bi bi-send me-1"></i>Distribuer
                        </a>
                        <?php endif; ?>
                        <form method="POST" action="/dons/<?= $d['id_don'] ?>/delete" class="d-inline" onsubmit="return confirm('Êtes-vous sûr ? Cette action est irréversible.');"><button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash me-1"></i>Supprimer</button></form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>