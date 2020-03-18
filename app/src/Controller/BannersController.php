<?php
namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Utility\Inflector;

class BannersController extends AppController
{

    public function beforeFilter(EventInterface $event) 
    {
        $this->modelName = Inflector::camelize($this->modelClass);
        $this->set('ModelName', $this->modelName);
        parent::beforeFilter($event);
        
    }

    public function index()
    {
        // $cond = ['id >' => 3];
        $cond = [];
        return parent::_lists($cond, [
            'order' => [
                $this->{$this->modelName}->aliasField('position') => 'desc'
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

    // public function file($id, $columns)
    // {
    //     // Idが存在かどうかをチェック
    //     $this->{$this->modelName}->exitsId($id);
    //     return parent::_file($id, $columns);
    // }

    public function edit($id = 0)
    {   
        $options = [];
        $options['redirect'] = ['controller' => $this->modelName, 'action' => 'index'];
        // $options['createMany'] = ['image2' => ''];
        // $options['saveMany'] = ['position' => 1];

        // Idが存在かどうかをチェック
        $this->{$this->modelName}->exitsId($id);
        return parent::_edit($id, $options);
    }



}

?>