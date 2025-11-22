<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Database/db.php'; 
require_once __DIR__ . '/../Database/userRepository.php'; 

class FacturationController {
    
    public function handleRequest() {

        require_once __DIR__ . '/../Views/chef/shared/header_chef.php';
        require __DIR__ . '/../Views/chef/facturation/facturation.php';
    }
}
