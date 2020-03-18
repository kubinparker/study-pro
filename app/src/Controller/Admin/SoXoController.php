<?php
    namespace App\Controller\Admin;

    use App\Controller\AppController;

    use Cake\Event\EventInterface;
    use Cake\Http\Exception\NotFoundException;

    class SoXoController extends AppController
    {
        public function beforeFilter(EventInterface $event) 
        {
            $this->checkLogin();
            // call function of parent => load layout admin
            parent::beforeFilter($event);
        }

        public function index()
        {
            $this->set('result', [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30]);
            $this->set('data', [$this->modelName => []]);
        }
    }