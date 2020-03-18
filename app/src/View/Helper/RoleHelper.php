<?php
namespace App\View\Helper;

class RoleHelper extends AppHelper {
    
    public function isAdmin() 
    {
        $admin = $this->Session->read('data');
        if ($admin['role'] == 0) {
            return true;
        } else {
            return false;
        }

    }
}
?>