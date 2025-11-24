<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Database/db.php'; 
require_once __DIR__ . '/../Database/userRepository.php'; 

class DashboardController {
    
    public function handleRequest() {

        require_once __DIR__ . '/../Views/chef/shared/header_chef.php';
        require_once __DIR__ . '/../Views/chef/dashboard/dashboard.php';
    }
}
