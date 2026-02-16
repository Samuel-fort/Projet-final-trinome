<?php
$success = isset($_GET['success']);
$deleted = isset($_GET['deleted']);
$error   = isset($_GET['error']);
?>
<div class="page-header">
    <h1><i class="bi bi-people-fill"></i>Donateurs</h1>
    <a href="/donateurs/create" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Nouveau donateur</a>
</div>

<?php if ($success): ?><div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>Donateur enregistré avec succès.</div><?php endif; ?>
<?php if ($deleted): ?><div class="alert alert-info"><i class="bi bi-trash-fill me-2"></i>Donateur supprimé.</div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Impossible de supprimer ce donateur (des dons lui sont associés).</div><?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header"><i class="bi bi-list-ul me-2"></i>Liste des donateurs (<?= count($donateurs) ?>)</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nom / Organisation</th>
                    <th>Type</th>
                    <th>Contact</th>
                    <th class="text-center">Nb dons</th>
                    <th class="text-end">Valeur totale</th>
                    <th>Date</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($donateurs)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Aucun donateur enregistré</td></tr>
            <?php else: ?>
                <?php foreach ($donateurs as $d):
                    $affichage = trim(($d['prenom'] ?? '') . ' ' . ($d['nom'] ?? ''));
                    if ($d['organisation']) $affichage = ($affichage ? $affichage . ' — ' : '') . $d['organisation'];
                    if (!$affichage) $affichage = '<em class="text-muted">Anonyme</em>';
                    $typeBadge = $d['type_donateur'] === 'entreprise' ? 'bg-primary' : 'bg-secondary';
                ?>
                <tr>
                    <td><?= $affichage ?></td>
                    <td><span class="badge <?= $typeBadge ?>"><?= htmlspecialchars($d['type_donateur']) ?></span></td>
                    <td>
                        <?php if ($d['telephone']): ?><div class="small"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($d['telephone']) ?></div><?php endif; ?>
                        <?php if ($d['email']): ?><div class="small text-muted"><?= htmlspecialchars($d['email']) ?></div><?php endif; ?>
                    </td>
                    <td class="text-center"><span class="badge bg-light text-dark"><?= $d['nb_dons'] ?></span></td>
                    <td class="text-end fw-semibold"><?= number_format($d['valeur_totale'], 0, ',', ' ') ?> Ar</td>
                    <td class="small text-muted"><?= date('d/m/Y', strtotime($d['date_inscription'])) ?></td>
                    <td class="text-center">
                        <a href="/donateurs/<?= $d['id_donateur'] ?>/edit" class="btn btn-sm btn-primary me-2">
                            <i class="bi bi-pencil me-1"></i>Modifier
                        </a>
                        <form method="POST" action="/donateurs/<?= $d['id_donateur'] ?>/delete" class="d-inline" onsubmit="return confirm('Êtes-vous sûr ? Cette action est irréversible.');"><button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash me-1"></i>Supprimer</button></form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>