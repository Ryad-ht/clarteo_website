// Clartéo Lab - Application simple
let currentChart = null;
let csvData = [];
let headers = [];

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
});

function setupEventListeners() {
    const uploadArea = document.getElementById('upload-area');
    const uploadBtn = document.getElementById('upload-btn');
    const fileInput = document.getElementById('file-input');
    
    // Drag & Drop
    uploadArea.addEventListener('dragover', handleDragOver);
    uploadArea.addEventListener('dragleave', handleDragLeave);
    uploadArea.addEventListener('drop', handleDrop);
    uploadArea.addEventListener('click', () => fileInput.click());
    
    // File input
    uploadBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        fileInput.click();
    });
    fileInput.addEventListener('change', handleFileSelect);
    
    // Chart controls
    document.getElementById('generate-chart').addEventListener('click', generateChart);
    
    // New file button
    document.getElementById('new-file-btn').addEventListener('click', resetApp);
}

function handleDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('dragover');
}

function handleDragLeave(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('dragover');
}

function handleDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        processFile(files[0]);
    }
}

function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file) {
        processFile(file);
    }
}

async function processFile(file) {
    // Validation
    if (!file.name.toLowerCase().endsWith('.csv')) {
        alert('Seuls les fichiers CSV sont acceptés');
        return;
    }
    
    if (file.size > 10 * 1024 * 1024) {
        alert('Le fichier est trop volumineux (max 10MB)');
        return;
    }
    
    // Afficher loading
    showSection('loading');
    
    // Préparer les données
    const formData = new FormData();
    formData.append('csv_file', file);
    formData.append('trim_spaces', 'true');
    formData.append('fix_decimals', 'true');
    formData.append('remove_duplicates', 'true');
    formData.append('normalize_dates', 'true');
    
    try {
        const response = await fetch('process.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Afficher les stats
            displayStats(result.stats);
            
            // Charger les données pour le dashboard
            await loadCsvData(result.download_url);
            
            // Afficher l'aperçu
            displayPreview(result.preview);
            
            // Configurer le téléchargement
            setupDownload(result.download_url);
            
            // Afficher les résultats
            showSection('results');
        } else {
            throw new Error(result.error || 'Erreur inconnue');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors du traitement: ' + error.message);
        showSection('upload');
    }
}

function showSection(section) {
    document.getElementById('upload-area').style.display = section === 'upload' ? 'block' : 'none';
    document.getElementById('loading-area').style.display = section === 'loading' ? 'block' : 'none';
    document.getElementById('results-area').style.display = section === 'results' ? 'block' : 'none';
}

function displayStats(stats) {
    document.getElementById('total-lines').textContent = stats.total_lines || '0';
    document.getElementById('cleaned-lines').textContent = stats.cleaned_lines || '0';
    document.getElementById('duplicates-removed').textContent = stats.duplicates_removed || '0';
}

async function loadCsvData(csvUrl) {
    try {
        const response = await fetch(csvUrl);
        const text = await response.text();
        const lines = text.trim().split('\n');
        
        if (lines.length === 0) return;

        // Parse CSV
        headers = parseCSVLine(lines[0]);
        csvData = [];

        for (let i = 1; i < lines.length; i++) {
            const values = parseCSVLine(lines[i]);
            if (values.length === headers.length) {
                const row = {};
                headers.forEach((header, index) => {
                    row[header] = values[index];
                });
                csvData.push(row);
            }
        }

        // Remplir les sélecteurs
        populateSelectors();
        
    } catch (error) {
        console.error('Erreur chargement CSV:', error);
    }
}

function parseCSVLine(line) {
    const result = [];
    let current = '';
    let inQuotes = false;
    
    for (let i = 0; i < line.length; i++) {
        const char = line[i];
        
        if (char === '"') {
            inQuotes = !inQuotes;
        } else if (char === ',' && !inQuotes) {
            result.push(current.trim());
            current = '';
        } else {
            current += char;
        }
    }
    
    result.push(current.trim());
    return result;
}

function populateSelectors() {
    const xSelect = document.getElementById('x-axis');
    const ySelect = document.getElementById('y-axis');
    
    xSelect.innerHTML = '<option value="">Axe X...</option>';
    ySelect.innerHTML = '<option value="">Axe Y...</option>';
    
    headers.forEach(header => {
        xSelect.add(new Option(header, header));
        ySelect.add(new Option(header, header));
    });
}

function displayPreview(previewData) {
    const tableHeader = document.getElementById('table-header');
    const tableBody = document.getElementById('table-body');
    const previewStatus = document.getElementById('preview-status');
    
    if (!previewData || previewData.length === 0) {
        previewStatus.textContent = 'Aucune donnée à afficher';
        return;
    }
    
    // En-têtes
    const headers = previewData[0];
    tableHeader.innerHTML = '<tr>' + headers.map(h => `<th>${h}</th>`).join('') + '</tr>';
    
    // Données
    const dataRows = previewData.slice(1);
    tableBody.innerHTML = dataRows.map(row => 
        '<tr>' + row.map(cell => `<td>${cell || ''}</td>`).join('') + '</tr>'
    ).join('');
    
    previewStatus.textContent = `Aperçu de ${dataRows.length} lignes`;
}

function setupDownload(downloadUrl) {
    const downloadBtn = document.getElementById('download-btn');
    downloadBtn.href = downloadUrl;
    downloadBtn.download = `clarteo_cleaned_${new Date().toISOString().split('T')[0]}.csv`;
    downloadBtn.style.display = 'inline-block';
}

function generateChart() {
    const xColumn = document.getElementById('x-axis').value;
    const yColumn = document.getElementById('y-axis').value;
    
    if (!xColumn || !yColumn) {
        document.getElementById('chart-status').textContent = 
            'Sélectionnez les deux axes pour générer le graphique';
        return;
    }

    try {
        // Filtrer les données valides
        let validData = csvData.filter(row => 
            row[xColumn] && row[yColumn] && 
            !isNaN(parseFloat(row[yColumn]))
        );

        if (validData.length === 0) {
            document.getElementById('chart-status').textContent = 
                'Aucune donnée numérique valide trouvée';
            return;
        }

        // Trier par date si possible
        validData.sort((a, b) => {
            const dateA = new Date(a[xColumn]);
            const dateB = new Date(b[xColumn]);
            if (!isNaN(dateA) && !isNaN(dateB)) {
                return dateA - dateB;
            }
            return 0;
        });

        // Limiter à 50 points
        if (validData.length > 50) {
            const step = Math.ceil(validData.length / 50);
            validData = validData.filter((_, index) => index % step === 0);
        }

        // Préparer les données
        const chartData = {
            labels: validData.map(row => row[xColumn]),
            datasets: [{
                label: yColumn,
                data: validData.map(row => parseFloat(row[yColumn])),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        };

        // Détruire l'ancien graphique
        if (currentChart) {
            currentChart.destroy();
        }

        // Créer le nouveau graphique
        const ctx = document.getElementById('data-chart').getContext('2d');
        currentChart = new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#f1f5f9' }
                    }
                },
                scales: {
                    x: {
                        ticks: { 
                            color: '#94a3b8',
                            maxTicksLimit: 8
                        },
                        grid: { color: 'rgba(148, 163, 184, 0.2)' }
                    },
                    y: {
                        ticks: { color: '#94a3b8' },
                        grid: { color: 'rgba(148, 163, 184, 0.2)' }
                    }
                }
            }
        });

        document.getElementById('chart-status').textContent = 
            `Graphique avec ${validData.length} points`;

    } catch (error) {
        console.error('Erreur graphique:', error);
        document.getElementById('chart-status').textContent = 
            'Erreur lors de la création du graphique';
    }
}

function resetApp() {
    // Reset interface
    document.getElementById('file-input').value = '';
    csvData = [];
    headers = [];
    
    // Détruire le graphique
    if (currentChart) {
        currentChart.destroy();
        currentChart = null;
    }
    
    // Retour à l'upload
    showSection('upload');
}

// Gestion des erreurs
window.addEventListener('error', function(e) {
    console.error('Erreur:', e.error);
});