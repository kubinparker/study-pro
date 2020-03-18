<?php 
namespace App\View\Helper;

class CommonHelper extends AppHelper {    

    public function isRole($role_key) {
        $role = $this->Session->read('role');

        if (intval($role) === 0) {
            $res = 'develop';
        }
        elseif ($role < 10) {
            $res = 'admin';
        } 
        /** 必要に応じて追加 */
        // elseif ($role < 20) {
        //     $res = 'manager';
        // } else {
        //     $res = 'staff';
        // }

        if (in_array($res, (array)$role_key)) {
            return true;
        } else {
            return false;
        }
    }
}
?>