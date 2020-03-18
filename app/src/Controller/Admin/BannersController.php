<?php
    namespace App\Controller\Admin;

    use App\Controller\AppController;

    use Cake\Event\EventInterface;
    use Cake\Http\Exception\NotFoundException;

    class BannersController extends AppController
    {
        public $size = [1 => '大', 2 => '中', 3 => '小'];
        public function beforeFilter(EventInterface $event) 
        {
            $this->checkLogin();
            $this->set('Size', $this->size);
            // call function of parent => load layout admin
            parent::beforeFilter($event);
        }

        public function index()
        {
            $cond = [];
            return parent::_lists($cond, [
                'order' => [
                    $this->{$this->modelName}->aliasField('position') => 'asc'
                ],
                'limit' => null
            ]);
        }
        
        public function delete($id = 0, $type = null, $columns = null)
        { 
            if (empty($type)) {
                throw new NotFoundException(__('Type of delete not defined'));
            }
            // Idが存在かどうかをチェック
            $this->{$this->modelName}->exitsId($id);
            return parent::_delete($id, $type, $columns);
        }

        public function view($id = 0)
        {   
            $id = $id == 0 ? $this->request->getParam('id') : $id;
            // Idが存在かどうかをチェック
            $this->{$this->modelName}->exitsId($id);
            return parent::_detail($id);
        }

        public function enable($id)
        {
            // Idが存在かどうかをチェック
            $this->{$this->modelName}->exitsId($id);
            return parent::_enable($id);
        }

        public function position($id, $pos)
        {
            // Idが存在かどうかをチェック
            $this->{$this->modelName}->exitsId($id);
            return parent::_position($id, $pos);
        }

        public function file($id = 0, $columns = null)
        {
            // Idが存在かどうかをチェック
            $this->{$this->modelName}->exitsId($id);
            return parent::file($id, $columns);
        }

        public function edit($id = 0)
        {   
            $options = [];
            $options['redirect'] = ['controller' => $this->modelName, 'action' => 'index'];
            // Idが存在かどうかをチェック
            $this->{$this->modelName}->exitsId($id);
            return parent::_edit($id, $options);
        }
    }