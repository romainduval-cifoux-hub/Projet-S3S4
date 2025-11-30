<?php

require_once __DIR__ . '/../../config.php';

class LogoutController
{
    public function handleRequest(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        session_destroy();

        header("Location: " . BASE_URL . "/public/index.php?page=home");
        exit();
    }
}
