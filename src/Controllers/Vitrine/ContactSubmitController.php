<?php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Utils/mailer.php';

require_once __DIR__ . '/../../Database/db.php';
require_once __DIR__ . '/../../Database/notificationsRepository.php';

class ContactSubmitController
{
    public function handleRequest(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $redirect = BASE_URL . '/public/index.php?page=home#main-contact';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $redirect);
            exit;
        }

        $email   = trim($_POST['email'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // Validation minimale
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
            $_SESSION['contact_error'] = "Veuillez renseigner un email valide et un message.";
            header('Location: ' . $redirect);
            exit;
        }

        $to = CONTACT_RECEIVER_EMAIL;
        $subject = "Nouveau message formulaire de contact - Team Jardin";

        $text =
            "Nouveau message via le formulaire de contact\n\n" .
            "Email : {$email}\n" .
            "Téléphone : " . ($phone !== '' ? $phone : '(non renseigné)') . "\n\n" .
            "Message :\n{$message}\n";

        $ok = sendMailViaMailgun($to, $subject, $text, $email);

        if ($ok) {
            $_SESSION['contact_success'] = "Merci ! Votre message a bien été envoyé. Nous vous recontacterons rapidement.";

            //notification admin
            $pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);

            $adminIds = user_getAllAdminIds($pdo);

            if (!empty($adminIds)) {

                $notifMessage =
                    "Nouveau contact reçu.\n" .
                    "Email : {$email}\n" .
                    "Téléphone : " . ($phone !== '' ? $phone : '(non renseigné)') . "\n\n" .
                    "Message :\n{$message}";

                foreach ($adminIds as $adminId) {
                    notif_create(
                        $pdo,
                        (int)$adminId,
                        null,
                        "Nouveau message de contact",
                        $notifMessage,
                        "info",
                        BASE_URL . "/public/index.php?page=chef/notifications"
                    );
                }
            }
            //


        } else {
            $_SESSION['contact_error'] = "Une erreur est survenue lors de l’envoi. Veuillez réessayer.";
        }

        header('Location: ' . $redirect);
        exit;
    }
}
