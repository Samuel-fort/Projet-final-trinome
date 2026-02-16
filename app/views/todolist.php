<?php
$todos = [
    ['id' => 1, 'title' => 'Implémenter la gestion des dons', 'completed' => true],
    ['id' => 2, 'title' => 'Créer l\'interface de distribution', 'completed' => true],
    ['id' => 3, 'title' => 'Ajouter les statistiques du dashboard', 'completed' => true],
    ['id' => 4, 'title' => 'Corriger les erreurs de connexion MySQL', 'completed' => true],
    ['id' => 5, 'title' => 'Créer le footer avec liens', 'completed' => true],
    ['id' => 6, 'title' => 'Ajouter la gestion des utilisateurs', 'completed' => false],
    ['id' => 7, 'title' => 'Implémenter les notifications email', 'completed' => false],
    ['id' => 8, 'title' => 'Ajouter les tests unitaires', 'completed' => false],
];

$completed = count(array_filter($todos, fn($t) => $t['completed']));
$total = count($todos);
$percentage = round(($completed / $total) * 100);
?>

<div class="page-header mb-4">
    <h1><i class="bi bi-check2-square"></i> Todolist du Projet</h1>
</div>

<!-- PROGRESS BAR -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0">Progression générale</h5>
            <span class="badge bg-primary"><?= $completed ?>/<?= $total ?> tâches complétées</span>
        </div>
        <div class="progress" style="height: 25px;">
            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100">
                <?= $percentage ?>%
            </div>
        </div>
    </div>
</div>

<!-- TODOS LIST -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php foreach ($todos as $todo): ?>
                        <li class="list-group-item d-flex align-items-center <?= $todo['completed'] ? 'opacity-75' : '' ?>">
                            <input class="form-check-input me-3" type="checkbox" <?= $todo['completed'] ? 'checked' : '' ?> disabled>
                            <span class="<?= $todo['completed'] ? 'text-decoration-line-through text-muted' : '' ?>">
                                <?= htmlspecialchars($todo['title']) ?>
                            </span>
                            <?php if ($todo['completed']): ?>
                                <span class="badge bg-success ms-auto">
                                    <i class="bi bi-check"></i> Complété
                                </span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- STATS SIDEBAR -->
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <i class="bi bi-check-circle"></i> Tâches complétées
            </div>
            <div class="card-body text-center">
                <h3 class="text-success"><?= $completed ?>/<?= $total ?></h3>
                <small class="text-muted">Tâches finalisées</small>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-warning text-dark">
                <i class="bi bi-hourglass-split"></i> Tâches en attente
            </div>
            <div class="card-body text-center">
                <h3 class="text-warning"><?= $total - $completed ?>/<?= $total ?></h3>
                <small class="text-muted">Tâches restantes</small>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> À propos
            </div>
            <div class="card-body">
                <p class="small">Suivi des tâches du projet de gestion des dons pour sinistrés.</p>
                <p class="small mb-0">Les tâches complétées sont cochées et grisées.</p>
            </div>
        </div>
    </div>
</div>
