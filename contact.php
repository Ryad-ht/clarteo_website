<?php
// contact.php — Envoi d'email pour Clartéo

mb_internal_encoding('UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit;
}

// honeypot : si rempli => bot
if (!empty($_POST['website'] ?? '')) {
  header('Location: merci.html');
  exit;
}

$name    = trim((string)($_POST['name'] ?? ''));
$email   = trim((string)($_POST['email'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

if ($name === '' || $email === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(400);
  exit('Champs invalides.');
}

// destinataire + en-têtes
$to   = 'contact@clarteo.com';             // ✅ maintenant que ta boîte pro existe
$from = 'contact@clarteo.com';             // expéditeur = ton domaine (meilleure délivrabilité)
$subj = 'Nouveau message – Formulaire Clartéo';

// corps texte
$ip   = $_SERVER['REMOTE_ADDR'] ?? '';
$body = "Nom : $name\r\nEmail : $email\r\nIP : $ip\r\n----------------------------------------\r\n$message";

// headers
$headers = [
  'MIME-Version: 1.0',
  'Content-Type: text/plain; charset=UTF-8',
  'Content-Transfer-Encoding: 8bit',
  'From: Clartéo <'.$from.'>',
  'Reply-To: '.$email,
  'X-Mailer: PHP/'.phpversion()
];

@mail($to, '=?UTF-8?B?'.base64_encode($subj).'?=', $body, implode("\r\n", $headers));

// page de remerciement
header('Location: merci.html');
exit;
