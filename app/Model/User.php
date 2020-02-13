<?php
class User extends AppModel
{
    public function hash($password)
    {
        return hash('sha256', $password);
    }
}
?>