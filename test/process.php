<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Configuration
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Vérification de l'upload
if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'Erreur lors de l\'upload du fichier']);
    exit;
}

$file = $_FILES['csv_file'];

// Vérifications
if ($file['size'] > MAX_FILE_SIZE) {
    echo json_encode(['error' => 'Fichier trop volumineux (max 10MB)']);
    exit;
}

if (!in_array(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)), ['csv'])) {
    echo json_encode(['error' => 'Seuls les fichiers CSV sont autorisés']);
    exit;
}

// Génération d'un ID unique
$unique_id = uniqid() . '_' . time();
$raw_file = 'uploads/raw_' . $unique_id . '.csv';
$clean_file = 'uploads/clean_' . $unique_id . '.csv';

// Sauvegarde du fichier brut
if (!move_uploaded_file($file['tmp_name'], $raw_file)) {
    echo json_encode(['error' => 'Impossible de sauvegarder le fichier']);
    exit;
}

// Récupération des options de nettoyage
$options = [
    'trim_spaces' => isset($_POST['trim_spaces']) && $_POST['trim_spaces'] === 'true',
    'fix_decimals' => isset($_POST['fix_decimals']) && $_POST['fix_decimals'] === 'true',
    'remove_duplicates' => isset($_POST['remove_duplicates']) && $_POST['remove_duplicates'] === 'true',
    'normalize_dates' => isset($_POST['normalize_dates']) && $_POST['normalize_dates'] === 'true'
];

// Fonction de nettoyage des données CSV
function cleanCsvData($input_file, $output_file, $options = []) {
    $handle = fopen($input_file, 'r');
    if (!$handle) {
        throw new Exception('Impossible de lire le fichier.');
    }

    $output = fopen($output_file, 'w');
    if (!$output) {
        fclose($handle);
        throw new Exception('Impossible de créer le fichier nettoyé.');
    }

    $line_count = 0;
    $cleaned_count = 0;
    $seen_rows = [];
    $headers = null;

    // Détection automatique du délimiteur
    $first_line = fgets($handle);
    rewind($handle);
    
    $delimiter = ',';
    if (substr_count($first_line, ';') > substr_count($first_line, ',')) {
        $delimiter = ';';
    }

    while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
        $line_count++;
        
        // Première ligne = en-têtes
        if ($line_count === 1) {
            $headers = array_map('trim', $data);
            fputcsv($output, $headers);
            continue;
        }

        // Nettoyage des données
        $cleaned_data = [];
        foreach ($data as $index => $value) {
            // Trim des espaces si activé
            if ($options['trim_spaces']) {
                $value = trim($value);
            }
            
            // Remplacement virgule par point pour les nombres si activé
            if ($options['fix_decimals'] && is_numeric(str_replace(',', '.', $value))) {
                $value = str_replace(',', '.', $value);
            }
            
            // Nettoyage des dates si activé
            if ($options['normalize_dates'] && preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $matches)) {
                $value = sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
            }
            
            $cleaned_data[] = $value;
        }

        // Détection des doublons si activée
        if ($options['remove_duplicates']) {
            $row_hash = md5(implode('|', $cleaned_data));
            if (!in_array($row_hash, $seen_rows)) {
                $seen_rows[] = $row_hash;
                fputcsv($output, $cleaned_data);
                $cleaned_count++;
            }
        } else {
            fputcsv($output, $cleaned_data);
            $cleaned_count++;
        }
    }

    fclose($handle);
    fclose($output);

    return [
        'total_lines' => $line_count - 1, // -1 pour exclure l'en-tête
        'cleaned_lines' => $cleaned_count,
        'duplicates_removed' => ($line_count - 1) - $cleaned_count,
        'headers' => $headers
    ];
}

// Fonction de nettoyage automatique des anciens fichiers
function cleanupOldFiles() {
    $upload_dir = 'uploads/';
    $files = glob($upload_dir . '*');
    $cutoff_time = time() - (24 * 60 * 60); // 24 heures

    foreach ($files as $file) {
        if (is_file($file) && filemtime($file) < $cutoff_time && basename($file) !== '.htaccess') {
            unlink($file);
        }
    }
}

try {
    // Nettoyage des anciens fichiers
    cleanupOldFiles();
    
    // Traitement du fichier
    $stats = cleanCsvData($raw_file, $clean_file, $options);
    
    // Lecture du fichier nettoyé pour l'aperçu
    $preview_data = [];
    $handle = fopen($clean_file, 'r');
    $line_count = 0;
    while (($data = fgetcsv($handle)) !== FALSE && $line_count < 10) {
        $preview_data[] = $data;
        $line_count++;
    }
    fclose($handle);
    
    // Réponse JSON
    echo json_encode([
        'success' => true,
        'file_id' => $unique_id,
        'stats' => $stats,
        'preview' => $preview_data,
        'download_url' => 'uploads/clean_' . $unique_id . '.csv'
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

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
?>