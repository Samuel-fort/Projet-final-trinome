<?php
$success = isset($_GET['success']);
$deleted = isset($_GET['deleted']);
$error   = isset($_GET['error']);
?>
<div class="page-header">
    <h1><i class="bi bi-geo-alt-fill"></i>Gestion des villes</h1>
    <a href="/villes/create" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Nouvelle ville</a>
</div>

<?php if ($success): ?><div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>Ville enregistrée avec succès.</div><?php endif; ?>
<?php if ($deleted): ?><div class="alert alert-info"><i class="bi bi-trash-fill me-2"></i>Ville supprimée.</div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Impossible de supprimer (des besoins ou distributions sont liées).</div><?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header"><i class="bi bi-list-ul me-2"></i>Liste des villes (<?= count($villes) ?>)</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom de la ville</th>
                    <th>Région</th>
                    <th>Date ajout</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($villes)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Aucune ville enregistrée</td></tr>
            <?php else: ?>
                <?php foreach ($villes as $ville): ?>
                <tr>
                    <td class="text-muted"><?= $ville['id_ville'] ?></td>
                    <td><strong><?= htmlspecialchars($ville['nom_ville']) ?></strong></td>
                    <td><?= htmlspecialchars($ville['region'] ?? '-') ?></td>
                    <td class="text-muted small"><?= date('d/m/Y', strtotime($ville['date_creation'])) ?></td>
                    <td class="text-center">
                        <a href="/villes/<?= $ville['id_ville'] ?>/edit" class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="/villes/<?= $ville['id_ville'] ?>/delete" class="d-inline"
                              onsubmit="return confirm('Supprimer cette ville ?')">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
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