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
            $publicPath = $this->handleAvatarBase64($userId, $role);


            
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

    private function handleAvatarBase64(int $userId, string $prefix): string
    {
        if (empty($_POST['croppedImage'])) {
            throw new RuntimeException("Image non fournie.");
        }

        $data = $_POST['croppedImage'];

        if (!preg_match('#^data:image/(png|jpeg|webp);base64,#', $data, $m)) {
            throw new RuntimeException("Format image invalide.");
        }

        $ext = $m[1] === 'jpeg' ? 'jpg' : $m[1];

        $data = substr($data, strpos($data, ',') + 1);
        $binary = base64_decode($data);

        if ($binary === false) {
            throw new RuntimeException("Décodage image impossible.");
        }

        $dirAbs = __DIR__ . '/../../../public/assets/uploads/avatars';
        if (!is_dir($dirAbs)) {
            mkdir($dirAbs, 0775, true);
        }

        $filename = $prefix . '_' . $userId . '.' . $ext;
        $destAbs  = $dirAbs . '/' . $filename;

        
        foreach (['jpg','png','webp'] as $e) {
            $old = $dirAbs . '/' . $prefix . '_' . $userId . '.' . $e;
            if ($old !== $destAbs && file_exists($old)) unlink($old);
        }

        file_put_contents($destAbs, $binary);

        return '/public/assets/uploads/avatars/' . $filename;
    }

}
