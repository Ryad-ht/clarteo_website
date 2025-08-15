<?php
// Configuration
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB

// Fonction de nettoyage automatique des anciens fichiers
function cleanupOldFiles() {
    $upload_dir = 'uploads/';
    $files = glob($upload_dir . '*');
    $cutoff_time = time() - (2 * 60 * 60); // 2 heures

    foreach ($files as $file) {
        if (is_file($file) && filemtime($file) < $cutoff_time && basename($file) !== '.htaccess') {
            unlink($file);
        }
    }
}

// Nettoyage automatique
cleanupOldFiles();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clartéo Lab - Nettoyage CSV</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>
    <meta name="robots" content="noindex,nofollow">
</head>
<body>
    <div class="page-container">
        <header class="page-header">
            <h1>Clartéo Lab</h1>
            <p>Nettoyez et analysez vos fichiers CSV en quelques clics</p>
        </header>

        <main class="page-main">
            
            <!-- Upload Area -->
            <div class="upload-area" id="upload-area">
                <div class="upload-icon">📁</div>
                <h2>Glissez votre fichier CSV ici</h2>
                <p>ou</p>
                <button class="upload-btn" id="upload-btn">Uploader un fichier</button>
                <input type="file" id="file-input" accept=".csv" style="display: none;">
                <div class="upload-info">
                    <small>Formats acceptés: CSV • Taille max: 10MB</small>
                </div>
            </div>

            <!-- Loading -->
            <div class="loading-area" id="loading-area" style="display: none;">
                <div class="loading-spinner"></div>
                <h3>Traitement en cours...</h3>
                <p>Nettoyage de vos données</p>
            </div>

            <!-- Results -->
            <div class="results-area" id="results-area" style="display: none;">
                
                <!-- Stats -->
                <div class="stats-section">
                    <h2>Résultats</h2>
                    <div class="stats-cards">
                        <div class="stat-card">
                            <div class="stat-number" id="total-lines">0</div>
                            <div class="stat-label">Lignes totales</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" id="cleaned-lines">0</div>
                            <div class="stat-label">Lignes nettoyées</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" id="duplicates-removed">0</div>
                            <div class="stat-label">Doublons supprimés</div>
                        </div>
                    </div>
                </div>

                <!-- Chart -->
                <div class="chart-section">
                    <h2>Graphique</h2>
                    <div class="chart-controls">
                        <select id="x-axis">
                            <option value="">Axe X...</option>
                        </select>
                        <select id="y-axis">
                            <option value="">Axe Y...</option>
                        </select>
                        <button id="generate-chart" class="chart-btn">Générer</button>
                    </div>
                    <div class="chart-container">
                        <canvas id="data-chart"></canvas>
                    </div>
                    <div id="chart-status" class="chart-status">Sélectionnez les axes pour créer un graphique</div>
                </div>

                <!-- Data Preview -->
                <div class="preview-section">
                    <h2>Aperçu des données</h2>
                    <div class="table-wrapper">
                        <table id="data-table">
                            <thead id="table-header"></thead>
                            <tbody id="table-body"></tbody>
                        </table>
                    </div>
                    <div id="preview-status" class="preview-status"></div>
                </div>

                <!-- Download -->
                <div class="download-section">
                    <a id="download-btn" class="download-btn" style="display: none;">
                        💾 Télécharger le fichier nettoyé
                    </a>
                    <button id="new-file-btn" class="new-file-btn">Nouveau fichier</button>
                </div>
            </div>
        </main>
    </div>

    <script src="app.js"></script>
</body>
</html>