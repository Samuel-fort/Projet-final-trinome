<div class="page-header">
    <h1><i class="bi bi-clipboard-data"></i> Récapitulation générale</h1>
    <div>
        <button type="button" class="btn btn-primary btn-sm" id="btnActualiser">
            <i class="bi bi-arrow-clockwise me-1"></i>Actualiser
        </button>
        <div class="form-check form-switch d-inline-block ms-3">
            <input class="form-check-input" type="checkbox" id="autoRefresh">
            <label class="form-check-label" for="autoRefresh">
                Auto-refresh (15s)
            </label>
        </div>
    </div>
</div>

<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>
    <strong>Dernière mise à jour :</strong> <span id="lastUpdate">-</span>
</div>

<!-- STATISTIQUES GLOBALES -->
<div class="row g-3 mb-4" id="statsGlobales">
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <i class="bi bi-geo-alt-fill text-primary fs-3"></i>
                <h2 class="mb-0 mt-2" id="statVilles">-</h2>
                <small class="text-muted">Villes sinistrées</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <i class="bi bi-list-check text-info fs-3"></i>
                <h2 class="mb-0 mt-2" id="statBesoins">-</h2>
                <small class="text-muted">Besoins identifiés</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="bi bi-check-circle-fill text-success fs-3"></i>
                <h2 class="mb-0 mt-2" id="statSatisfaits">-</h2>
                <small class="text-muted">Besoins satisfaits</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-danger">
            <div class="card-body text-center">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-3"></i>
                <h2 class="mb-0 mt-2" id="statRestants">-</h2>
                <small class="text-muted">Besoins restants</small>
            </div>
        </div>
    </div>
</div>

<!-- MONTANTS -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body">
                <strong class="text-muted d-block mb-1">Valeur totale des besoins</strong>
                <h3 class="mb-0" id="valeurTotale">-</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <strong class="d-block mb-1">Valeur satisfaite</strong>
                <h3 class="mb-0" id="valeurSatisfaite">-</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <strong class="d-block mb-1">Valeur manquante</strong>
                <h3 class="mb-0" id="valeurManquante">-</h3>
            </div>
        </div>
    </div>
</div>

<!-- BESOINS PAR VILLE -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <i class="bi bi-building me-2"></i>Besoins par ville
            </div>
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-hover mb-0">
                    <thead class="sticky-top">
                        <tr>
                            <th>Ville</th>
                            <th class="text-end">Montant total</th>
                            <th class="text-end">Satisfait</th>
                            <th class="text-end">%</th>
                        </tr>
                    </thead>
                    <tbody id="tableauVilles">
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="spinner-border spinner-border-sm me-2"></i>Chargement...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- STATISTIQUES DONS -->
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <i class="bi bi-gift me-2"></i>Statistiques des dons
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block">Nombre de dons</small>
                            <h4 class="mb-0" id="nbDons">-</h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block">Valeur nature</small>
                            <h4 class="mb-0" id="valeurNature">-</h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block">Valeur matériaux</small>
                            <h4 class="mb-0" id="valeurMateriaux">-</h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block">Montant argent</small>
                            <h4 class="mb-0" id="montantArgent">-</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DÉTAILS BESOINS RESTANTS -->
<div class="card shadow-sm">
    <div class="card-header">
        <i class="bi bi-exclamation-triangle me-2"></i>
        Détails des besoins restants
    </div>
    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
        <table class="table table-sm table-hover mb-0">
            <thead class="sticky-top">
                <tr>
                    <th>Ville</th>
                    <th>Catégorie</th>
                    <th>Type</th>
                    <th class="text-end">Demandé</th>
                    <th class="text-end">Reçu</th>
                    <th class="text-end">Manquant</th>
                    <th class="text-end">Valeur manquante</th>
                </tr>
            </thead>
            <tbody id="tableauBesoinsRestants">
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="spinner-border spinner-border-sm me-2"></i>Chargement...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
let autoRefreshInterval = null;

