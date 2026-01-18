<?php

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Database/db.php';
require_once __DIR__ . '/../../../Database/notificationsRepository.php';

class NotificationsChefController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    private function checkAdmin(): void
    {
        if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit("Accès réservé au chef d'entreprise.");
        }
    }

    public function index(): void
    {
        $this->checkAdmin();

        $userId = (int)$_SESSION['user_id'];
        $notifications = notif_getLatest($this->pdo, $userId, 50);
        $nbNotifs = count(notif_getUnread($this->pdo, $userId));

        $pageTitle = "Notifications – Chef";

        require __DIR__ . '/../../../Views/chef/notifications_chef.php';
    }

    public function read(): void
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id_notification'] ?? 0);
            if ($id > 0) {
                notif_markRead($this->pdo, $id, (int)$_SESSION['user_id']);
            }
        }

        header("Location: " . BASE_URL . "/public/index.php?page=chef/notifications");
        exit;
    }

    public function readAll(): void
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            notif_markAllRead($this->pdo, (int)$_SESSION['user_id']);
        }

        header("Location: " . BASE_URL . "/public/index.php?page=chef/notifications");
        exit;
    }
}
