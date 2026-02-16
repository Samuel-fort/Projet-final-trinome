<?php
$pct = ($totalBesoins['valeur_besoins'] > 0)
    ? round(($totalBesoins['valeur_couverte'] / $totalBesoins['valeur_besoins']) * 100)
    : 0;
$pctDons = ($statsGlobaux['valeur_totale'] > 0)
    ? round(($statsGlobaux['valeur_distribuee'] / $statsGlobaux['valeur_totale']) * 100)
    : 0;
?>
<div class="page-header">
    <h1><i class="bi bi-speedometer2"></i>Tableau de bord</h1>
    <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y') ?></span>
</div>

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card primary d-flex justify-content-between align-items-center shadow-sm">
            <div>
                <div class="stat-label">Total dons reçus</div>
                <div class="stat-value"><?= number_format($statsGlobaux['valeur_totale'], 0, ',', ' ') ?> Ar</div>
                <div style="font-size:.8rem;opacity:.7"><?= $statsGlobaux['nb_dons'] ?> dons enregistrés</div>
            </div>
            <i class="bi bi-gift-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card danger d-flex justify-content-between align-items-center shadow-sm">
            <div>
                <div class="stat-label">Total besoins</div>
                <div class="stat-value"><?= number_format($totalBesoins['valeur_besoins'], 0, ',', ' ') ?> Ar</div>
                <div style="font-size:.8rem;opacity:.7"><?= $totalBesoins['nb_besoins'] ?> besoins</div>
            </div>
            <i class="bi bi-clipboard2-pulse-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card success d-flex justify-content-between align-items-center shadow-sm">
            <div>
                <div class="stat-label">Dons distribués</div>
                <div class="stat-value"><?= number_format($statsGlobaux['valeur_distribuee'], 0, ',', ' ') ?> Ar</div>
                <div style="font-size:.8rem;opacity:.7"><?= $pctDons ?>% des dons</div>
            </div>
            <i class="bi bi-send-check-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card warning d-flex justify-content-between align-items-center shadow-sm">
            <div>
                <div class="stat-label">Besoins couverts</div>
                <div class="stat-value"><?= number_format($totalBesoins['valeur_couverte'], 0, ',', ' ') ?> Ar</div>
                <div style="font-size:.8rem;opacity:.7"><?= $pct ?>% des besoins</div>
            </div>
            <i class="bi bi-check2-all stat-icon"></i>
        </div>
    </div>
</div>

<!-- BARRE DE PROGRESSION GLOBALE -->
<div class="card mb-4 shadow-sm">
    <div class="card-header"><i class="bi bi-bar-chart-fill me-2"></i>Avancement global de la couverture des besoins</div>
    <div class="card-body">
        <div class="d-flex justify-content-between mb-1">
            <small class="fw-semibold">Besoins couverts</small>
            <small class="fw-bold"><?= $pct ?>%</small>
        </div>
        <div class="progress mb-2" style="height:14px">
            <div class="progress-bar bg-success" style="width:<?= $pct ?>%" role="progressbar"></div>
        </div>
        <div class="d-flex justify-content-between mb-1 mt-3">
            <small class="fw-semibold">Dons distribués</small>
            <small class="fw-bold"><?= $pctDons ?>%</small>
        </div>
        <div class="progress" style="height:14px">
            <div class="progress-bar bg-primary" style="width:<?= $pctDons ?>%" role="progressbar"></div>
        </div>
    </div>
</div>

<!-- TABLEAU VILLES -->
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-map-fill me-2"></i>Besoins et dons par ville</span>
        <a href="/besoins/create" class="btn btn-sm btn-warning"><i class="bi bi-plus-circle me-1"></i>Saisir un besoin</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Ville</th>
                    <th>Région</th>
                    <th class="text-center">Nb besoins</th>
                    <th class="text-end">Valeur des besoins</th>
                    <th class="text-end">Montant reçu</th>
                    <th class="text-center">Taux</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($dashboardVilles)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Aucune ville enregistrée</td></tr>
            <?php else: ?>
                <?php foreach ($dashboardVilles as $v):
                    $taux = $v['valeur_besoins'] > 0 ? round(($v['valeur_recue'] / $v['valeur_besoins']) * 100) : 0;
                    $barColor = $taux >= 80 ? 'success' : ($taux >= 40 ? 'warning' : 'danger');
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($v['nom_ville']) ?></strong></td>
                    <td><span class="text-muted"><?= htmlspecialchars($v['region'] ?? '-') ?></span></td>
                    <td class="text-center"><span class="badge bg-secondary"><?= $v['nb_besoins'] ?></span></td>
                    <td class="text-end"><?= number_format($v['valeur_besoins'], 0, ',', ' ') ?> Ar</td>
                    <td class="text-end text-success fw-semibold"><?= number_format($v['valeur_recue'], 0, ',', ' ') ?> Ar</td>
                    <td class="text-center" style="min-width:120px">
                        <div class="progress">
                            <div class="progress-bar bg-<?= $barColor ?>" style="width:<?= $taux ?>%"></div>
                        </div>
                        <small class="text-muted"><?= $taux ?>%</small>
                    </td>
                    <td>
                        <a href="/besoins?ville=<?= $v['id_ville'] ?>" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>