async function chargerDonnees() {
    try {
        const resp = await fetch('/dashboard/recap-data');
        const data = await resp.json();
        
        // MAJ timestamp
        document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString('fr-FR');

        // Stats globales
        const stats = data.stats;
        document.getElementById('statVilles').textContent = stats.nb_villes;
        document.getElementById('statBesoins').textContent = stats.nb_besoins_total;
        document.getElementById('statSatisfaits').textContent = stats.nb_besoins_satisfaits;
        document.getElementById('statRestants').textContent = stats.nb_besoins_total - stats.nb_besoins_satisfaits;

        document.getElementById('valeurTotale').textContent = 
            parseFloat(stats.valeur_totale_besoins || 0).toLocaleString('fr-FR') + ' Ar';
        document.getElementById('valeurSatisfaite').textContent = 
            parseFloat(stats.valeur_totale_recue || 0).toLocaleString('fr-FR') + ' Ar';
        document.getElementById('valeurManquante').textContent = 
            parseFloat(stats.valeur_totale_manquante || 0).toLocaleString('fr-FR') + ' Ar';

        // Besoins par ville
        let htmlVilles = '';
        data.besoins_par_ville.forEach(v => {
            const pct = v.pourcentage_satisfait || 0;
            const colorClass = pct >= 75 ? 'success' : pct >= 50 ? 'warning' : 'danger';
            
            htmlVilles += `
                <tr>
                    <td>${v.nom_ville}</td>
                    <td class="text-end">${parseFloat(v.montant_total || 0).toLocaleString('fr-FR')} Ar</td>
                    <td class="text-end">${parseFloat(v.montant_satisfait || 0).toLocaleString('fr-FR')} Ar</td>
                    <td class="text-end">
                        <span class="badge bg-${colorClass}">${pct.toFixed(1)}%</span>
                    </td>
                </tr>
            `;
        });
        document.getElementById('tableauVilles').innerHTML = htmlVilles || 
            '<tr><td colspan="4" class="text-center text-muted py-3">Aucune donnée</td></tr>';

        // Stats dons
        const statsDons = data.stats_dons;
        document.getElementById('nbDons').textContent = statsDons.nb_dons_total;
        document.getElementById('valeurNature').textContent = 
            parseFloat(statsDons.valeur_nature || 0).toLocaleString('fr-FR') + ' Ar';
        document.getElementById('valeurMateriaux').textContent = 
            parseFloat(statsDons.valeur_materiaux || 0).toLocaleString('fr-FR') + ' Ar';
        document.getElementById('montantArgent').textContent = 
            parseFloat(statsDons.montant_argent || 0).toLocaleString('fr-FR') + ' Ar';

        // Besoins restants
        let htmlRestants = '';
        data.besoins_restants.forEach(b => {
            htmlRestants += `
                <tr>
                    <td>${b.nom_ville}</td>
                    <td><span class="badge bg-secondary">${b.nom_categorie}</span></td>
                    <td>${b.type_nom}</td>
                    <td class="text-end">${parseFloat(b.quantite_demandee).toLocaleString('fr-FR')} ${b.unite}</td>
                    <td class="text-end text-success">${parseFloat(b.quantite_recue).toLocaleString('fr-FR')} ${b.unite}</td>
                    <td class="text-end text-danger fw-bold">${parseFloat(b.quantite_manquante).toLocaleString('fr-FR')} ${b.unite}</td>
                    <td class="text-end">${parseFloat(b.valeur_manquante).toLocaleString('fr-FR')} Ar</td>
                </tr>
            `;
        });
        document.getElementById('tableauBesoinsRestants').innerHTML = htmlRestants || 
            '<tr><td colspan="7" class="text-center text-success py-4"><i class="bi bi-check-circle me-2"></i>Tous les besoins sont satisfaits !</td></tr>';

    } catch (error) {
        console.error('Erreur de chargement:', error);
        alert('Erreur lors du chargement des données');
    }
}

// Bouton actualiser
document.getElementById('btnActualiser').addEventListener('click', chargerDonnees);

// Auto-refresh
document.getElementById('autoRefresh').addEventListener('change', (e) => {
    if (e.target.checked) {
        autoRefreshInterval = setInterval(chargerDonnees, 15000); // 15 secondes
        chargerDonnees(); // Charger immédiatement
    } else {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
        }
    }
});

// Charger au démarrage
chargerDonnees();
</script>