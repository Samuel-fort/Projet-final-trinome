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
