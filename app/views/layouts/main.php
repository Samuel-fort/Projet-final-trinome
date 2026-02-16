<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'BNGRC - Gestion des Dons') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bngrc-primary: #0a3d62;
            --bngrc-accent: #e74c3c;
            --bngrc-light: #f0f4f8;
            --bngrc-gold: #f39c12;
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--bngrc-light);
        }
        h1, h2, h3, h4, h5, .navbar-brand, th { font-family: 'Sora', sans-serif; }

        /* NAVBAR */
        .navbar {
            background: var(--bngrc-primary);
            border-bottom: 3px solid var(--bngrc-accent);
            padding: 0.6rem 1.5rem;
        }
        .navbar-brand {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff !important;
            letter-spacing: 0.5px;
        }
        .navbar-brand span { color: var(--bngrc-gold); }
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            font-size: 0.88rem;
            font-weight: 500;
            padding: 0.5rem 0.9rem !important;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            color: #fff !important;
            background: rgba(255,255,255,0.12);
        }
        .nav-link i { margin-right: 5px; }

        /* CONTENT */
        .main-content { padding: 2rem 1.5rem; }
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.8rem;
        }
        .page-header h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--bngrc-primary);
            margin: 0;
        }
        .page-header h1 i { color: var(--bngrc-accent); margin-right: 8px; }

        /* CARDS */
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
        .card-header {
            background: var(--bngrc-primary);
            color: #fff;
            border-radius: 12px 12px 0 0 !important;
            font-family: 'Sora', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.9rem 1.2rem;
        }

        /* STAT CARDS */
        .stat-card {
            border-radius: 12px;
            padding: 1.3rem 1.5rem;
            color: #fff;
            border: none;
        }
        .stat-card.primary { background: linear-gradient(135deg, #0a3d62, #1a5276); }
        .stat-card.danger  { background: linear-gradient(135deg, #c0392b, #e74c3c); }
        .stat-card.success { background: linear-gradient(135deg, #1a7a4a, #27ae60); }
        .stat-card.warning { background: linear-gradient(135deg, #d68910, #f39c12); }
        .stat-card .stat-label { font-size: 0.8rem; opacity: 0.85; font-weight: 500; }
        .stat-card .stat-value { font-size: 1.7rem; font-weight: 700; font-family: 'Sora', sans-serif; }
        .stat-card .stat-icon { font-size: 2rem; opacity: 0.3; }

        /* TABLE */
        .table th { background: #e8eef4; color: var(--bngrc-primary); font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .table td { vertical-align: middle; font-size: 0.9rem; }
        .table-hover tbody tr:hover { background-color: #f0f4f8; }

        /* BADGES */
        .badge-nature   { background: #d5f5e3; color: #1e8449; }
        .badge-materiaux{ background: #d6eaf8; color: #1a5276; }
        .badge-argent   { background: #fef9e7; color: #9a7d0a; border: 1px solid #f9e79f; }

        /* ALERTS */
        .alert { border-radius: 10px; font-size: 0.9rem; }

        /* PROGRESS BAR */
        .progress { height: 8px; border-radius: 4px; }

        /* FOOTER */
        footer { color: #6c757d; font-size: 0.82rem; margin-top: 3rem; padding: 1rem 1.5rem; border-top: 1px solid #dee2e6; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">
            <i class="bi bi-heart-pulse-fill me-2" style="color: var(--bngrc-gold)"></i>
            BNGRC <span>| Gestion des Dons</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto gap-1">
                <li class="nav-item">
                    <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/') === 0 && $_SERVER['REQUEST_URI'] === '/') ? 'active' : '' ?>" href="/">
                        <i class="bi bi-speedometer2"></i>Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/villes') !== false ? 'active' : '' ?>" href="/villes">
                        <i class="bi bi-geo-alt"></i>Villes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/besoins') !== false ? 'active' : '' ?>" href="/besoins">
                        <i class="bi bi-clipboard-list"></i>Besoins
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/types-besoins') !== false ? 'active' : '' ?>" href="/types-besoins">
                        <i class="bi bi-tags"></i>Types
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/donateurs') !== false ? 'active' : '' ?>" href="/donateurs">
                        <i class="bi bi-people"></i>Donateurs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/dons') !== false ? 'active' : '' ?>" href="/dons">
                        <i class="bi bi-gift"></i>Dons
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/distributions') !== false ? 'active' : '' ?>" href="/distributions">
                        <i class="bi bi-send"></i>Distributions
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="main-content">
    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($flash_success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (!empty($flash_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($flash_error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?= $content ?? '' ?>
</div>

<footer class="text-center">
    BNGRC &mdash; Système de gestion des dons pour sinistrés &mdash; 2026
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>