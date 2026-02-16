<?php
$success = isset($_GET['success']);
$deleted = isset($_GET['deleted']);
$error   = isset($_GET['error']);
?>
<div class="page-header">
    <h1><i class="bi bi-tags-fill"></i>Types de besoins</h1>
    <a href="/types-besoins/create" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Nouveau type</a>
</div>

<?php if ($success): ?><div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>Type enregistré avec succès.</div><?php endif; ?>
<?php if ($deleted): ?><div class="alert alert-info"><i class="bi bi-trash-fill me-2"></i>Type supprimé.</div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Impossible de supprimer (des besoins ou dons utilisent ce type).</div><?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header"><i class="bi bi-list-ul me-2"></i>Catalogue des types (<?= count($types) ?>)</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Catégorie</th>
                    <th>Nom</th>
                    <th>Unité</th>
                    <th class="text-end">Prix unitaire</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($types)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Aucun type enregistré</td></tr>
            <?php else: ?>
                <?php foreach ($types as $t):
                    $badgeClass = match($t['nom_categorie']) {
                        'nature' => 'badge-nature', 'materiaux' => 'badge-materiaux', default => 'badge-argent'
                    };
                ?>
                <tr>
                    <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($t['nom_categorie']) ?></span></td>
                    <td><strong><?= htmlspecialchars($t['nom']) ?></strong></td>
                    <td><?= htmlspecialchars($t['unite']) ?></td>
                    <td class="text-end"><?= number_format($t['prix_unitaire'], 0, ',', ' ') ?> Ar / <?= $t['unite'] ?></td>
                    <td class="text-center">
                        <a href="/types-besoins/<?= $t['id_type_besoin'] ?>/edit" class="btn btn-sm btn-primary me-2">
                            <i class="bi bi-pencil me-1"></i>Modifier
                        </a>
                        <form method="POST" action="/types-besoins/<?= $t['id_type_besoin'] ?>/delete" class="d-inline" onsubmit="return confirm('Êtes-vous sûr ? Cette action est irréversible.');"><button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash me-1"></i>Supprimer</button></form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>