<?php

App::uses('Helper', 'View');

class CommonHelper extends AppHelper {

    public $helpers = array('Session');
    
    public function isRole($role_key) {
        $role = $this->Session->read('role');

        if (intval($role) === 0) {
            $res = 'develop';
        }
        elseif ($role < 10) {
            $res = 'admin';
        } 
        if (in_array($res, (array)$role_key)) {
            return true;
        } else {
            return false;
        }
    }
}
