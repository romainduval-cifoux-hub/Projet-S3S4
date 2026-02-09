<?php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Database/db.php';
require_once __DIR__ . '/../../Database/avatarRepository.php';

class AvatarController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    public function upload(): void
    {
        if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
            http_response_code(403);
            exit("Accès refusé.");
        }

        $userId = (int)$_SESSION['user_id'];
        $role   = (string)$_SESSION['role']; 

        $redirect = $_POST['redirect'] ?? ($_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/public/index.php'));
        if (!is_string($redirect) || $redirect === '') {
            $redirect = BASE_URL . '/public/index.php';
        }

        
        if (!in_array($role, ['client', 'salarie'], true)) {
            $_SESSION['flash_error'] = "Rôle non autorisé pour l’avatar.";
            header('Location: ' . $redirect);
            exit;
        }

        try {
            $publicPath = $this->handleAvatarUpload($userId, $role);

            
            $ok = false;
            if ($role === 'client') {
                $ok = avatar_updateForClient($this->pdo, $userId, $publicPath);
            } else { 
                $ok = avatar_updateForSalarie($this->pdo, $userId, $publicPath);
            }

            if (!$ok) {
                throw new RuntimeException("Upload ok, mais impossible de mettre à jour la BDD.");
            }

            $_SESSION['flash_success'] = "Photo de profil mise à jour.";
        } catch (Throwable $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        header('Location: ' . $redirect);
        exit;
    }

    private function handleAvatarUpload(int $userId, string $prefix): string
    {
        if (empty($_FILES['avatar']) || $_FILES['avatar']['error'] === UPLOAD_ERR_NO_FILE) {
            throw new RuntimeException("Aucun fichier sélectionné.");
        }
        if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException("Erreur upload.");
        }
        if ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
            throw new RuntimeException("Fichier trop volumineux (max 2 Mo).");
        }

        $tmp = $_FILES['avatar']['tmp_name'];

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($tmp);

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($allowed[$mime])) {
            throw new RuntimeException("Format non autorisé (jpg, png, webp).");
        }
        $ext = $allowed[$mime];

        
        $dirAbs = __DIR__ . '/../../../public/uploads/avatars';
        if (!is_dir($dirAbs)) {
            mkdir($dirAbs, 0775, true);
        }

        $filename = $prefix . '_' . $userId . '.' . $ext; 
        $destAbs  = $dirAbs . '/' . $filename;

       
        foreach (['jpg','png','webp'] as $e) {
            $old = $dirAbs . '/' . $prefix . '_' . $userId . '.' . $e;
            if ($old !== $destAbs && file_exists($old)) unlink($old);
        }

        if (!move_uploaded_file($tmp, $destAbs)) {
            throw new RuntimeException("Impossible d'enregistrer le fichier.");
        }

        
        return '/public/uploads/avatars/' . $filename;
    }
}
