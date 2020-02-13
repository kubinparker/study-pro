<?php
class UserController extends AppController
{
    public function beforeFilter()
    {
        $this->modelName = Inflector::camelize($this->modelKey);
        $this->set('ModelName', $this->modelName);
        $this->set('Size', $this->size);
        parent::beforeFilter();
        $this->check_login();
    }


    public function admin_index(){
        return parent::_lists([], ['order' => 'role ASC']);
    }

    public function admin_edit($id = 0){
        parent::_edit($id);
    }
}

?>