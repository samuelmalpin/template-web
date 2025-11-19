<?php
// Import de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'config_email.php';

header('Content-Type: application/json');

// Vérification de la méthode POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupération et nettoyage des données
$name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
$email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
$type = isset($_POST['type']) ? htmlspecialchars(trim($_POST['type'])) : '';
$message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

// Validation des champs
if (empty($name) || empty($email) || empty($type) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires']);
    exit;
}

// Validation de l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email invalide']);
    exit;
}

// Création de l'instance PHPMailer
$mail = new PHPMailer(true);

try {
    // Configuration du serveur SMTP
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USERNAME;
    $mail->Password   = SMTP_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;
    $mail->CharSet    = 'UTF-8';

    // Destinataires
    $mail->setFrom(SMTP_USERNAME, EMAIL_FROM_NAME);
    $mail->addAddress(EMAIL_TO);
    $mail->addReplyTo($email, $name);

    // Contenu de l'email
    $mail->isHTML(false);
    $mail->Subject = "Nouveau contact - " . $type;
    $mail->Body    = "Nouveau message depuis votre site portfolio\n\n" .
                     "Nom: " . $name . "\n" .
                     "Email: " . $email . "\n" .
                     "Type de projet: " . $type . "\n\n" .
                     "Message:\n" . $message . "\n\n" .
                     "---\n" .
                     "Envoyé le " . date('d/m/Y à H:i:s');

    // Envoi de l'email
    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Message envoyé avec succès']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'envoi: ' . $mail->ErrorInfo]);
}
?>
