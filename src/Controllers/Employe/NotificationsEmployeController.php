<?php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Database/db.php';
require_once __DIR__ . '/../../Database/notificationsRepository.php';

class NotificationsEmployeController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    private function checkSalarie(): void
    {
        if (empty($_SESSION['user']) || ($_SESSION['role'] ?? '') !== 'salarie') {
            http_response_code(403);
            exit("Accès réservé aux employés.");
        }
    }

    /** GET: liste */
    public function index(): void
    {
        $this->checkSalarie();

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $notifications = notif_getLatest($this->pdo, $userId, 30);
        $nbNotifs = count(notif_getUnread($this->pdo, $userId)); // badge header

        $pageTitle = "Mes notifications – Team Jardin";

        require __DIR__ . '/../../Views/employe/notifications.php';
    }

    /** POST: marquer une notif comme lue */
    public function read(): void
    {
        $this->checkSalarie();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/public/index.php?page=employe/notifications');
            exit;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $id = (int)($_POST['id_notification'] ?? 0);

        if ($id > 0) {
            notif_markRead($this->pdo, $id, $userId);
        }

        header('Location: ' . BASE_URL . '/public/index.php?page=employe/notifications');
        exit;
    }

    /** POST: tout marquer comme lu */
    public function readAll(): void
    {
        $this->checkSalarie();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/public/index.php?page=employe/notifications');
            exit;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        notif_markAllRead($this->pdo, $userId);

        header('Location: ' . BASE_URL . '/public/index.php?page=employe/notifications');
        exit;
    }
}
